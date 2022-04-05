<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\FaqCategory;
use Auth;

class UpdateFaqCategoryRequest extends FormRequest
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
		$rules = FaqCategory::$rules;
        $rules['title'] = 'required|unique:faq_categories,title,'.$this->route('faq_category');

        return $rules;
    }
}
