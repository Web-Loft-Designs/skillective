<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Log;

class CreatePreRLessonAPIRequest extends APIRequest
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
    $availableGenresIds  = Genre::select('id')->get()->pluck('id')->toArray();

    $documents = $request->input('documents', null);

    $documentsCount = count($documents);

    $rules = [
      'title' => ['required'],
      'description' => ['required'],
      'preview' => ['required'],
      'genre'      => ['required', Rule::in($availableGenresIds)],
      'price'  => ['required', 'numeric', 'min:0.99']
    ];

    if($documentsCount === 0){
      $rules['video'] = ['required'];
    }

    return $rules;
  }

  public function messages() {
		return [
			'video.required' => 'You must upload video or documents, or both',
      'price.min' => 'Minimum lesson price must be greater than $0.99',
		];
	}
}
