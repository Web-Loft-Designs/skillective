<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CartUserInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {

        if( Auth::check() ) {
            return [
                'first_name'		=> ['required', 'string', 'max:255'],
                'last_name'			=> ['required', 'string', 'max:255'],
                'email'				=> ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::user()->id),],
                'zip'				=> getPostCodeValidationRules(),
                'dob'				=> getDOBValidationRules(),
                'mobile_phone'		=> getMobilePhoneValidationRules(),
                'accept_terms'		=> ['accepted']
            ];
        } else {
            return [
                'first_name'		=> ['required', 'string', 'max:255'],
                'last_name'			=> ['required', 'string', 'max:255'],
                'email'				=> ['required', 'string', 'email', 'max:255', 'unique:users'],
                'zip'				=> getPostCodeValidationRules(),
                'dob'				=> getDOBValidationRules(),
                'mobile_phone'		=> getMobilePhoneValidationRules(),
                'accept_terms'		=> ['accepted']
            ];
        }

    }

    public function messages()
    {
        $mesages = [
            'email.unique'	=> "Seems you already have an account on our site, login please to book.",
            'accept_terms.accepted'	=> "Agree to our terms please",
        ];
        $mesages = array_merge($mesages, getDOBValidationMessages(), getMobilePhoneValidationRules(), getPostCodeValidationRules());
        return $mesages;
    }
}
