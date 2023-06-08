<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomNotificationMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'method',
        'content',
        'data',
        'active',
        'custom_notification_id',
    ];

    protected $casts = [
        'active' => 'bool',
        'data'   => 'array',
    ];

    protected $attributes = [
        'active' => true,
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

    public function notification()
    {
        return $this->belongsTo(CustomNotificationMethod::class, 'custom_notification_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
