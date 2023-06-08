<?php

namespace App\Http\Controllers\API;

use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\UploadMediaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class MediaAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        parent::__construct();
        $this->userRepository = $userRepo;
    }

    /**
     * @param $user
     * @return array|JsonResponse
     */
    public function index($user)
	{
		$user = $this->userRepository->findWithoutFail($user);
		if ($user){
			return $user->getGalleryMedia();
		}
		return $this->sendError('Bad Request', 404);
	}

	public function store(UploadMediaRequest $request) {
    	$currentUser 	= Auth::user();

		$countUploadedMedia = $currentUser->uploadMedia($request->all());
		$uploadedMedia = $currentUser->getGalleryMedia($countUploadedMedia);

		return $this->sendResponse($uploadedMedia, 'You have successfully uploaded file(s).');
	}

	public function destroy(Media $media)
	{
		if ( $media && ($media->model_id==Auth::user()->id ) ){
			$media->delete();
			return $this->sendResponse(null, 'Gallery item successfully deleted');
		}
		return $this->sendError('Media not found');
	}
}
