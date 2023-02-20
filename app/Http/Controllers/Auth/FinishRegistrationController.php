<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;

class FinishRegistrationController extends Controller
{

    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Application|Factory|View
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
