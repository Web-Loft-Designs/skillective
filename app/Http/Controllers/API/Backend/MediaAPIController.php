<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\UploadMediaRequest;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaAPIController extends AppBaseController
{
    /**
     * @param UploadMediaRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(UploadMediaRequest $request, User $user) {
		$countUploadedMedia = $user->uploadMedia($request->all());
		$uploadedMedia = $user->getGalleryMedia($countUploadedMedia);

		return $this->sendResponse($uploadedMedia, 'You have successfully uploaded file(s).');
	}

    /**
     * @param Media $media
     * @return JsonResponse
     */
    public function destroy(Media $media) {
		$media->delete();
		return $this->sendResponse(null, 'Gallery item successfully deleted');
	}
}
