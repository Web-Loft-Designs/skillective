<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class FeaturedInstructor extends Model
{

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    protected $fillable = [
    	'instructor_id',
        'priority'
    ];

    protected $table = 'featured_instructors';
}
