<?php

namespace App\Http\Requests\API;


use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateCountInvitesRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()->hasRole(User::ROLE_ADMIN);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
			'max_allowed_instructor_invites'	=> ['sometimes', 'nullable', 'integer', 'min:0'],
		];

		return $rules;
    }

    public function messages() {
		return [
			'max_allowed_instructor_invites.integer' => 'The value must be a numeric or not presented',
			'max_allowed_instructor_invites.min' => 'The value must be positive or not presented'
		];
	}
}
