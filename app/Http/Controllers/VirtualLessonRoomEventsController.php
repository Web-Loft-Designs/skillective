<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;


class VirtualLessonRoomEventsController extends Controller
{
    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function handle(Request $request)
    {
        Log::channel('twilio')->info('Got Twilio Event [ Room:' . $request->input('RoomName') . '; Event:' . $request->input('StatusCallbackEvent') . '; ]');
        if ($request->input('StatusCallbackEvent')=='participant-connected'){
            Log::channel('twilio')->info('Connected participant:' . $request->input('ParticipantIdentity'));
        }else if($request->input('StatusCallbackEvent')=='participant-disconnected'){
            Log::channel('twilio')->info('Disconnected participant:' . $request->input('ParticipantIdentity') . '; Connection duration was:' . $request->input('ParticipantDuration'));
        }else if($request->input('StatusCallbackEvent')=='room-ended'){
            Log::channel('twilio')->info( 'Room duration was: ' . $request->input('RoomDuration '));
            $roomLesson = Lesson::where('room_sid', $request->input('RoomSid'))->first();
            if ($roomLesson)
                $roomLesson->update(['room_completed' => 1]);
        }
        return response('ok');
    }
}
