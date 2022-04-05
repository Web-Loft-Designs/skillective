<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;

class CancelBookingsAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
			'bookings' => ['required', 'array'],
		];
		return $rules;
    }
}