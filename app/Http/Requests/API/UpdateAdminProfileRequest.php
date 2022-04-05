<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Models\Genre;
use Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class UpdateAdminProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()->hasRole('Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
		$rules = [
			'first_name'		=> ['required', 'string', 'max:255'],
			'last_name'			=> ['required', 'string', 'max:255'],
			'email'				=> ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::user()->id],
			'mobile_phone'		=> getMobilePhoneValidationRules()
			];

		return $rules;
    }
}
