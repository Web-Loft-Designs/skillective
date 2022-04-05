<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;
use App\Models\UserGeoLocation;
use Auth;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateInstructorGoalAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
		return (
			Auth::user()->hasRole(User::ROLE_INSTRUCTOR)
		);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'goal_value' => ['required', 'numeric', 'min:1'],
			'goal_color' => ['required', 'regex:/#([a-f0-9]{3}){1,2}\b/i']
		];
    }

//	public function messages()
//	{
//		return [
//
//		];
//	}
}
