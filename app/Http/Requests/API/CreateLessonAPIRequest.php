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
			'date'			=> ['required', 'date_format:Y-m-d', 'future_date', 'no_lessons_this_time:time_from,time_to,id,date,date_to,timezone_id'],
			'date_to'		=> ['required', 'date_format:Y-m-d', 'future_date', 'no_lessons_this_time:time_from,time_to,id,date,date_to,timezone_id'],
			'time_from'		=> ['required', 'date_multi_format:' . $formats],
			'time_to'		=> ['required', 'date_multi_format:' . $formats],
			'spots_count'	=> ['required', 'integer', 'min:1', 'max:100'],
			'spot_price'	=> ['required', 'numeric', 'virtual_min_price:lesson_type'],
			'location'		=> ['required_if:lesson_type,in_person', 'nullable', 'is_exact_address'],
            'timezone_id'   => ['required_if:lesson_type,virtual,in_person_client', "valid_timezone:$lesson_type"],
            'lesson_type'   => ['required', 'in:in_person,virtual,in_person_client']
		];

		return $rules;
    }

    public function messages() {

        if(request('lesson_type') == 'virtual') {
            $minPrice = data_get(auth()->user(), 'profile.virtual_min_price');
            $virtual_min_price = empty($minPrice) ? 1.00 : $minPrice;
        } else {
            $virtual_min_price = 1;
        }


		return [
			'date.future_date' => 'Select future date',
			'date_to.future_date' => 'Select future date',
			'time_from.date_multi_format' => 'Wrong time format',
			'time_to.date_multi_format' => 'Wrong time format',
			'date.no_lessons_this_time' => 'You have already scheduled a lesson at this time.',
			'date_to.no_lessons_this_time' => 'You have already scheduled a lesson at this time.',
			'spot_price.virtual_min_price' => 'Minimum lesson price must be greater than ' . number_format($virtual_min_price, 2, null, null) . '$',
			'spots_count.required' => 'Please enter a number of students',
			'location.is_exact_address' => 'Lesson location address should be clarified',
			'timezone_id.valid_timezone' => 'Wrong timezone',
			'lesson_type.in' => 'Wrong lesson type',
			'location.required_if' => 'The location field is required when lesson type is In Person.',
			'timezone_id.required_if' => 'The location field is required when lesson type is In Virtual.',
		];
	}
}
