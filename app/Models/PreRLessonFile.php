<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;

class PreRLessonFile extends Model implements Transformable
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    public $fillable = [
        'pre_r_lesson_id',
        'name'
    ];


    public $table = 'pre_r_lesson_files';
    
    public function preRecordedLesson()
    {
        return $this->belongsTo('App\Models\PreRecordedLesson');
    }

    public function transform()
	{
		return [
			'title' => $this->name,
			'url' => $this->getFileUrl()
		];
	}

    // public function toArray()
	// {
	// 	return [
	// 		'title' => $this->name,
	// 		'url' => $this->getFileUrl()
	// 	];
	// }

    public function getFileUrl()
    {
        return config('app.url') . '/storage/' . 'videos/' . $this->preRecordedLesson->instructor_id . '/' . $this->name;
    }
}
