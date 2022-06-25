<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\LessonRequest;
use App\Models\Lesson;

class LessonRequestInListTransformer extends TransformerAbstract
{
    public function transform(LessonRequest $model)
    {
    	$student = $model->student;
    	$instructor = $model->instructor;

		return [
			'id'				=> $model->id,
			'instructor_id'		=> $model->instructor_id,
			'student_id'		=> $model->student_id,
			'lesson_id'			=> $model->lesson_id,
			'student'			=> [
				'id' => $student->id,
				'first_name' => $student->first_name,
				'last_name'=> $student->last_name,
				'full_name'=> $student->getName(),
				'email'=> $student->getEmail(),
				'profile' => [
					'id' =>  $student->profile->id,
					'instagram_handle' => $student->profile->instagram_handle,
					'image' => $student->profile->getImageUrl(),
					'mobile_phone' => $student->profile->mobile_phone,
				]
			],
            'instructor'=> [
                'id' => $instructor->id,
                'first_name' => $instructor->first_name,
                'last_name'=> $instructor->last_name,
                'full_name'=> $instructor->getName(),
                'genres' => $instructor->genres->toArray(),
                'profile' => [
                    'id' =>  $instructor->profile->id,
                    'user_id' => $instructor->profile->user_id,
                    'instagram_handle' => $instructor->profile->instagram_handle,
                    'image' => $instructor->profile->getImageUrl(),

                ]
            ],
            'genre_id'=> $model->genre_id,
            'genre'=> $model->genre->transform(),
            'start'=> $model->start->format('Y-m-d H:i:s'),
            'end'=> $model->end->format('Y-m-d H:i:s'),
            'timezone_id'=> getTimezoneAbbrev($model->timezone_id),
            'timezone_id_name' => $model->timezone_id,
            'count_participants'=> $model->count_participants,
            'lesson_price'=> number_format((float)$model->lesson_price, 2, '.', ''),
            'location'=> $model->location,
            'lat' => $model->lat,
            'lng' => $model->lng,
            'city' => $model->city,
            'state' => $model->state,
            'address' => $model->address,
            'zip' => $model->zip,
            'student_note' => $model->student_note,
            'lesson_type' => $model->lesson_type,
            'preview' => $model->getPreviewUrl(),

		];
    }
}
