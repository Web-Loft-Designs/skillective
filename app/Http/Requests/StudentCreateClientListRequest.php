<?php

namespace App\Http\Requests;

use App\Models\Genre;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentCreateClientListRequest extends FormRequest
{
    public function rules(Request $request)
    {
        $availableGenresIds	= Genre::select('id')->get()->pluck('id')->toArray();

        return [
            'first_name'		=> ['required', 'string', 'max:255'],
            'last_name'			=> ['required', 'string', 'max:255'],
            'city'				=> getCityValidationRules($request),
            'state'				=> ['required', 'string', 'max:255', 'valid_us_state'],
            'zip'				=> getPostCodeValidationRules(),
            'dob'				=> getDOBValidationRules(),
            'genres'			=> ['required', 'array'],
            'genres.*'			=> [Rule::in( $availableGenresIds )],
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
