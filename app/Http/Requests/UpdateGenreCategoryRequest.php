<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\GenreCategory;
use Auth;

class UpdateGenreCategoryRequest extends FormRequest
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
		$rules = GenreCategory::$rules;

        $rules['title'] = 'required|unique:genre_categories,title,'.$this->route('genreCategory');

        return $rules;
    }
}
