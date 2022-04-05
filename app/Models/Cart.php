<?php

namespace App\Models;

use Eloquent as Model;

class Cart extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
    	'student_id',
        'lesson_id',
        'instructor_id',
        'description',
        'pre_r_lesson_id'
    ];

    protected $table = 'cart';

    public function lesson()
	{
		return $this->belongsTo(\App\Models\Lesson::class, 'lesson_id');
	}

    public function preRecordedLesson()
	{
		return $this->belongsTo(\App\Models\PreRecordedLesson::class, 'pre_r_lesson_id');
	}

    public function checkout($request, $user_repository)
    {
    }
}
