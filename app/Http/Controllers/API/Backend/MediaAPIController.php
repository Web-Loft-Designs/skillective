<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;
use App\Http\Requests\API\UploadMediaRequest;
use Spatie\MediaLibrary\Models\Media;

class MediaAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo) {
        $this->userRepository = $userRepo;
    }

	public function store(UploadMediaRequest $request, User $user) {
		$countUploadedMedia = $user->uploadMedia($request->all());
		$uploadedMedia = $user->getGalleryMedia($countUploadedMedia);

		return $this->sendResponse($uploadedMedia, 'You have successfully uploaded file(s).');
	}

	public function destroy(Media $media) {
		$media->delete();
		return $this->sendResponse(null, 'Gallery item successfully deleted');
	}
}
