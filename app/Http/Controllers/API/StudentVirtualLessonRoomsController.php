<?php

namespace App\Http\Controllers\API;

use App\Facades\TwilioVideo;
use App\Http\Controllers\AppBaseController;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class StudentVirtualLessonRoomsController extends AppBaseController
{
    /**
     * @return JsonResponse
     */
    public function get()
    {
        $rooms = [];
        if (config('app.env')!='prod'){
            Booking::whereRaw(' (bookings.student_id = '.Auth::id().' ) OR ( bookings.instructor_id = '.Auth::id().' )')
                ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
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

}