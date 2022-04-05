<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use TwilioVideo;

class VirtualLessonRoomEventsController extends Controller
{
    public function handle(Request $request)
    {
        \Log::channel('twilio')->info('Got Twilio Event [ Room:' . $request->input('RoomName') . '; Event:' . $request->input('StatusCallbackEvent') . '; ]');
        if ($request->input('StatusCallbackEvent')=='participant-connected'){
            \Log::channel('twilio')->info('Connected participant:' . $request->input('ParticipantIdentity'));
        }else if($request->input('StatusCallbackEvent')=='participant-disconnected'){
            \Log::channel('twilio')->info('Disconnected participant:' . $request->input('ParticipantIdentity') . '; Connection duration was:' . $request->input('ParticipantDuration'));
        }else if($request->input('StatusCallbackEvent')=='room-ended'){
            \Log::channel('twilio')->info( 'Room duration was: ' . $request->input('RoomDuration '));
            $roomLesson = Lesson::where('room_sid', $request->input('RoomSid'))->first();
            if ($roomLesson)
                $roomLesson->update(['room_completed' => 1]);
        }
        return response('ok');
    }
}
