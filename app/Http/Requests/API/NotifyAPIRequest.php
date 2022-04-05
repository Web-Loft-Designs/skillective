<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Validation\Rule;

class NotifyAPIRequest extends FormRequest
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
		$availableNotificationMethods = array_keys(Profile::getAvailableNotificationMethods());

		$rules = [
			'users'								=> ['required', 'array'],
			'message'							=> ['required'],
//			'notification_methods'				=> ['required', 'array'],
//			'notification_methods.*'			=> [Rule::in( $availableNotificationMethods )],
		];

//		if (Auth::user()->hasRole(User::ROLE_STUDENT)){
//			unset($rules['notification_methods']);
//			unset($rules['notification_methods.*']);
//		}

		return $rules;
    }
}
