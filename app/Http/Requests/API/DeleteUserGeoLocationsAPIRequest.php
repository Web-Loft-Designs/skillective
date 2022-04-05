<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use Illuminate\Http\Request;

class DeleteUserGeoLocationsAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
		return (
			Auth::user()->hasRole(User::ROLE_INSTRUCTOR) || Auth::user()->hasRole(User::ROLE_STUDENT)
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
		];
    }
}
