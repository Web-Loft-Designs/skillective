<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\PreRecordedLesson;
use DateInterval;

/**
 * Class LessonInListTransformer.
 *
 * @package namespace App\Transformers;
 */
class PreRLessonSingleTransformer extends TransformerAbstract
{
    /**
     * Transform the Lesson entity.
     *
     * @param \App\Models\Lesson $model
     *
     * @return array
     */
    public function transform(PreRecordedLesson $model)
    {

        list($hours, $minutes, $seconds) = sscanf($model->duration, '%d:%d:%d');
        
        $duration = "";

        if($model->duration){
            $duration = new DateInterval(sprintf('PT%dH%dM%dS', $hours, $minutes, $seconds));
        }
       
        return [
            'id' => (int)$model->id,
            'instructor_id' => $model->instructor_id,
            'genre_id' => $model->genre_id,
            'genre' => $model->genre->transform(),
            'preview' => $model->getPreviewUrl(),
            'video' => $model->video ? $model->getVideoUrl() : "",
            'description' => $model->description,
            'title' => $model->title,
            'price' => $model->price,
            'documents' => $model->files,
            'documentsPath' => config('app.url') . '/storage/' . 'videos/' . $model->instructor->id . '/',
            'duration' => $model->video ? $duration->format('%h h %i min %s sec') : "",
            'start' => $model->created_at->format('M d, h:i A'),
            'instructor' => [
                'id' => $model->instructor->id,
                'first_name' => $model->instructor->first_name,
                'last_name' => $model->instructor->last_name,
                'full_name' => $model->instructor->getName(),
                'instagram_handle' => $model->instructor->profile->instagram_handle,
                'profile' => [
                    'id' =>  $model->instructor->profile->id,
                    'user_id' => $model->instructor->profile->user_id,
                    'instagram_handle' => $model->instructor->profile->instagram_handle,
                    'image' => $model->instructor->profile->getImageUrl()
                ]
            ],
        ];
    }
}
