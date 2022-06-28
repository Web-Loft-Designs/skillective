<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Http\Request;

class CartUserInfoRequest extends FormRequest
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
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			];
    }

	public function messages()
	{

        return [
            'email.unique'	=> "User with this email is already registered",
        ];

	}
}
