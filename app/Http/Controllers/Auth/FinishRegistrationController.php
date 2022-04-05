<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use Flash;

class FinishRegistrationController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserRepository $userRepository, Request $request)
    {
		if (! $request->filled('email') || ! $request->filled('token')) {
			Flash::error('Unable to process your request.')->important();
		}

		$user = $userRepository->getByFinishRegistrationToken($request->input('token'), $request->input('email'));

		abort_if(! $user, 404);

		return view('auth.finish-registration', [
			'user' => $userRepository->presentResponse($user)['data']
		]);
    }
}
