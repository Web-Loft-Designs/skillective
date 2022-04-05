<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UploadMediaRequest extends FormRequest
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
    	$fileRules = 'mimes:mp4,mov,qt,jpeg,png,jpg|max:20480';
        $rules = [
        	'media' => 'required'
		];

        if ($this->has('media.0'))
			$rules['media.*'] = $fileRules;
        else
			$rules['media'] .= '|'.$fileRules;

        return $rules;
    }

	public function messages()
	{
		return [
			'media.max'	=> 'Max file size to upload is 20Mb',
			'media.*.max'	=> 'Max file size to upload is 20Mb',
		];
	}
}
