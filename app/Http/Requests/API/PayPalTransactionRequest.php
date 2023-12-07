<?php

namespace App\Http\Requests\API;

use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Request\APIRequest;
use App\Models\User;

class PayPalTransactionRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
		return Auth::user()->hasRole(User::ROLE_STUDENT);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
			'payment_method_nonce' => ['required','string','max:255']
		];
    }
}
