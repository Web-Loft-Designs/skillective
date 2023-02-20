<?php

namespace App\Http\Controllers;

use App\Facades\TwilioVideo;
use Braintree\MerchantAccount;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class VirtualLessonPageController extends Controller
{

    /**
     * @param Lesson $lesson
     * @param Request $request
     * @return Application|Factory|View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
				|| $lesson->instructor->bt_submerchant_status!=MerchantAccount::STATUS_ACTIVE
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
