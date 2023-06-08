<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BecomeAnInstructorRequest extends FormRequest
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
			'fullName' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			];
    }

	public function messages()
	{
		$mesages = [
			'fullName.required' => "Enter your name",
            'email.required' => "Enter your email",
            'email.unique' => 'A user with this email is already registered in the system'
		];

		return $mesages;
	}
}
