<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class BackendLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/backend';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
		parent::__construct();
    }

	public function showLoginForm(Request $request)
	{
		$page_title = 'Login';
		return view('auth.login', ['page_title'=>$page_title]);
	}

	public function redirectTo()
	{
		return session('prev_admin_page', route('backend.dashboard'));
	}

	protected function validateLogin(Request $request)
	{
		$request->validate([
			$this->username()	=> ['required', 'email', 'is_admin_email', 'email_active'],
			'password'			=> ['required', 'string']
		],
			[
				'is_admin_email'	=> 'Not an Admin Email',
				'email_active'		=> 'User doesn\'t exist or not active'
			]
		);
	}
}
