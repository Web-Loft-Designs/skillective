<?php

namespace App\Http\Requests\API;

use App\Http\Requests\API\CreateLessonAPIRequest;
use Illuminate\Http\Request;

class UpdateLessonAPIRequest extends CreateLessonAPIRequest
{
	public function rules(Request $request)
	{
		$rules = parent::rules($request);

		$rules['date']			= ['required', 'date_format:Y-m-d', 'future_date', 'no_lessons_this_time'];
		$rules['spots_count']	= ['required', 'integer', 'min:1', 'max:100']; // TODO: min to be >= than count booked spots

		return $rules;
	}
}
