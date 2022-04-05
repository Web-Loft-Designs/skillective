<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;
use App\Models\UserGeoLocation;
use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Log;

class UpdateUserGeoLocationsAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Request $request)
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
		Log::info('tttt');

        return [
			'*.location' => ['required', 'is_exact_address'],
			'*.limit' => ['required', 'integer', 'valid_limit'],
			'*.date_from' => ['required', 'date_format:Y-m-d'],
			'*.date_to' => ['required', 'date_format:Y-m-d'],
		];
    }

	public function messages()
	{
		return [
			'*.limit.required'	=> "Limit required",
			'*.limit.integer'	=> "Limit invalid",
			'*.limit.valid_limit'	=> "Limit invalid",
			'*.limit.valid_limit'	=> "Limit invalid",
			'*.location.is_exact_address' => 'Location address should be clarified',
			'*.date_from.required' => 'Date from required',
			'*.date_to.required' => 'Date to required',
			'*.date_from.date_format' => 'Invalid date format',
			'*.date_to.date_format' => 'Invalid date format',
			'*.date_from.before' => 'End Date must be a date before Start Date',
		];
	}
}
