<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');

		parent::__construct();
    }

    /**
     * @param Request $request
     * @return void
     */
    protected function validateEmail(Request $request)
	{
		$request->validate(
			['email' => 'required|email|email_active'],
			['email_active'		=> 'Email doesn\'t exist or not active'] // TODO: send email to finish registration if not finished
		);
	}
}
