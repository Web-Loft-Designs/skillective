<?php

namespace App\Http\Requests\API;

use App\Models\Lesson;
use Illuminate\Http\Request;
use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Validation\Rule;

class AcceptLessonRequestAPIRequest extends APIRequest
{
    public function authorize()
    {
		return Auth::user()->hasRole(User::ROLE_INSTRUCTOR);
    }

    public function rules(Request $request)
    {
		$availableGenresIds	= Genre::select('id')->get()->pluck('id')->toArray();

		$formats = [];
		for ($m = 0; $m<60; $m += Lesson::TIME_INTERVAL){
			$formats[] = 'H:' . sprintf('%02d', $m) . ':00';
		}
		$formats = implode(',', $formats);

        $rules = [
			'genre'			=> ['required', Rule::in( $availableGenresIds )],
			'date'			=> ['required', 'date_format:Y-m-d', 'future_date', 'no_lessons_this_time:time_from,time_to,id,date,date_to,timezone_id'],
			'date_to'		=> ['required', 'date_format:Y-m-d', 'future_date', 'no_lessons_this_time:time_from,time_to,id,date,_date_to,timezone_id'],
			'time_from'		=> ['required', 'date_multi_format:' . $formats],
			'time_to'		=> ['required', 'date_multi_format:' . $formats],
			'count_participants'	=> ['required', 'integer', 'min:1', 'max:50'],
			'lesson_price'	=> ['required', 'numeric', 'min:5'],
			'location'		=> ['required_if:lesson_type,in_person', 'nullable', 'is_exact_address'],
            'timezone_id'      => ['required_if:lesson_type,virtual', 'nullable'],
            'lesson_type'   => ['required', 'in:in_person,virtual'],
            'instructor_note' => ['required']
		];

        if ($request->input('lesson_type')=='virtual')
            $rules['timezone_id'][] = 'valid_timezone';

		return $rules;
    }

    public function messages() {
		return [
			'date.future_date' => 'Select future date',
			'time_from.date_multi_format' => 'Wrong time format',
			'time_to.date_multi_format' => 'Wrong time format',
			'date.no_lessons_this_time' => 'You have already scheduled a lesson at this time.',
			'date_to.no_lessons_this_time' => 'You have already scheduled a lesson at this time.',
			'location.is_exact_address' => 'Lesson location address should be clarified',
			'timezone_id.valid_timezone' => 'Wrong timezone',
			'lesson_type.in' => 'Wrong lesson type',
			'location.required_if' => 'The location field is required when lesson type is In Person.',
			'timezone_id.required_if' => 'The location field is required when lesson type is In Virtual.',
            'instructor_note.required' => 'Description required'
		];
	}
}
