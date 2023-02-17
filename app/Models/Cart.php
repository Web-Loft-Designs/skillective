<?php

namespace App\Models;

use App\Scopes\CartIsGuest;
use Illuminate\Database\Eloquent\Model;
class Cart extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
    	'student_id',
        'lesson_id',
        'instructor_id',
        'description',
        'pre_r_lesson_id',
        'is_guest',
    ];

    protected $table = 'cart';

    public function lesson()
	{
		return $this->belongsTo(Lesson::class, 'lesson_id');
	}

    public function preRecordedLesson()
	{
		return $this->belongsTo(PreRecordedLesson::class, 'pre_r_lesson_id');
	}

    public function checkout($request, $user_repository)
    {
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {

        parent::boot();
        static::addGlobalScope(new CartIsGuest());

    }
}
