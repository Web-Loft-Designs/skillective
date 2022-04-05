<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomNotification extends Model
{
    public static $tagsWithoutSms = [
        'reset_password',

        'student_registration_confirmation',
        'student_registration_for_admins',

        'instructor_registration_confirmation',
        'instructor_registration_for_admins',
        'instructor_registration_request_denied',
        'instructor_registration_request_approved'
        ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag',
        'data',
    ];

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Date casts.
     *
     * @var array
     */
    protected $dates = [
        'updated_at',
        'created_at',
    ];

    /**
     * Appends to JSON.
     *
     * @var array
     */
    protected $appends = [
        //
    ];

    public function methods()
    {
        return $this->hasMany(CustomNotificationMethod::class);
    }
}
