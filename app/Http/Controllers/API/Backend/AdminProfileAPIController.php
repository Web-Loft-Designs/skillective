<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\UpdateProfilePasswordRequest;
use App\Http\Requests\API\UpdateAdminProfileRequest;
use Response;
use Auth;

class AdminProfileAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

	public function updatePassword(UpdateProfilePasswordRequest $request, User $user = null)
	{
		if (!Auth::user()->hasRole(User::ROLE_ADMIN)){
			$user = Auth::user();
		}
		if (!$user)
			return abort(404);

		$user->update(['password'=>$request->input('new_password')]);
		return $this->sendResponse(true, 'Password updated');
	}

	public function updateProfile(UpdateAdminProfileRequest $request)
	{
		$user = Auth::user();

		$user->update($request->only([
			'first_name',
			'last_name',
			'email',
		]));

		$user->profile->update($request->only([
			'mobile_phone'
		]));

		return $this->sendResponse(true, 'Personal Info updated');
	}
}
