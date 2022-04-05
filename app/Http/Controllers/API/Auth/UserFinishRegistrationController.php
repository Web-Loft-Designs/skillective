<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\FinishStudentRegistrationRequest;
use Auth;
use Log;
use Cookie;

class UserFinishRegistrationController extends AppBaseController
{
	/** @var  UserRepository */
	private $userRepository;

	public function __construct(UserRepository $userRepo) {
		$this->userRepository = $userRepo;
		parent::__construct();
	}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function exists(UserRepository $userRepository, Request $request)
    {
		if (!$request->filled('email') || !$request->filled('token')) {
			return $this->sendError('Unable to process your request.', 400);
		}

		$user = $this->userRepository->getByFinishRegistrationToken();

		if (!$user)
			return $this->sendError('Unable to process your request.', 404);

		return $this->sendResponse(true);
    }

	public function finishRegistration(FinishStudentRegistrationRequest $request)
	{
		if (!$request->filled('email') || !$request->filled('token')) {
			return $this->sendError('Unable to process your request.', 400);
		}

		$user = $this->userRepository->getByFinishRegistrationToken($request->input('token'), $request->input('email'));

		if (!$user)
			return $this->sendError('Unable to process your request.', 404);

		$user->update(['password'=>$request->input('password'), 'accepted_invitation_id' => null]);
		$user->setFinishRegistrationToken('');
		$user->setStatus(User::STATUS_ACTIVE);

		Auth::login($user);

		if ($user->hasRole(User::ROLE_INSTRUCTOR))
			$redirect = route('instructor.dashboard');
		else{
            if ( Cookie::has('backToRequestLesson') ){
                $redirect = route('profile', ['user' => Cookie::get('backToRequestLesson')]);
            }else{
                $redirect = route('student.dashboard');
            }
        }

		return $this->sendResponse(['redirect'=>$redirect]);
	}
}
