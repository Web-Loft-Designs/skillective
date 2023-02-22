<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * @return BelongsTo
     */
    public function preRecordedLesson()
    {
        return $this->belongsTo(PreRecordedLesson::class);
    }

    /**
     * @return array
     */
    public function transform()
	{
		return [
			'title' => $this->name,
			'url' => $this->getFileUrl()
		];
	}

    /**
     * @return string
     */
    public function getFileUrl()
    {
        return config('app.url') . '/storage/' . 'videos/' . $this->preRecordedLesson->instructor_id . '/' . $this->name;
    }
}
