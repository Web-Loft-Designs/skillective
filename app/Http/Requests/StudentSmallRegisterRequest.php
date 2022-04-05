<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentSmallRegisterRequest extends FormRequest
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
            'email'				=> ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile_phone'		=> getMobilePhoneValidationRules(),
        ];
    }

    public function messages()
    {
        return [
            'accept_terms.accepted'	=> "Agree to our terms please",
        ];
    }
}
