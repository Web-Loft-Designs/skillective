<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentAddClientListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'instructor_id' => ['required', 'numeric', Rule::exists('users', 'id')]
        ];
    }
}
