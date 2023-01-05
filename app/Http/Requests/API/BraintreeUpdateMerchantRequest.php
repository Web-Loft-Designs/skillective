<?php

namespace App\Http\Requests\API;

use App\Models\Lesson;
use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Validation\Rule;

class BraintreeUpdateMerchantRequest extends BraintreeCreateMerchantRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()->hasRole(User::ROLE_INSTRUCTOR);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'individual_firstName'		=> ['required'],
            'individual_lastName'		=> ['required'],
            'individual_email'			=> ['email'],
            'individual_streetAddress'	=> ['required'],
            'individual_locality'		=> ['required'],
            'individual_region'			=> ['valid_us_state'],
            'individual_postalCode'		=> getPostCodeValidationRules(),
            'individual_dateOfBirth'	=> getDOBValidationRules(),
            'individual_phone'			=> getMobilePhoneValidationRules(),
//            'individual_ssn'			=> ['sometimes', 'nullable'],
            'funding_email'				=> ['sometimes', 'nullable', 'email'],
            'funding_mobilePhone'		=> getFundingMobilePhoneValidationRules(),
            'funding_routingNumber'		=> ['required'],

        ];

        return $rules;
    }

    public function messages()
    {
        $mesages = [
            'individual_firstName.required'	=> "First name required",
            'individual_lastName.required'	=> "Last name required",
            'individual_email.required'	=> "Email required",
            'individual_streetAddress.required'	=> "Street address required",
            'individual_locality.required'	=> "City required",
            'individual_region.required'	=> "State required",
            'individual_region.valid_us_state'	=> "State invalid",
            'individual_postalCode.required'	=> "Postal code required",
            'individual_postalCode.regex'	=> "Postal code wrong format",
            'individual_dateOfBirth.required'			=> "Date of birth required",
            'individual_dateOfBirth.date_format'		=> "Not valid date format",
            'individual_dateOfBirth.before'			=> "You must be more than 16 years old to become an instructor.",
            'individual_phone.required'	=> "Phone required",
            'individual_phone.regex'	=> "Phone wrong format",

            'funding_email.required'	=> "Email required",
            'funding_mobilePhone.required'	=> "Phone required",
            'funding_mobilePhone.regex'	=> "Phone wrong format",
            'funding_accountNumber.required'	=> "Bank Account number required",
            'funding_routingNumber.required'	=> "Routing number required",

            'tosAccepted.accepted'	=> "Agree to our terms please",
        ];
        $mesages = array_merge($mesages, getDOBValidationMessages(), getAboutMeValidationMessages());
        return $mesages;
    }
}
