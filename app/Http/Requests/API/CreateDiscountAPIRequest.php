<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Log;

class CreateDiscountAPIRequest extends APIRequest
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
  public function rules(Request $request)
  {

    $rules = [
      'title' => ['required'],
      'start'      => ['required', 'date_format:Y-m-d', 'future_date'],
      'finish'      => ['required', 'date_format:Y-m-d', 'future_date'],
      'discount'      => ['required'],
    ];

    return $rules;
  }

  public function messages()
  {
    return [
      'start.future_date' => 'Select future date',
      'finish.future_date' => 'Select future date',
    ];
  }
}
