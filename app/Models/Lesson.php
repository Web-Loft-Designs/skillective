<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Illuminate\Support\Str;
use App\Notifications\StudentBookingConfirmation;
use App\Notifications\InstructorNewBookingNotification;
use App\Models\Booking;
use App\Models\LessonRequest;
use App\Models\User;
use Auth;
use Log;
use App\Facades\UserRegistrator;
use App\Http\Requests\API\StudentRegisterRequest;
use MaksimM\SubqueryMagic\SubqueryMagic;
use Carbon\Carbon;
use App\Facades\BraintreeProcessor;

class Lesson extends Model implements Transformable
{
	use SoftDeletes, SubqueryMagic;

	public $table = 'lessons';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	const TIME_INTERVAL = 15;

	const VIRTUAL_LESSON_EXTRA_TIME_BEFORE_START = 5;
	const VIRTUAL_LESSON_EXTRA_TIME_AFTER_END = 1;

	protected $dates = ['deleted_at'];


	public $fillable = [
		'instructor_id',
		'genre_id',
		'start',
		'end',
		'spots_count',
		'spot_price',
		'location',
		'address',
		'description',
		'city',
		'state',
		'zip',
		'lat',
		'lng',
		'is_cancelled',
		'timezone_id',
		'timezone_offset_gmt',
		'lesson_type',
		'room_sid',
		'room_completed',
		'private_for_student_id',
		'count_places_in_spot'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'instructor_id' => 'integer',
		'genre_id' => 'integer',
		'start' => 'datetime',
		'end' => 'datetime',
		'spots_count' => 'integer',
		'spot_price' => 'float',
		'location' => 'string',
		'address' => 'string',
		'city' => 'string',
		'state' => 'string',
		'zip' => 'string',
		'is_cancelled' => 'boolean',
		'lat' => 'float',
		'lng' => 'float',
		'description' => 'string',
		'timezone_id' => 'string'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'instructor_id' => 'required',
		'genre_id' => 'required',
		'start' => 'required',
		'end' => 'required',
		'spots_count' => 'required',
		'spot_price' => 'required',
		'location' => 'required',
		//        'address' => 'required',
		//        'city' => 'required',
		//        'state' => 'required',
		//        'zip' => 'required'
	];

	protected $hidden = [
		'created_at', 'updated_at', 'deleted_at'
	];

