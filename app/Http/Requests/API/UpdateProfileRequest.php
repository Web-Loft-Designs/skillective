<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Models\Genre;
use Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class UpdateProfileRequest extends FormRequest
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
    public function rules(Request $request)
    {
    	$availableGenresIds	= Genre::select('id')->get()->pluck('id')->toArray();

    	$editableUserId = $request->route('user')!=null?$request->route('user')->id : Auth::user()->id;

		$rules = [
			'first_name'		=> ['required', 'string', 'max:255'],
			'last_name'			=> ['required', 'string', 'max:255'],
//			'instagram_handle'	=> ['required', 'string', 'max:255'],
			'email'				=> ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$editableUserId],
//			'address'			=> ['required', 'string', 'max:255'],
			'city'				=> getCityValidationRules($request),
			'state'				=> ['required', 'string', 'max:255', 'valid_us_state'],
			'zip'				=> getPostCodeValidationRules(),
			'dob'				=> getDOBValidationRules(),
			'mobile_phone'		=> getMobilePhoneValidationRules(),
			'genres'			=> ['required', 'array'],
			'genres.*'			=> [Rule::in( $availableGenresIds )],
			'about_me'			=> getAboutMeValidationRules(),
			];

		if (User::find($editableUserId)->hasRole(User::ROLE_STUDENT)){
			$rules['gender'] = ['required', Rule::in( getGenders() )];
			unset($rules['about_me']);
        }else{
            $rules['lesson_block_min_price'] = ['required', 'numeric', 'min:0'];
        }

		return $rules;
    }

	public function messages()
	{
		$mesages = [];
		$mesages = array_merge($mesages, getDOBValidationMessages(), getAboutMeValidationMessages(), getCityValidationMessages());
		$messages['lesson_block_min_price.required'] = 'Min price per 30 minute lesson required';
		$messages['lesson_block_min_price.numeric'] = 'Min price per 30 minute lesson must be a numeric value';
		$messages['lesson_block_min_price.min'] = 'Min price per 30 minute lesson must be positive number';
		return $mesages;
	}
}
