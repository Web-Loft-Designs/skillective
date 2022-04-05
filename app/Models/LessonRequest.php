<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Auth;
use Log;
use MaksimM\SubqueryMagic\SubqueryMagic;
use Carbon\Carbon;

class LessonRequest extends Model implements Transformable
{
    use SoftDeletes, SubqueryMagic;

    public $table = 'lesson_requests';

    const STATUS_PENDING	= 'pending'; // waiting instructor approval
    const STATUS_APPROVED	= 'approved'; // lesson created for this request
    const STATUS_CANCELLED	= 'cancelled';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'instructor_id',
        'student_id',
        'genre_id',
        'start',
        'end',
        'count_participants',
        'lesson_price',
        'location',
        'address',
		'student_note',
		'instructor_note',
        'city',
        'state',
        'zip',
		'lat',
		'lng',
        'status',
		'timezone_id',
        'timezone_offset_gmt',
        'lesson_type',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'instructor_id' => 'integer',
        'student_id' => 'integer',
        'genre_id' => 'integer',
        'start' => 'datetime',
        'end' => 'datetime',
        'count_participants' => 'integer',
        'lesson_price' => 'float',
        'location' => 'string',
        'address' => 'string',
        'city' => 'string',
        'state' => 'string',
        'zip' => 'string',
        'status' => 'string',
		'lat' => 'float',
		'lng' => 'float',
        'student_note' => 'string',
        'instructor_note' => 'string',
		'timezone_id' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'instructor_id' => 'required',
        'student_id' => 'required',
        'genre_id' => 'required',
        'start' => 'required',
        'end' => 'required',
        'count_participants' => 'required',
        'lesson_price' => 'required',
        'location' => 'required',
        'lesson_type' => 'required'
//        'address' => 'required',
//        'city' => 'required',
//        'state' => 'required',
    ];

	protected $hidden = [
		'created_at', 'updated_at', 'deleted_at'
	];

	public static function getLessonTypes(){
	    return Lesson::getLessonTypes();
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function student()
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function lesson()
    {
        return $this->hasOne(\App\Models\Lesson::class, 'lesson_id');
    }

	public function transform()
	{
		return [
			'id' => (int)$this->id,
			'instructor_id' => $this->instructor_id,
			'student_id' => $this->student_id,
			'genre_id'=> $this->genre_id,
			'genre'=> $this->genre->transform(),
			'instructor'=> $this->instructor->transform(),
			'student' => $this->student->transform(),
			'start'=> $this->start->format('Y-m-d H:i:s'),
			'end'=> $this->end->format('Y-m-d H:i:s'),
			'timezone_id'=> getTimezoneAbbrev($this->timezone_id),
			'timezone_offset_gmt'=> $this->timezone_offset_gmt,
			'count_participants'=> $this->count_participants,
			'lesson_price'=> number_format((float)$this->lesson_price, 2, '.', ''),
			'location'=> $this->location,
			'lat' => $this->lat,
			'lng' => $this->lng,
			'city' => $this->city,
			'state' => $this->state,
			'address' => $this->address,
			'zip' => $this->zip,
			'student_note' => $this->student_note,
			'instructor_note' => $this->instructor_note,
            'lesson_type' => $this->lesson_type,
		];
	}

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
			self::STATUS_APPROVED,
            self::STATUS_CANCELLED,
        ];
    }

    public static function getStatusTitle($status)
    {
        return ucfirst(str_replace('_', ' ', $status));
    }

    public function saveQuietly(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }

	public function save(array $options = [])
	{
	    if ($this->lesson_type=='in_person'){
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

	// public function getLocationAttribute($value){
	// 	if ($this->address!=null && $this->state!=null)
	// 		return str_replace(', ,', ', ', "{$this->address} <br/>$this->city, $this->state, $this->zip");
	// 	else
	// 		return $value;
	// }

    // can cancel not past and not already cancelled
    public function cancel($reason = null){
        $this->status = self::STATUS_CANCELLED;
        $this->instructor_note = $reason;
        $this->save();
        return true;
    }

    public function autoCancel(){
        $this->cancel(null);
        return true;
    }
}