<?php

namespace App\Http\Requests\API;

use App\Models\Lesson;
use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class CreateLessonAPIRequest extends APIRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()->hasRole(User::ROLE_INSTRUCTOR);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
		$availableGenresIds	= Genre::select('id')->get()->pluck('id')->toArray();

		$formats = [];
		for ($m = 0; $m<60; $m += Lesson::TIME_INTERVAL){
			$formats[] = 'H:' . sprintf('%02d', $m) . ':00';
		}
		$formats = implode(',', $formats);

		$lesson_type = $request->input('lesson_type', null);

        $rules = [
			'genre'			=> ['required', Rule::in( $availableGenresIds )],
            'lesson_type'   => ['required', 'in:in_person,virtual,in_person_client'],
            'location'		=> ['required_if:lesson_type,in_person', 'nullable', 'is_exact_address'],
            'timezone_id'   => ['required_if:lesson_type,virtual', 'nullable', "valid_timezone:$lesson_type"],
            'time_from'		=> ['required', 'date_multi_format:' . $formats],
            'time_to'		=> ['required', 'date_multi_format:' . $formats],
			'date'			=> ['required', 'date_format:Y-m-d', 'future_date',
                'no_lessons_this_time:time_from,time_to,id,date,date_to,timezone_id,lesson_id'],
			'date_to'		=> ['required', 'date_format:Y-m-d', 'future_date',
                'no_lessons_this_time:time_from,time_to,id,date,date_to,timezone_id,lesson_id'],
			'spots_count'	=> ['required', 'integer', 'min:1', 'max:100'],
			'spot_price'	=> ['required', 'numeric', 'min:1'],
		];

		return $rules;
    }

    public function messages() {
		return [
            'date.required' => 'Please enter a Month and/or Date',
            'date_to.required' => 'Please enter a Month and/or Date',
			'date.future_date' => 'Select future date',
			'date_to.future_date' => 'Select future date',
			'time_from.date_multi_format' => 'Please enter a Start time',
			'time_to.date_multi_format' => 'Please enter a End time',
			'date.no_lessons_this_time' => 'Overlaps with some other lesson',
			'date_to.no_lessons_this_time'	 => 'Overlaps with some other lesson',
            'spot_price.*' => 'A price of $1 or larger is required',
			//'spot_price.min' => 'Minimum lesson price must be greater than $0.99',
			'spots_count.required' => 'Please enter a number of students',
			'location.is_exact_address' => 'Lesson location address should be clarified',
			'timezone_id.valid_timezone' => 'Wrong timezone',
			'lesson_type.in' => 'Wrong lesson type',
			'location.required_if' => 'The location field is required when lesson type is In Person.',
			'timezone_id.required_if' => 'The location field is required when lesson type is In Virtual.',
		];
	}
}
