<?php

namespace App\Http\Controllers\API;

use App\Facades\TwilioVideo;
use App\Http\Controllers\AppBaseController;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class InstructorVirtualLessonRoomsController extends AppBaseController
{

    /**
     * @param Lesson $lesson
     * @return JsonResponse
     */
    public function completeRoom(Lesson $lesson)
    {
        if ($lesson->instructor_id!=Auth::id()){
            return $this->sendError('You are not authorized for this action');
        }

        $room = TwilioVideo::getRoom($lesson);
        if (!$room)
            return $this->sendError('Room doesn\'t exist or already completed');

        TwilioVideo::completeRoom($room);

        $lesson->update(['room_completed' => true]);

        return $this->sendResponse(true, 'Room completed');
    }

    /**
     * @param Lesson $lesson
     * @param $participantId
     * @return JsonResponse
     */
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
            Log::channel('twilio')->info( 'Client #' . $booking->student_id . ' disconnected from lesson #' . $booking->lesson_id);
            return $this->sendResponse(true, 'Participant disconnected');
        }else{
            return $this->sendError('Participant doesn\'t exist or already disconnected');
        }

    }

}