<?php

namespace App\Http\Requests\API;

use App\Models\Lesson;
use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class BookingUserDataAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return !Auth::user() || Auth::user()->hasRole(User::ROLE_STUDENT);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
		if (!Auth::user() || Auth::user()->hasFakeEmail()){ // unauthorized user or incomplete profile
			$availableGenresIds	= Genre::select('id')->get()->pluck('id')->toArray();
			return [
				'first_name'		=> ['required', 'string', 'max:255'],
				'last_name'			=> ['required', 'string', 'max:255'],
				'instagram_handle'	=> ['sometimes', 'string', 'max:255', 'unique:profiles'],
				'email'				=> ['required', 'string', 'email', 'max:255', 'unique:users'],
//				'address'			=> ['required', 'string', 'max:255'],
				'city'				=> getCityValidationRules($request),
				'state'				=> ['required', 'string', 'max:255', 'valid_us_state'],
				'zip'				=> getPostCodeValidationRules(),
				'dob'				=> getDOBValidationRules(),
				'mobile_phone'		=> getMobilePhoneValidationRules(),
				'gender'			=> ['required', Rule::in( getGenders() )],
				'genres'			=> ['required', 'array'],
				'genres.*'			=> [Rule::in( $availableGenresIds )],
				'accept_terms'		=> ['accepted']
			];
		}else{
			return [
				'accept_terms'		=> ['accepted']
			];
		}
    }

	public function messages()
	{
		$mesages = [
			'email.unique'	=> "Seems you already have an account on our site, login please to book.",
			'instagram_handle.unique'	=> "Seems you already have an account on our site, login please to book.",
			'accept_terms.accepted'	=> "Agree to our terms please",
		];
		$mesages = array_merge($mesages, getDOBValidationMessages(), getCityValidationMessages());
		return $mesages;
	}
}
