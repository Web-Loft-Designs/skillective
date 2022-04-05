<?php

namespace App\Http\Requests\API;

use App\Models\Lesson;
use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Validation\Rule;

class BraintreeTransactionRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()->hasRole(User::ROLE_STUDENT);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'payment_method_nonce'			=> ['required']
		];
    }
}
