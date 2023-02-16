<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Cart;
use Log;

/**
 * Class BookingInListTransformer.
 *
 * @package namespace App\Transformers;
 */
class CartListTransformer extends TransformerAbstract
{
	/**
	 * Transform the Booking entity.
	 *
	 * @param \App\Models\Cart $model
	 *
	 * @return array
	 */
	public function transform(Cart $model)
	{
		if ($model->lesson_id && !$model->pre_r_lesson_id) {
			$lesson = $model->lesson;

			$genre =  $lesson->genre->transform();
			return [
				'id'				=> $model->id,
				'instructor_id'		=> $model->instructor_id,
				'student_id'		=> $model->student_id,
				'lesson_id'			=> $model->lesson_id,
				'description'       => $model->description,
				'count_booked' => (int)$model->count_booked,
				'discounts'			=> $model->discounts,
				'lesson'			=> [
					'id' => (int)$lesson->id,
					'genre' => $genre['title'],
					'instructor' => [
						'instagram_handle' => $lesson->instructor->profile->instagram_handle,
						'image' => $lesson->instructor->profile->getImageUrl(),
						'full_name' => $lesson->instructor->getName(),
					],
					'start' => $lesson->start->format('Y-m-d H:i:s'),
					'end' => $lesson->end->format('Y-m-d H:i:s'),
					'timezone_id' => getTimezoneAbbrev($lesson->timezone_id),
					'timezone_id_name' => $lesson->timezone_id,
					'spots_count' => $lesson->spots_count,
					'spot_price' => number_format((float)$lesson->spot_price, 2, '.', ''),
					'location' => $lesson->location,
					'city' => $lesson->city,
					'state' => $lesson->state,
					'address' => $lesson->address,
					'zip' => $lesson->zip,
					'description' => $lesson->description,
					'lesson_type' => $lesson->lesson_type,

				]
			];
		} else {

			$lesson = $model->preRecordedLesson;

			$genre =  $lesson->genre->transform();

			return [
				'id'				=> $model->id,
				'instructor_id'		=> $model->instructor_id,
				'student_id'		=> $model->student_id,
				'lesson_id'			=> $model->lesson_id,
				'description'       => $model->description,
				'discounts'			=> $model->discounts,
				'lesson' => [
					'id' => (int)$lesson->id,
					'instructor_id' => $lesson->instructor_id,
					'genre_id' => $lesson->genre_id,
					'genre' => $lesson->genre->transform(),
					'preview' => $lesson->getPreviewUrl(),
					'description' => $lesson->description,
					'title' => $lesson->title,
					'price' => $lesson->price,
					'duration' => $lesson->duration,
					'start' => $lesson->created_at->format('Y-m-d H:i:s'),
					'instructor' => [
						'id' => $lesson->instructor->id,
						'first_name' => $lesson->instructor->first_name,
						'last_name' => $lesson->instructor->last_name,
						'full_name' => $lesson->instructor->getName(),
						'image' => $lesson->instructor->profile->getImageUrl(),
						'instagram_handle' => $lesson->instructor->profile->instagram_handle,
						'profile' => [
							'id' =>  $lesson->instructor->profile->id,
							'user_id' => $lesson->instructor->profile->user_id,
							'instagram_handle' => $lesson->instructor->profile->instagram_handle,
							'image' => $lesson->instructor->profile->getImageUrl()
						]
					]
				]
			];
		}
	}
}
