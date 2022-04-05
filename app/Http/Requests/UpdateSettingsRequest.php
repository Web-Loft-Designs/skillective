<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Profile;
use App\Models\User;

class UpdateSettingsRequest extends FormRequest
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
            'settings.skillective_service_fee_fixed' => ['required', 'numeric'],
            'settings.skillective_service_fee_percent' => ['required', 'numeric'],
            'settings.braintree_processing_fee' => ['required', 'numeric'],
            'settings.braintree_transaction_fee' => ['required', 'numeric'],
            'settings.twilio_small_group_fee' => ['required', 'numeric'],
            'settings.twilio_group_fee' => ['required', 'numeric'],
        ];

        return $rules;
    }
}
