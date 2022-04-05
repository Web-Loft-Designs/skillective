<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;

class InstructorsFilterRequest extends FormRequest
{
    public function authorize()
    {
		return true;
    }

    public function rules()
    {
		return [

			];
    }
}
