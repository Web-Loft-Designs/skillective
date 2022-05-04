<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Lesson;
use App\Models\Booking;

/**
 * Class LessonInListTransformer.
 *
 * @package namespace App\Transformers;
 */
class LessonDashboardTransformer extends TransformerAbstract
{
    /**
     * Transform the Lesson entity.
     *
     * @param \App\Models\Lesson $model
     *
     * @return array
     */
    public function transform(Lesson $model)
    {
        return [
			'id' => (int)$model->id,
			'instructor_id' => $model->instructor_id,
			'genre_id'=> $model->genre_id,
			'genre'=> $model->genre->transform(),
			'students' => [],
			'instructor'=> [
				'id' => $model->instructor->id,
				'first_name' => $model->instructor->first_name,
				'last_name'=> $model->instructor->last_name,
				'full_name'=> $model->instructor->getName(),
//				'email'=> $model->instructor->getEmail(),
				'profile' => [
					'id' =>  $model->instructor->profile->id,
					'user_id' => $model->instructor->profile->user_id,
					'instagram_handle' => $model->instructor->profile->instagram_handle,
//					'address' => $model->instructor->profile->address,
//					'city' => $model->instructor->profile->city,
//					'state' => $model->instructor->profile->state,
//					'zip' => $model->instructor->profile->zip,
//					'full_address' => $model->instructor->profile->getFullAddress(),
//					'mobile_phone' => $model->instructor->profile->mobile_phone,
//					'dob' => $model->instructor->profile->dob ? $model->instructor->profile->dob->toDateString(): null,
//					'about_me' => $model->instructor->profile->about_me,
					'image' => $model->instructor->profile->getImageUrl(),
//					'notification_methods' => $model->instructor->profile->notification_methods,
//					'instagram_followers_count' => $model->instructor->profile->instagram_followers_count,
//					'gender' => $model->instructor->profile->gender,
				]
            ],
            'bookings' => $model->bookings,
			'start'=> $model->start->format('Y-m-d H:i:s'),
			'end'=> $model->end->format('Y-m-d H:i:s'),
			'timezone_id' => $model->timezone_id,
            'timezone_id_name' => $model->timezone_id,
			'spots_count'=> $model->spots_count,
			'count_booked'=> (int)$model->count_booked,
			'spot_price'=> number_format((float)$model->spot_price, 2, '.', ''),
			'location'=> $model->location,
			'lat' => $model->lat,
			'lng' => $model->lng,
			'city' => $model->city,
			'state' => $model->state,
			'address' => $model->address,
			'zip' => $model->zip,
			'description' => $model->description,
            'lesson_type' => $model->lesson_type,
            'room_sid' => $model->room_sid,
            'room_completed' => $model->room_completed,
            'extra_time_before_start' => Lesson::VIRTUAL_LESSON_EXTRA_TIME_BEFORE_START,
			'extra_time_after_end' => Lesson::VIRTUAL_LESSON_EXTRA_TIME_AFTER_END,
			'bookings_count' => $model->bookings_count
        ];
    }
}
