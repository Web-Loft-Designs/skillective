<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RegularNotification extends Model
{

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
    	'user_id',
        'booking_id',
        'type',
        'status',
        'message',
        'sended_at',
        'lesson_id',
        'date_send_time_utc'
    ];

    protected $table = 'regular_notification';

    /**
     * @return BelongsToMany
     */
    public function user()
    {
        return $this->belongsToMany(User::class);
    }


    /**
     * @return BelongsToMany
     */
    public function booking()
    {
        return $this->belongsToMany(Booking::class);
    }

    /**
     * @return BelongsToMany
     */
    public function lesson()
    {
        return $this->belongsToMany(Lesson::class);
    }
}
