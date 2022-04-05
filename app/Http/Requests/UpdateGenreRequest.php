<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Genre;
use Auth;

class UpdateGenreRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
		$rules = Genre::$rules;

		$genreObj = Genre::find($this->genre);

        $rules['title'] = 'required|unique:genres,title,'.$this->route('genre');
		if ($genreObj->image!=''){
			unset($rules['image']);
		}

        return $rules;
    }
}
