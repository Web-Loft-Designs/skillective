<?php

namespace App\Http\Controllers\API;

use App\Facades\TwilioVideo;
use App\Http\Controllers\AppBaseController;
use App\Models\Booking;
use App\Models\Lesson;
use App\Models\User;
use Braintree\MerchantAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VirtualLessonRoomsController extends AppBaseController
{
    public function getList()
    {
        $rooms = [];
        if (config('app.env')!='prod'){

            Booking::whereRaw(' (bookings.student_id = '.Auth::id().' ) OR ( bookings.instructor_id = '.Auth::id().' )')
                ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
                ->whereRaw('( lessons.room_completed <> 1 OR lessons.room_completed IS NULL )')
                ->whereNotNull('lessons.room_sid')
                ->with('lesson')
                ->groupBy('bookings.lesson_id')
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

    /**
     * @param Lesson $lesson
     * @return JsonResponse
     */
    public function getRoomConnectSettings(Lesson $lesson){

        $error = $this->_validateAccess($lesson);
        if (!empty($error)){
            return $this->sendError($error);
        }

        $roomName = TwilioVideo::getRoomNameForLesson($lesson);
        $lifetime = 1;
        $identity = Auth::user()->hasRole('Instructor') ? TwilioVideo::getInstructorParticipantIdentity(Auth::user()) : TwilioVideo::getStudentParticipantIdentity(Auth::user());
        $token    = TwilioVideo::getAccessToken($identity, $roomName, $lifetime);

        return response()->json(TwilioVideo::getRoomSettings($token, $lesson));
    }

    /**
     * @param Lesson $lesson
     * @return JsonResponse
     */
    public function getLessonAccessToken(Lesson $lesson)
    {
        $error = $this->_validateAccess($lesson);
        if (!empty($error)){
            return $this->sendError($error);
        }
        $roomName = TwilioVideo::getRoomNameForLesson($lesson);
        if ( empty($room = TwilioVideo::getRoom($lesson)) ) {
            try {
                $room = TwilioVideo::createRoom($lesson);
            } catch (\Exception $e) {
                return $this->sendError("Can't connect to this room: " . $e->getMessage());
            }
            $lesson->update(['room_sid' => $room->sid]);
            Log::debug("created new room: ".$roomName);
        }else{
            Log::debug('room already exists');
        }

        $let = Str::random(60);
        session()->forget('lesson-access-token', $let);
        session()->put('lesson-access-token', $let);
        return response()->json(['lat' => $let]);
    }

    /**
     * @param $lesson
     * @return string|null
     */
    private function _validateAccess($lesson)
    {
        if ($lesson->is_cancelled){
            return 'This Lesson was cancelled';
        }

        if ( $lesson->instructor_id != Auth::id()
            &&
            $lesson->students()->where('users.id', Auth::id())->first()==null
        ){
            return 'You are not authorized for this action';
        }

        $disconnectedParticipant = Booking::where('bookings.lesson_id', $lesson->id)
            ->where('bookings.student_id', Auth::id())
            ->where('bookings.disconnected', 1)
            ->first();
        if ($disconnectedParticipant){
            return 'You where disconnected from this lesson';
        }

        if (config('app.env')=='prod'
            && (
                $lesson->instructor->bt_submerchant_id==null
                || $lesson->instructor->bt_submerchant_status!=MerchantAccount::STATUS_ACTIVE
                || $lesson->instructor->status != User::STATUS_ACTIVE
            )
        ){
            return 'Lesson not found';
        }

        if (!TwilioVideo::isValidTimeForLessonStart($lesson->start, $lesson->end, $lesson->timezone_id)){
            return 'It\'s not the time to for this lesson';
        }

        return null;
    }
}