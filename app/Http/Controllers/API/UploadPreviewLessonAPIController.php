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
     * @return JsonResponse|int|void
     */
    public function uploadPreview()
    {
        if(Request::hasFile('preview')){
            if (Auth::check()) {
                $user = Auth::user();
                $file = Request::file('preview');
                $filename = md5(uniqid(rand(), true)) . '.' . $file->getClientOriginalExtension();
                $save_path = storage_path('app/public/lessons/'.$user->id.'/');
                File::makeDirectory($save_path, 0775, true, true);
                $file->move($save_path, $filename);

                return $this->sendResponse(config('app.url') . '/storage/' . 'lessons/' . $user->id . '/' . $filename);
            } else {
                return back()
                    ->withInput()
                    ->withErrors('unauthorized')
                    ->status(422);
            }

        }
    }
}
