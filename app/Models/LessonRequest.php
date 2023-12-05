<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;


class LessonRequest extends Model implements Transformable
{

    use SoftDeletes;

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
    ];

	protected $hidden = [
		'created_at', 'updated_at', 'deleted_at'
	];

    /**
     * @return string[]
     */
    public static function getLessonTypes(){
	    return Lesson::getLessonTypes();
    }


    /**
     * @return BelongsTo
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }


    /**
     * @return BelongsTo
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }


    /**
     * @return BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }


    /**
     * @return HasOne
     */
    public function lesson()
    {
        return $this->hasOne(Lesson::class, 'lesson_id');
    }

    /**
     * @return array
     */
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

    /**
     * @return string[]
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
			self::STATUS_APPROVED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * @param $status
     * @return string
     */
    public static function getStatusTitle($status)
    {
        return ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * @param array $options
     * @return bool|mixed
     * @throws Exception
     */
    public function saveQuietly(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }

    /**
     * @param array $options
     * @return bool|void
     * @throws Exception
     */
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

    /**
     * @param $reason
     * @return true
     * @throws Exception
     */
    public function cancel($reason = null): bool
    {
        $this->status = self::STATUS_CANCELLED;
        $this->instructor_note = $reason;
        $this->save();
        return true;
    }

    /**
     * @return true
     * @throws Exception
     */
    public function autoCancel(): bool
    {
        $this->cancel();
        return true;
    }
}
