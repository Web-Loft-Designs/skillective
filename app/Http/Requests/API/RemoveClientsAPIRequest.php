<?php

namespace App\Http\Requests\API;

use App\Models\Lesson;
use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Validation\Rule;

class RemoveClientsAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()->hasRole(User::ROLE_INSTRUCTOR);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
			'clients' => ['required', 'array'],
		];
		return $rules;
    }
}
