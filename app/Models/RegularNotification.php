<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'lesson_id'
    ];

    protected $table = 'regular_notification';

    public function user()
    {
        return $this->belongsToMany(\App\Models\User::class);
    }


    public function booking()
    {
        return $this->belongsToMany(\App\Models\Booking::class);
    }

    public function lesson()
    {
        return $this->belongsToMany(\App\Models\Lesson::class);
    }
}
