<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Request;
use App\Models\User;
use Auth;
use File;

use Log;

class UploadVideoAPIController extends AppBaseController
{
    public function upload(Request $request)
    {
        if(Request::hasFile('uploaded_video')){

            $user = Auth::user();

            if (!$user)
                return abort(404);

            $file = Request::file('uploaded_video');

            $filename = md5(uniqid(rand(), true)) . '.' . $file->getClientOriginalExtension();
            $save_path = storage_path('app/public/videos/'.$user->id.'/');

            File::makeDirectory($save_path, 0775, true, true);
            $file->move($save_path, $filename);
            
            return $this->sendResponse(config('app.url') . '/storage/' . 'videos/' . $user->id . '/' . $filename);
        }
    }
}