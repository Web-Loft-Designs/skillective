<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use App\Models\Genre;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class CheckoutRequest extends FormRequest
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
		return [
			'first_name'		=> ['required', 'string', 'max:255'],
			'last_name'			=> ['required', 'string', 'max:255'],
			'zip'				=> getPostCodeValidationRules(),
			'dob'				=> getDOBValidationRules(),
			'email'				=> ['required', 'string', 'email', 'max:255', 'unique:users'],
			'mobile_phone'		=> getMobilePhoneValidationRules(),
			'gender'			=> ['required', Rule::in( getGenders() )],
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
