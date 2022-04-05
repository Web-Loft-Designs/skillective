<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\User;
use Auth;
use Log;
use TwilioVideo;

class VirtualLessonPageController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Lesson $lesson, Request $request)
    {
        $lessonAccessToken = session()->get('lesson-access-token');

        if (
            empty($lessonAccessToken)
            || empty($request->get('lat'))
            || $lessonAccessToken != $request->get('lat')
        ){
            session()->forget('lesson-access-token');
            abort(403, 'You are not authorized for this action');
        }
        session()->forget('lesson-access-token');

        $error = '';
    	if ($lesson->is_cancelled){
            $error = 'This Lesson was cancelled';
		}

        if ( $lesson->instructor_id != Auth::id()
            &&
            $lesson->students()->where('users.id', Auth::id())->first()==null
        ){
            $error = 'You are not authorized for this action';
        }

		if (config('app.env')=='prod'
			&& (
				$lesson->instructor->bt_submerchant_id==null
				|| $lesson->instructor->bt_submerchant_status!=\Braintree_MerchantAccount::STATUS_ACTIVE
				|| $lesson->instructor->status != User::STATUS_ACTIVE
			)
		){
			abort(404, 'Lesson not found');
		}

        if ( empty($room = TwilioVideo::getRoom($lesson)) ) {
            $error = 'Can\'t connect to this room';
        }

        return view('frontend.virtual-lesson', ['roomError' => $error, 'lessonId' => $lesson->id]);
    }
}
