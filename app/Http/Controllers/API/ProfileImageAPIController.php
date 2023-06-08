<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\UpdateProfileImageRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileImageAPIController extends AppBaseController
{

    /**
     * @param UpdateProfileImageRequest $request
     * @param User|null $user
     * @return JsonResponse|never
     */
    public function update(UpdateProfileImageRequest $request, User $user = null) {
		if (!Auth::user()->hasRole(User::ROLE_ADMIN) || $user==null){
			$user = Auth::user();
		}
		if (!$user)
			return abort(404);

//		$profile_image = Input::file('profile_image');
		$profile_image = $request->file('profile_image');
		$profile_image_path = $user->profile->updateProfileImage($profile_image);

		return $this->sendResponse($profile_image_path, 'You have successfully uploaded profile image.');
	}

    /**
     * @param User|null $user
     * @return JsonResponse|never
     */
    public function delete(User $user = null)
	{
		if (!Auth::user()->hasRole(User::ROLE_ADMIN) || $user==null){
			$user = Auth::user();
		}
		if (!$user)
			return abort(404);

		if ( $user->profile->avatar ){
			$result = $user->profile->deleteOldImage();
			if ($result==true)
				return $this->sendResponse($user->profile->getImageUrl(), 'Profile image successfully deleted');
		}
		return $this->sendError('Profile image not found');
	}
}