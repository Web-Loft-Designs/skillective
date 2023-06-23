<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;

class UploadVideoAPIController extends AppBaseController
{

    /**
     * @param Request $request
     * @return JsonResponse|never|void
     */
    public function upload(Request $request)
    {
        if(Request::hasFile('uploaded_video')){

            $user = Auth::user();

            $file = Request::file('uploaded_video');

            if ($file->getClientMimeType() == 'application/pdf') {
                $filename = $file->getClientOriginalName();
            } else {
                $filename = md5(uniqid(rand(), true)) . '.' . $file->getClientOriginalExtension();
            }
            $save_path = storage_path('app/public/videos/'.$user->id.'/');
            File::makeDirectory($save_path, 0775, true, true);
            $file->move($save_path, $filename);
            
            return $this->sendResponse(config('app.url') . '/storage/' . 'videos/' . $user->id . '/' . $filename);
        }
    }
}