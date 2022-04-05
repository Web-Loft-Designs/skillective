<?php

namespace App\Http\Requests\API;

use App\Models\LessonRequest;
use App\Models\Lesson;
use Illuminate\Http\Request;
use InfyOm\Generator\Request\APIRequest;
use Auth;
use App\Models\User;
use Illuminate\Validation\Rule;

class CancelLessonRequestAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		return Auth::user()->hasRole(User::ROLE_STUDENT) || Auth::user()->hasRole(User::ROLE_INSTRUCTOR);
    }

    public function rules(){
        return [];
    }
}
