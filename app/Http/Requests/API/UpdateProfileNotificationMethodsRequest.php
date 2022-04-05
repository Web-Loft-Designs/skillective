<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Profile;
use Auth;
use Illuminate\Validation\Rule;

class UpdateProfileNotificationMethodsRequest extends FormRequest
{
	private $maxWordsAbout = 300;
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

		return [
			'notification_methods'				=> ['required', 'array'],
			'notification_methods.*'			=> [Rule::in( $availableNotificationMethods )],
			];
    }
}
