<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;
use Auth;
use Illuminate\Validation\Rule;

class ContactUsAPIRequest extends APIRequest
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
    public function rules()
    {
        $rules = [
			'first_name' => 'required|string|max:255',
			'last_name' => 'required|string|max:255',
			'address' => 'required|string|max:255',
			'mobile_phone' => getMobilePhoneValidationRules(),
			'email' => 'required|email|max:255',
			'reason' => 'required|string|min:10',
		];

		return $rules;
    }
}
