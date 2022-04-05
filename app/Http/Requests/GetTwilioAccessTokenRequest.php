<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 31.03.2020
 * Time: 12:47
 */

namespace App\Http\Requests\API;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class GetTwilioAccessTokenRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::user();
    }

    public function rules(){
        return [
            'identity'		=> ['required', 'string', 'max:255'],
        ];
    }
}