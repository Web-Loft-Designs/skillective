<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UpdateProfileImageRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()!=null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
    	$fileRules = 'mimes:jpeg,png,jpg|max:8192';
        $rules = [
        	'profile_image' => "required|{$fileRules}"
		];
        return $rules;
    }
}
