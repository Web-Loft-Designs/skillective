<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Genre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class StudentRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return !Auth::user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
		$availableGenresIds	= Genre::select('id')->get()->pluck('id')->toArray();

		return [
			'first_name'		=> ['required', 'string', 'max:255'],
			'last_name'			=> ['required', 'string', 'max:255'],
			'city'				=> getCityValidationRules($request),
			'state'				=> ['required', 'string', 'max:255', 'valid_us_state'],
			'zip'				=> getPostCodeValidationRules(),
			'genres'			=> ['required', 'array'],
			'genres.*'			=> [Rule::in( $availableGenresIds )],
			'email'				=> ['required', 'string', 'email', 'max:255', 'unique:users'],
			'mobile_phone'		=> getMobilePhoneValidationRules(),
			'accept_terms'		=> ['accepted']
			];
    }

	public function messages()
	{
		$mesages = [
			'accept_terms.accepted'	=> "Agree to our terms please",
		];
		$mesages = array_merge($mesages, getDOBValidationMessages(), getCityValidationMessages());
		return $mesages;
	}
}
