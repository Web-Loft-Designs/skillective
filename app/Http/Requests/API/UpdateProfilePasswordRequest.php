<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class UpdateProfilePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()!=null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
    	$rules = [
			'current_password'		=> ['required', 'hash_match'],
			'new_password' 			=> ['required', 'string', 'regex:' . getPasswordValidationFormat(), 'confirmed']
			];

		if (Auth::user()->hasRole('Admin')
			|| strpos(Auth::user()->password, 'skillectivefake-')===0
		)
			unset($rules['current_password']);

    	return $rules;
    }

	public function messages()
	{
		return [
			'current_password.hash_match'	=> "Invalid current password",
			'new_password.regex'	=> getPasswordValidationFormatError(),
		];
	}
}
