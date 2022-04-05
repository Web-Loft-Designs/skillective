<?php

namespace App\Observers;

use App\Models\LessonRequest;
use App\Models\User;
use Log;
use Auth;
use App\Repositories\LessonRequestRepository;
use App\Notifications\LessonRequest\LessonRequestApprovedNotification;
use App\Notifications\LessonRequest\LessonRequestCancelledNotification;
use App\Notifications\LessonRequest\LessonRequestCreatedNotification;

class LessonRequestObserver
{
	/** @var  LessonRequestRepository */
	private $lessonRequestRepository;

	public function __construct(LessonRequestRepository $lessonRequestRepository) {
		$this->lessonRequestRepository = $lessonRequestRepository;
	}

	public function created(LessonRequest $lessonRequest){
	    if ($lessonRequest->status==LessonRequest::STATUS_PENDING){
            try{
                $lessonRequest->instructor->notify(new LessonRequestCreatedNotification($lessonRequest));
            }catch (\Exception $e){
                Log::error("LessonRequestCreatedNotification Error for #{$lessonRequest->id} : " . $e->getCode() . ': ' . $e->getMessage());
            }
        }
	}

	public function updated(LessonRequest $lessonRequest){
        if ($lessonRequest->status==LessonRequest::STATUS_APPROVED){
//            try{
                $lessonRequest->student->notify(new LessonRequestApprovedNotification($lessonRequest));
//            }catch (\Exception $e){
//                Log::error("LessonRequestApprovedNotification Error for #{$lessonRequest->id} : " . $e->getCode() . ': ' . $e->getMessage());
//            }
        }elseif ($lessonRequest->status==LessonRequest::STATUS_CANCELLED){
            try{
                if (Auth::user() && Auth::user()->hasRole(User::ROLE_STUDENT)){
                    $lessonRequest->instructor->notify(new LessonRequestCancelledNotification($lessonRequest));
                }else{
                    Log::info('notify student', (array)$lessonRequest);
                    $lessonRequest->student->notify(new LessonRequestCancelledNotification($lessonRequest));
                }
            }catch (\Exception $e){
                Log::error("LessonRequestCancelledNotification Error for #{$lessonRequest->id} : " . $e->getCode() . ': ' . $e->getMessage());
            }
        }
    }
}
