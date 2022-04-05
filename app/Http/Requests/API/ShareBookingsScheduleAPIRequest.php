<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;

class ShareBookingsScheduleAPIRequest extends FormRequest
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
		return [
			'email'				=> ['required', 'email', 'max:255'],
			];
    }
}
