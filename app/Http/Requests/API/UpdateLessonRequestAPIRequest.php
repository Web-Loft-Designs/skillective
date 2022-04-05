<?php

namespace App\Http\Requests\API;

use App\Http\Requests\API\CreateLessonRequestAPIRequest;

class UpdateLessonRequestAPIRequest extends CreateLessonRequestAPIRequest
{
	public function rules()
	{
		$rules = parent::rules();

		$rules['date']			= ['required', 'date_format:Y-m-d', 'future_date', 'no_lessons_this_time:time_from,time_to,id']; // TODO: remove future date validation. date >= lesson.date
		$rules['spots_count']	= ['required', 'integer', 'min:1', 'max:100']; // TODO: min to be >= than count booked spots

		return $rules;
	}
}
