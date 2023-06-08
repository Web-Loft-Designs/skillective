<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class NotifyAPIRequest extends FormRequest
{

    /**
     * @return Authenticatable|null
     */
    public function authorize()
    {
		return Auth::user();
    }


    /**
     * @return array[]
     */
    public function rules()
    {
		$rules = [
			'users'								=> ['required', 'array'],
			'message'							=> ['required'],
		];
        return $rules;
    }
}
