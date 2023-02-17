<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;


class UploadPreviewLessonAPIController extends AppBaseController
{
    /**
     * @param Request $request
     * @return JsonResponse|never|void
     */
    public function uploadPreview(Request $request)
    {
        if(Request::hasFile('preview')){
            $user = Auth::user();
            if (!$user)
                return abort(404);

            $file = Request::file('preview');

            $filename = md5(uniqid(rand(), true)) . '.' . $file->getClientOriginalExtension();
            $save_path = storage_path('app/public/lessons/'.$user->id.'/');

            File::makeDirectory($save_path, 0775, true, true);
            $file->move($save_path, $filename);

            return $this->sendResponse(config('app.url') . '/storage/' . 'lessons/' . $user->id . '/' . $filename);
        }
    }
}
