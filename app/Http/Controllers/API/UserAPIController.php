<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\FinishUserAPIRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\UpdateProfilePasswordRequest;
use App\Http\Requests\API\FinishUserRegistrationRequest;
use App\Http\Requests\API\UpdateProfileRequest;
use App\Http\Requests\API\UpdateProfileNotificationMethodsRequest;
use Response;
use Auth;
/**
 * Class UserController
 * @package App\Http\Controllers\API
 */

class UserAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

	public function getUserData(Request $request){
		$user = Auth::user();
		$userData = $this->userRepository->getUserData(Auth::user()->id);
		$isInstructor = false;
		if ($user->hasRole(User::ROLE_INSTRUCTOR)) {
			$isInstructor = true;
		}else{

		}
		$userData['isInstructor']		 = $isInstructor;
		$userData['isStudent']			 = !$isInstructor;

		return response()->json($userData);
	}

//	public function finishUserRegistration(FinishUserRegistrationRequest $request)
//	{
//		if (!$request->filled('email') || !$request->filled('token')) {
//			return $this->sendError('Unable to process your request.', 400);
//		}
//
//		$user = $this->userRepository->getByFinishRegistrationToken($request->input('token'), $request->input('email'));
//
//		if (!$user)
//			return $this->sendError('Unable to process your request.', 404);
//
//		$user->update(['password'=>$request->input('password')]);
//		$user->setFinishRegistrationToken('');
//		$user->setStatus(User::STATUS_ACTIVE);
//
//		Auth::login($user);
//
//		return $this->sendResponse(['redirect'=>route('student.dashboard')]);
//
////		$user = User::query()
////					->where('email', $request->get('email'))
////					->where('token', $request->get('token'))
////					->firstOrFail();
////
////		$user->fill(['password' => $request->get('password')]);
////		$user->save();
////
////		return response()->json($user);
//	}

	public function updatePassword(UpdateProfilePasswordRequest $request, User $user = null)
	{
		if (!Auth::user()->hasRole(User::ROLE_ADMIN) || $user==null){
			$user = Auth::user();
		}
		if (!$user)
			return abort(404);

		$user->changePassword($request->input('new_password'));
		return $this->sendResponse(true, 'Password updated');
	}

	public function updateProfile(UpdateProfileRequest $request, User $user = null, UserRepository $user_repository)
	{
		if (!Auth::user()->hasRole(User::ROLE_ADMIN) || $user==null){
			$user = Auth::user();
		}

		$user_repository->updateUserData($user->id, $request);

		return $this->sendResponse(true, 'Personal Info updated');
	}

	public function updateNotificationMethods(UpdateProfileNotificationMethodsRequest $request, User $user = null)
	{
		if (!Auth::user()->hasRole(User::ROLE_ADMIN) || $user==null){
			$user = Auth::user();
		}
		if (!$user)
			return abort(404);

		$user->profile->update($request->only([
			'notification_methods'
		]));

		return $this->sendResponse(true, 'Personal Info updated');
	}
}
