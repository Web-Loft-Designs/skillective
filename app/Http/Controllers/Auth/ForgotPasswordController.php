<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\StudentMustFinishRegistration;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

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
     * @return JsonResponse|RedirectResponse
     * @throws ValidationException
     */
    public function sendResetLinkEmail(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $user = User::where('email', $request->email)->first();
        if ($user->finish_registration_token) {
            Notification::route('mail', $user->getEmail())->notify(new StudentMustFinishRegistration($user));
            return $request->wantsJson()
                ? new JsonResponse(['message' => 'Please check your email first and complete the registration.'], 406)
                : back()->with('status', 'Please check your email first and complete the registration.');
        }

        $this->validateEmail($request);
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }


    /**
     * @param Request $request
     * @return void
     */
    protected function validateEmail(Request $request): void
    {
        $request->validate(
            ['email' => 'required|email|email_active'],
            ['email_active'		=> 'Email doesn\'t exist or not active']
        );

	}
}
