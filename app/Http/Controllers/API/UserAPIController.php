<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\UpdateProfilePasswordRequest;
use App\Http\Requests\API\UpdateProfileRequest;
use App\Http\Requests\API\UpdateProfileNotificationMethodsRequest;
use Illuminate\Support\Facades\Auth;


class UserAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
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


    /**
     * @param UpdateProfilePasswordRequest $request
     * @param User|null $user
     * @return JsonResponse|never
     */
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

    /**
     * @param UpdateProfileRequest $request
     * @param User|null $user
     * @param UserRepository $user_repository
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request, User $user = null, UserRepository $user_repository)
	{
		if (!Auth::user()->hasRole(User::ROLE_ADMIN) || $user==null){
			$user = Auth::user();
		}

		$user_repository->updateUserData($user->id, $request);

		return $this->sendResponse(true, 'Personal Info updated');
	}

    /**
     * @param UpdateProfileNotificationMethodsRequest $request
     * @param User|null $user
     * @return JsonResponse|never
     */
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
