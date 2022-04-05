<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Booking;
use App\Models\Lesson;

/**
 * Class BookingSingleTransformer.
 *
 * @package namespace App\Transformers;
 */
class BookingSingleTransformer extends TransformerAbstract
{
    /**
     * Transform the Booking entity.
     *
     * @param \App\Models\Booking $model
     *
     * @return array
     */
    public function transform(Booking $model)
    {
    	$lesson = $model->lesson;
    	$student = $model->student;
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
//				'email'=> $student->getEmail(),
				'profile' => [
					'id' =>  $student->profile->id,
					'user_id' => $student->profile->user_id,
					'instagram_handle' => $student->profile->instagram_handle,
//					'address' => $student->profile->address,
//					'city' => $student->profile->city,
//					'state' => $student->profile->state,
//					'zip' => $student->profile->zip,
//					'full_address' => $student->profile->getFullAddress(),
//					'mobile_phone' => $student->profile->mobile_phone,
//					'dob' => $student->profile->dob ? $student->profile->dob->toDateString(): null,
//					'about_me' => $student->profile->about_me,
					'image' => $student->profile->getImageUrl(),
					'notification_methods' => $student->profile->notification_methods,
//					'instagram_followers_count' => $student->profile->instagram_followers_count,
					'gender' => $student->profile->gender,
				]
			],
			'lesson'			=> [
				'id' => (int)$lesson->id,
				'instructor_id' => $lesson->instructor_id,
				'genre_id'=> $lesson->genre_id,
				'genre'=> $lesson->genre->transform(),
				'students' => $lesson->students->toArray(),
				'instructor'=> [
					'id' => $lesson->instructor->id,
					'first_name' => $lesson->instructor->first_name,
					'last_name'=> $lesson->instructor->last_name,
					'full_name'=> $lesson->instructor->getName(),
//					'email'=> $lesson->instructor->getEmail(),
					'profile' => [
						'id' =>  $lesson->instructor->profile->id,
						'user_id' => $lesson->instructor->profile->user_id,
						'instagram_handle' => $lesson->instructor->profile->instagram_handle,
//						'address' => $lesson->instructor->profile->address,
//						'city' => $lesson->instructor->profile->city,
//						'state' => $lesson->instructor->profile->state,
//						'zip' => $lesson->instructor->profile->zip,
//						'full_address' => $lesson->instructor->profile->getFullAddress(),
//						'mobile_phone' => $lesson->instructor->profile->mobile_phone,
//						'dob' => $lesson->instructor->profile->dob ? $lesson->instructor->profile->dob->toDateString(): null,
//						'about_me' => $lesson->instructor->profile->about_me,
						'image' => $lesson->instructor->profile->getImageUrl(),
						'notification_methods' => $lesson->instructor->profile->notification_methods,
//						'instagram_followers_count' => $lesson->instructor->profile->instagram_followers_count,
						'gender' => $lesson->instructor->profile->gender,
					]
				],
				'start'=> $lesson->start->format('Y-m-d H:i:s'),
				'end'=> $lesson->end->format('Y-m-d H:i:s'),
				'timezone_id'=> getTimezoneAbbrev($lesson->timezone_id),
				'spots_count'=> $lesson->spots_count,
				'spot_price'=> number_format((float)$lesson->spot_price, 2, '.', ''),
				'location'=> $lesson->location,
				'lat' => $lesson->lat,
				'lng' => $lesson->lng,
				'city' => $lesson->city,
				'state' => $lesson->state,
				'address' => $lesson->address,
				'zip' => $lesson->zip,
				'description' => $lesson->description,
                'lesson_type' => $lesson->lesson_type,
                'room_sid' => $lesson->room_sid,
                'room_completed' => $lesson->room_completed,
				'count_places_in_spot' => $lesson->count_places_in_spot,
				'extra_time_before_start' => Lesson::VIRTUAL_LESSON_EXTRA_TIME_BEFORE_START,
                'extra_time_after_end' => Lesson::VIRTUAL_LESSON_EXTRA_TIME_AFTER_END,
			],
			'spot_price'		=> $model->spot_price,
			'special_request'	=> $model->special_request,
			'status'			=> $model->status,
			'has_cancellation_request'			=> $model->has_cancellation_request,
            'disconnected'			=> $model->disconnected
		];
    }
}
