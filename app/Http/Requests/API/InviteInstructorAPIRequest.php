<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Auth;
use Illuminate\Validation\Rule;

class InviteInstructorAPIRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user() && (Auth::user()->hasRole(User::ROLE_INSTRUCTOR) || Auth::user()->hasRole(User::ROLE_STUDENT));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
    	$emailRegexp = getInviteEmailValidationRegexp();
    	$phoneRegexp = getInviteMobilePhoneValidationRegexp();
		$rules = [
			'invited_name'				=> ['nullable', 'string', 'max:255'],
			'invited_contact'			=> ['required', 'string', 'max:255', "regex:/^($phoneRegexp)|($emailRegexp)$/ix"],
			];
		if (Auth::user()->hasRole(User::ROLE_STUDENT)){
			$rules['invited_contact']	= ['required', 'string', 'max:255', "not_regex:/^($phoneRegexp)|($emailRegexp)$/ix"];
		}

		return $rules;
    }

	public function messages()
	{
		$mesages = [
			'invited_name.required'			=> "Name required",
			'invited_name.string'			=> "Name should be a string",
			'invited_name.string'			=> "Name max length is 255 symbols",
			'invited_contact.required'		=> "Contact Email/Phone required",
			'invited_contact.string'		=> "Contact Email/Phone should be a string",
			'invited_contact.regex'			=> "Contact Email/Phone should be a valid email address or US phone number(+1XXXXXXXXXX)",
			'invited_contact.not_regex'			=> "Contact should existing instagram username",
			'invited_contact.max'			=> "Contact Email/Phone max length is 255 symbols",

		];
		return $mesages;
	}
}
