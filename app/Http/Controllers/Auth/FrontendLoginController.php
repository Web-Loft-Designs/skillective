<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\AppBaseController;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Cookie;


class FrontendLoginController extends AppBaseController
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

    private $cartRepository;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(cartRepository $cartRepo)
    {
        $this->middleware('guest')->except('logout');
        $this->cartRepository = $cartRepo;

        parent::__construct();
    }

	public function redirectTo()
	{

		if ( Auth::user()->hasRole(User::ROLE_INSTRUCTOR) )
        {

            return route('instructor.dashboard');//session('prev_page', route('instructor.dashboard'));

		}elseif ( Auth::user()->hasRole(User::ROLE_STUDENT) )
        {

            if (Cookie::has('redirect_after_auth_checkout'))
            {

                Cookie::queue(Cookie::forget('redirect_after_auth_checkout'));
                return route('checkout');

            }else{

                return route('student.dashboard'); //session()->exists('prev_page') ? session('prev_page') : route('student.dashboard');

            }

		}
	}



    public function showLoginForm()
    {
        $page_title = 'Login';
		session(['prev_page' => url()->previous()]);
        return view('auth.frontend-login', ['page_title'=>$page_title]);
    }

	protected function validateLogin(Request $request)
	{
		$request->validate([
			$this->username()	=> ['required', 'email', 'not_admin_email', 'email_active'],
			'password'			=> ['required', 'string']
		],
			[
				'not_admin_email'	=> 'Unable to login', // Not an Instructor/Student Email
				'email_active'		=> 'User doesn\'t exist or not active'
			]
		);
	}

    public function login(Request $request)
    {
        $this->validateLogin($request);
        if ($request->isXmlHttpRequest()){
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
                return $this->sendError('Too many login attempts. Try again later.', 401);
            }
            if ($this->attemptLogin($request)) {
                $this->redirectTo = $request->filled('redirect') ? $request->input('redirect') : $this->redirectTo();
                $this->cartRepository->storeGuestCart($request, false);
                return response()->json([
                    'redirect'  => $this->redirectTo,
                    'message'   => 'You have successfully logged in'
                ]);
            }else{
                return $this->sendError( 'Invalid email or password', 400);
            }
        }else{ // usual login
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }

            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        }
    }

}
