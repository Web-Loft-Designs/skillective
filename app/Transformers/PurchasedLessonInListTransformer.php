<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\PurchasedLesson;
use DateInterval;

/**
 * Class LessonInListTransformer.
 *
 * @package namespace App\Transformers;
 */
class PurchasedLessonInListTransformer extends TransformerAbstract
{
    /**
     * Transform the Lesson entity.
     *
     * @param \App\Models\Lesson $model
     *
     * @return array
     */
    public function transform(PurchasedLesson $model)
    {

        list($hours, $minutes, $seconds) = sscanf($model->preRecordedLesson->duration, '%d:%d:%d');
        $duration = new DateInterval(sprintf('PT%dH%dM%dS', $hours, $minutes, $seconds));

        return [
			'id' => (int)$model->id,
			'instructor_id' => $model->instructor_id,
			'genre_id'=> $model->preRecordedLesson->genre_id,
			'genre'=> $model->preRecordedLesson->genre->transform(),
            'preview' => $model->preRecordedLesson->getPreviewUrl(),
            'description' => $model->preRecordedLesson->description,
            'title' => $model->preRecordedLesson->title,
            'duration' => $duration->format('%h h %i min %s sec'),
            'price' => $model->price,
            'start' => $model->preRecordedLesson->created_at->format('M d, H:i A'),
            'video' => $model->preRecordedLesson->video ? true : false,
            'documents' => $model->preRecordedLesson->files,
			'instructor'=> [
				'id' => $model->preRecordedLesson->instructor->id,
				'first_name' => $model->preRecordedLesson->instructor->first_name,
				'last_name'=> $model->preRecordedLesson->instructor->last_name,
				'full_name'=> $model->preRecordedLesson->instructor->getName(),
                'instagram_handle' => $model->preRecordedLesson->instructor->profile->instagram_handle,
				'profile' => [
					'id' =>  $model->preRecordedLesson->instructor->profile->id,
					'user_id' => $model->preRecordedLesson->instructor->profile->user_id,
					'instagram_handle' => $model->preRecordedLesson->instructor->profile->instagram_handle,
					'image' => $model->preRecordedLesson->instructor->profile->getImageUrl()
				]
			],
        ];
    }
}
