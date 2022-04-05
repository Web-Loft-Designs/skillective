<?php

namespace App\Observers;

use App\Models\Lesson;
use Log;
use App\Repositories\UserRepository;
use App\Notifications\YouMayBeInterestedInLessonNotification;
use App\Notifications\YouMayBeInterestedInVirtualLessonNotification;

class LessonObserver
{
	/** @var  UserRepository */
	private $userRepository;

	public function __construct(UserRepository $userRepo) {
		$this->userRepository = $userRepo;
	}

	public function created(Lesson $lesson){
	    if ($lesson->private_for_student_id==null){
            if ($lesson->lesson_type=='virtual'){
                $students = $this->userRepository->getStudentsWhoMayBeInterestedInVirtualLesson($lesson);
                foreach ($students as $student) {
                    try{
                        $student->notify(new YouMayBeInterestedInVirtualLessonNotification($lesson));
                    }catch (\Exception $e){
                        Log::error("YouMayBeInterestedInVirtualLessonNotification Error for #{$lesson->id} user#{{$student->id}} : " . $e->getCode() . ': ' . $e->getMessage());
                    }
                }
            }else{
                $students = $this->userRepository->getStudentsWhoMayBeInterestedInRegularLesson($lesson);
                foreach ($students as $student) {
                    try{
                        $student->notify(new YouMayBeInterestedInLessonNotification($lesson));
                    }catch (\Exception $e){
                        Log::error("YouMayBeInterestedInLessonNotification Error for #{$lesson->id} user#{{$student->id}} : " . $e->getCode() . ': ' . $e->getMessage());
                    }
                }
            }
        }
	}
}
