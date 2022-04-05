<?php

namespace App\Http\Requests\API;

use App\Models\Lesson;
use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class CreateBookingAPIRequest extends BookingUserDataAPIRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
    	$rules = parent::rules($request);

		$rules['payment_method_token'] = ['required_without:payment_method_nonce'];

	 	return $rules;
    }

	public function messages()
	{
		$messages = parent::messages();
		$messages['payment_method_token.required_without'] = "No payment method provided";
		return $messages;
	}
}
