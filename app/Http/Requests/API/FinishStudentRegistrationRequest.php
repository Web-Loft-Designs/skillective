<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


class FinishStudentRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()==null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
		return [
			'email'		=> 'required|email',
			'token'		=> 'required',
			'password'	=> 'required|string|regex:'.getPasswordValidationFormat().'|confirmed'
			];
    }

	public function messages()
	{
		return [
			'password.regex'	=> getPasswordValidationFormatError()
		];
	}
}
