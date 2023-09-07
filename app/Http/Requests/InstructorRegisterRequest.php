<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class InstructorRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
		return !Auth::user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request): array
    {
    	$availableGenresIds	= Genre::select('id')->get()->pluck('id')->toArray();

		return [
			'first_name'		=> ['required', 'string', 'max:255'],
			'last_name'			=> ['required', 'string', 'max:255'],
//			'instagram_handle'	=> ['required', 'string', 'max:255'],
			'email'				=> ['required', 'string', 'email', 'max:255', 'unique:users'],
//			'address'			=> ['required', 'string', 'max:255'],
			'city'				=> getCityValidationRules($request),
			'state'				=> ['required', 'string', 'max:255', 'valid_us_state'],
			'zip'				=> getPostCodeValidationRules(),
			'dob'				=> getDOBValidationRules(),
			'mobile_phone'		=> getMobilePhoneValidationRules(),
			'genres'			=> ['required', 'array'],
			'genres.*'			=> [Rule::in( $availableGenresIds )],
			'gender'			=> ['required', Rule::in( getGenders() )],
			'about_me'			=> getAboutMeValidationRules(),
			'accept_terms'		=> ['accepted'],
			'provider'			=> ['required', Rule::in( User::ALLOWED_SM_PROVIDERS )],
			];
    }

	public function messages(): array
    {
		$mesages = [
			'accept_terms.accepted'	=> "Agree to our terms please",
		];
		$mesages = array_merge($mesages, getDOBValidationMessages(), getAboutMeValidationMessages(), getCityValidationMessages());
		return $mesages;
	}
}