	public static function getLessonTypes()
	{
		return [
			'in_person' => 'In Person Location (Instructor Determined)',
			'virtual' => 'Virtual',
			'in_person_client' => 'In Person Location (Client Determined)'
		];
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 **/
	public function genre()
	{
		return $this->belongsTo(\App\Models\Genre::class, 'genre_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 **/
	public function instructor()
	{
		return $this->belongsTo(\App\Models\User::class, 'instructor_id');
	}

	public function roomChatMessages()
	{
		return $this->hasMany('App\Models\RoomChatMessage', 'lesson_id', 'id');
	}

	public function regularNotifications()
	{
		return $this->hasMany(\App\Models\RegularNotification::class, 'user_regular_notifications');
	}

	public function bookings()
	{
		return $this->hasMany('App\Models\Booking', 'lesson_id', 'id');
	}

	public function students()
	{
		return $this->hasManyThrough('App\Models\User', 'App\Models\Booking', 'lesson_id', 'id', 'id', 'student_id')
			->whereNotIn('bookings.status', [Booking::STATUS_CANCELLED, Booking::STATUS_PENDING]);
	}

	public function transform()
	{
		return [
			'id' => (int)$this->id,
			'instructor_id' => $this->instructor_id,
			'genre_id' => $this->genre_id,
			'genre' => $this->genre->transform(),
			'instructor' => $this->instructor->transform(),
			//			'students' => $this->students->toArray(),
			'start' => $this->start->format('Y-m-d H:i:s'),
			'end' => $this->end->format('Y-m-d H:i:s'),
			'timezone_id' => getTimezoneAbbrev($this->timezone_id),
			'timezone_offset_gmt' => $this->timezone_offset_gmt,
			'spots_count' => $this->spots_count,
			'spot_price' => number_format((float)$this->spot_price, 2, '.', ''),
			'location' => $this->location,
			'lat' => $this->lat,
			'lng' => $this->lng,
			'city' => $this->city,
			'state' => $this->state,
			'address' => $this->address,
			'zip' => $this->zip,
			'description' => $this->description,
			'count_booked' => isset($this->count_booked) ? $this->count_booked : null,
			'lesson_type' => $this->lesson_type,
			'room_sid' => $this->room_sid,
			'room_completed' => $this->room_completed,
		];
	}

	public function isBookableNowByCurrentUser()
	{
		return (currentUserCanBook()
			&&
			$this->getCountFreeSpots() > 0
			&&
			!$this->is_cancelled
			&&
			!$this->alreadyStarted()
			&&
			$this->instructor->status == User::STATUS_ACTIVE
			&&
			($this->private_for_student_id == null || (Auth::user() && $this->private_for_student_id == Auth::id())));
	}

	public function getCountBookedSpots()
	{
		return $this->bookings()->where('status', '<>', Booking::STATUS_CANCELLED)->count();
	}

	public function getCountFreeSpots()
	{
		return $this->spots_count - $this->getCountBookedSpots();
	}

	public function alreadyStarted()
	{
		return strtotime($this->start->format('Y-m-d H:i:s')) < strtotime(Carbon::now($this->timezone_id)->format('Y-m-d H:i:s')); // compare lesson datetime with current time on lesson location
	}

	public function book($user_repository, $request, $paymentMethodNonce, $student)
	{
		
		if(Auth::user() != null){
			$user_repository->updateUserData(Auth::user()->id, $request);
		}
		

		if ($paymentMethodNonce) {
			$device_data = $request->input('device_data', null);
			$paymentMethod = BraintreeProcessor::createPaymentMethod($student, $paymentMethodNonce, $device_data);
		} else {
			$paymentMethodToken = $request->input('payment_method_token', null);
			$paymentMethod = BraintreeProcessor::findPaymentMethod($paymentMethodToken);

			if (!$paymentMethod || $paymentMethod->customerId != $student->braintree_customer_id) {
				Log::error("Student {$student->id} booking : Payment method not defined");
				throw new \Exception('Payment method not defined');
			}
			$paymentMethod = ['token' => $paymentMethod->token, 'type' => BraintreeProcessor::_getPaymentMethodType($paymentMethod)];
		}

		if (!$paymentMethod) {
			throw new \Exception('Payment method not found');
		}
		
		$booking = new Booking();
		$booking->lesson_id = $request->input('lesson_id', '');;
		$booking->special_request	= $request->input('special_request', '');
		$booking->spot_price		= $this->spot_price;
		$booking->instructor_id		= $this->instructor_id;
		$booking->student_id		= $student->id;
		$booking->status			= Booking::STATUS_PENDING;
		$booking->payment_method_token	= $paymentMethod['token'];
		$booking->payment_method_type	= $paymentMethod['type'];

	
		$service_fee = $booking->getBookingServiceFeeAmount($this->spot_price);
		$virtual_fee = $booking->getBookingVirtualFeeAmount($this);
		$booking->service_fee = $service_fee;
		$booking->virtual_fee  = $virtual_fee;
		$booking->processor_fee		= $booking->getBookingPaymentProcessingFeeAmount($this->spot_price, $service_fee + $virtual_fee);
		$booking->save();



		if ($this->lesson_type == 'in_person_client' && $request->input('location')) {

			$this->location = $request->input('location');
			$locationDetails = getLocationDetails($this->location);

			$this->lat = isset($locationDetails['lat']) ? $locationDetails['lat'] : null;
			$this->lng = isset($locationDetails['lng']) ? $locationDetails['lng'] : null;
			$this->city = isset($locationDetails['city']) ? $locationDetails['city'] : null;
			$this->state = isset($locationDetails['state']) ? $locationDetails['state'] : null;
			$this->address = isset($locationDetails['address']) ? $locationDetails['address'] : null;
			$this->zip = isset($locationDetails['zip']) ? $locationDetails['zip'] : null;

			$this->save();
		}


		if(Auth::user() != null){
			if ($this->instructor->clients()->where('client_id', $student->id)->count() == 0) {
				\Log::info('add instructor client');
				$this->instructor->clients()->attach($student);
			}
			if ($student->instructors()->where('instructor_id', $this->instructor_id)->count() == 0) {
				\Log::info('add client instructor');
				$student->instructors()->attach($this->instructor);
			}
		}
		
		return $booking;
	}

	// can cancel not past and not already cancelled
	public function cancel()
	{
		$this->bookings()
			->where('status', '<>', Booking::STATUS_CANCELLED)
			->each(function (Booking $booking) {
				$cencelledBy = Auth::user()->id;
				$booking->cancel($cencelledBy);
			});
		$this->is_cancelled = true;
		$this->save();
		return true;
	}

	public function save(array $options = [])
	{
		if ($this->lesson_type == 'in_person') {
			$locationDetails = getLocationDetails($this->location);

			$this->lat = isset($locationDetails['lat']) ? $locationDetails['lat'] : null;
			$this->lng = isset($locationDetails['lng']) ? $locationDetails['lng'] : null;
			$this->city = isset($locationDetails['city']) ? $locationDetails['city'] : null;
			$this->state = isset($locationDetails['state']) ? $locationDetails['state'] : null;
			$this->address = isset($locationDetails['address']) ? $locationDetails['address'] : null;
			$this->zip = isset($locationDetails['zip']) ? $locationDetails['zip'] : null;
			$this->timezone_id = isset($locationDetails['timezone_id']) ? $locationDetails['timezone_id'] : null;
		}

		$time = new \DateTime($this->start, new \DateTimeZone($this->timezone_id));
		$this->timezone_offset_gmt = $time->format('P');

		parent::save($options);
	}


	public function getLocationAttribute($value)
	{
		if ($this->address != null && $this->state != null)
			return str_replace(', ,', ', ', "{$this->address} <br/>$this->city, $this->state, $this->zip");
		else
			return $value;
	}

	public function getRoomType()
	{
		if ($this->spots_count <= 4)
			return 'group-small';

		return 'group';
	}

	public function createFromLessonRequest(LessonRequest $lessonRequest)
	{
		$input = [
			'instructor_id' => $lessonRequest->instructor_id,
			'private_for_student_id' => $lessonRequest->student_id,
			'count_places_in_spot' => $lessonRequest->count_participants,
			'genre_id' => $lessonRequest->genre_id,
			'start' => $lessonRequest->start,
			'end' => $lessonRequest->end,
			'spots_count' => 1,
			'spot_price' => $lessonRequest->lesson_price,
			'location' => $lessonRequest->location,
			'lesson_type' => $lessonRequest->lesson_type,
			'timezone_id' => $lessonRequest->timezone_id,
			'description' => $lessonRequest->instructor_note
		];
		
		return $this->create($input);
	}
}
