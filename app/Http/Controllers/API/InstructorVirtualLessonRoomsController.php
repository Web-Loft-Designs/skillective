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
use Illuminate\Http\Request;
//use App\Http\Requests\API\GetTwilioAccessTokenRequest;
use TwilioVideo;
use Auth;

class InstructorVirtualLessonRoomsController extends AppBaseController
{
//    public function createRoom(Lesson $lesson)
//    {
//        if (!TwilioVideo::isValidTimeForLessonStart($lesson->start, $lesson->end, $lesson->timezone_id)){
//            return $this->sendError('It\'s not the time to start a virtual lesson');
//        }
//        if ($lesson->instructor_id!=Auth::id()){
//            return $this->sendError('You are not authorized for this action');
//        }
//
//        $roomName = TwilioVideo::getRoomNameForLesson($lesson);
//        if ( empty($room = TwilioVideo::getRoom($lesson)) ) {
//            try {
//                $room = TwilioVideo::createRoom($lesson);
//            } catch (Exception $e) {
//                return $this->sendError("Error: " . $e->getMessage());
//            }
//            $lesson->update(['room_sid' => $room->sid]);
//            \Log::debug("created new room: ".$roomName);
//        }else{
//            \Log::debug('room already exists');
//        }
//
//
//        return $this->sendResponse( ['instructorName' => Auth::user()->getName(), 'genreTitle'=>$lesson->genre->title] );
////        $lifetime   = 60;
////        $identity   = TwilioVideo::getInstructorParticipantIdentity(Auth::user());
////        $token      = TwilioVideo::getAccessToken($identity, $roomName, $lifetime);
////
////        return $this->sendResponse( TwilioVideo::getRoomSettings($token, $lesson) );
//    }

    public function completeRoom(Lesson $lesson)
    {
        if ($lesson->instructor_id!=Auth::id()){
            return $this->sendError('You are not authorized for this action');
        }

//        if ($lesson->room_completed==true){
//            return $this->sendError('Room already completed');
//        }

        $room = TwilioVideo::getRoom($lesson);
        if (!$room)
            return $this->sendError('Room doesn\'t exist or already completed');

        TwilioVideo::completeRoom($room);

        $lesson->update(['room_completed' => true]);

        return $this->sendResponse(true, 'Room completed');
    }

    public function disconnectParticipantFromRoom(Lesson $lesson, $participantId)
    {
        if ($lesson->instructor_id!=Auth::id()){
            return $this->sendError('You are not authorized for this action');
        }

        $room = TwilioVideo::getRoom($lesson);
        if (!$room)
            return $this->sendError('Room doesn\'t exist or already completed');

        $booking = $lesson->bookings()->where('student_id', $participantId)->first();
        if ($booking){
            $identity = TwilioVideo::getStudentParticipantIdentity($booking->student);
            TwilioVideo::disconnectParticipant($room, $identity);
            $booking->update(['disconnected' =>true]);
            \Log::channel('twilio')->info( 'Client #' . $booking->student_id . ' disconnected from lesson #' . $booking->lesson_id);
            return $this->sendResponse(true, 'Participant disconnected');
        }else{
            return $this->sendError('Participant doesn\'t exist or already disconnected');
        }

    }

//    public function getTwilioAccessToken(  GetTwilioAccessTokenRequest $request )
//    {
//        $roomName = TwilioVideo::getRoomNameForLesson($lesson);
//        $lifetime   = 60;
//        $identity   = TwilioVideo::getInstructorParticipantIdentity(Auth::user());
//        $token      = TwilioVideo::getAccessToken($identity, $roomName, $lifetime);
//        return $this->sendResponse(['token'=>$token]);
//    }
}