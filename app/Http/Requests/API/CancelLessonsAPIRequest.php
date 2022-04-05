<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;

class CancelLessonsAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()->hasRole(User::ROLE_INSTRUCTOR) || Auth::user()->hasRole(User::ROLE_ADMIN);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
			'lessons' => ['required', 'array'],
		];
		return $rules;
    }
}
