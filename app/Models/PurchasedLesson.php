<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasedLesson extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
    	'instructor_id',
		'student_id',
        'pre_r_lesson_id',
		'price'
    ];

    const STATUS_PENDING	= 'pending'; // waiting merchant approval
	const STATUS_ESCROW		= 'payment_in_escrow'; // money on marketplace account
	const STATUS_ESCROW_RELEASED		= 'payment_released'; // money transferring to merchant account
	const STATUS_UNABLE_ESCROW_RELEASE	= 'payment_release_error'; // can't release transaction, reason sent to admin emails
	const STATUS_COMPLETE	= 'complete'; // money moved to merchant account
	const STATUS_CANCELLED	= 'cancelled'; // merchant cancelled booking , money must go back to student

    protected $table = 'purchased_lessons';

    /**
     * @return BelongsTo
     */
    public function preRecordedLesson()
    {
        return $this->belongsTo(PreRecordedLesson::class, 'pre_r_lesson_id');
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
     * @return string[]
     */
    public static function getStatuses()
	{
		return [
			self::STATUS_PENDING,
			self::STATUS_ESCROW,
			self::STATUS_ESCROW_RELEASED,
			self::STATUS_UNABLE_ESCROW_RELEASE,
			self::STATUS_CANCELLED,
			self::STATUS_COMPLETE
		];
	}

    /**
     * @param $status
     * @return string
     */
    public static function getStatusTitle($status)
	{
		switch ($status){
			case self::STATUS_ESCROW:
				return 'In Escrow';
				break;
			case self::STATUS_ESCROW_RELEASED:
				return 'Released from Escrow';
				break;
			case self::STATUS_UNABLE_ESCROW_RELEASE:
				return 'Unable release from Escrow';
				break;
			case self::STATUS_CANCELLED:
				return 'Cancelled';
				break;
			case self::STATUS_COMPLETE:
				return 'Complete';
				break;
			default:
				return ucfirst(str_replace('_', ' ', $status));
		}
	}

    /**
     * @param $status
     * @return void
     */
    public function setStatusAttribute($status)
	{
		if (in_array($status, self::getStatuses()) && $this->status!==$status){
			$this->attributes['status'] = $status;
		}
	}
}
