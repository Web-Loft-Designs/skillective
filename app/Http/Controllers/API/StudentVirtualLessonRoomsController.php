<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 30.03.2020
 * Time: 19:46
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Lesson;
use App\Models\Booking;
use TwilioVideo;
use Auth;
use Log;

class StudentVirtualLessonRoomsController extends AppBaseController
{
    public function get()
    {
        $rooms = [];
        if (config('app.env')!='prod'){
            Booking::whereRaw(' (bookings.student_id = '.Auth::id().' ) OR ( bookings.instructor_id = '.Auth::id().' )')
                ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
//                ->where('lessons.start', '>', date('Y-m-d'))
                ->where('lessons.room_completed', '<>', true)
                ->whereNotNull('lessons.room_sid')
                ->with('lesson')
                ->get()
                ->each(function($booking) use (&$rooms){
                    $room = TwilioVideo::getRoom( $booking->lesson );
                    if ($room){
                        $room->available = $booking->disconnected==0;
                        $rooms[] = $room;
                    }
                });
        }

        return $this->sendResponse($rooms);
    }

//    public function connect(Lesson $lesson)
//    {
//        if (!($participant = $lesson->students()->where('id', Auth::id())->first())){
//            return $this->sendError('You are not authorized for this action');
//        }
//
//        if ($participant->disconnected)
//            return $this->sendError('You were disconnected from this lesson');
//
//        $roomName = TwilioVideo::getRoomNameForLesson($lesson);
//
//        if ( empty($room = TwilioVideo::getRoom($lesson)) ) {
//            return $this->sendError('Can\'t connect to this room');
//        }
//
//        $lifetime = 1;
//        $identity = TwilioVideo::getStudentParticipantIdentity(Auth::user());
//        $token = TwilioVideo::getAccessToken($identity, $roomName, $lifetime);
//        return $this->sendResponse( TwilioVideo::getRoomSettings($token, $lesson) );
//    }
}