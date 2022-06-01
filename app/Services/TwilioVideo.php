<?php

namespace App\Services;

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;
use App\Models\Booking;
use App\Models\Lesson;
use Auth;
use Carbon\Carbon;

class TwilioVideo
{
    private $twilio_account_sid, $twilio_account_token, $twilio_api_sid, $twilio_api_secret, $client;

    public function __construct()
    {
        $this->twilio_account_sid = config('services.twilio.account_sid');
        $this->twilio_account_token = config('services.twilio.auth_token');
        $this->twilio_api_sid = config('services.twilio.api_sid');
        $this->twilio_api_secret = config('services.twilio.api_secret');

        $this->client = new Client($this->twilio_account_sid, $this->twilio_account_token);
    }

    public function getRooms()
    {
        $rooms = [];

        $allRooms = $this->client->video->rooms->read([]);
        $rooms = array_map(function($room) {
            return $room;
        }, $allRooms);

        return $rooms;
    }

    public function getRoom($lesson)
    {
        $roomName = $this->getRoomNameForLesson($lesson);
        try{
            $room = $this->client->video->v1->rooms($roomName)->fetch();

//            $lessonEndGmt = new \DateTime(date('Y-m-d H:i:s', strtotime('+'.Lesson::VIRTUAL_LESSON_EXTRA_TIME_AFTER_END.' minutes', strtotime($lesson->end))), new \DateTimeZone($lesson->timezone_id));
//            $lessonEndGmt->setTimezone(new \DateTimeZone("GMT"));

//            $lessonTimezone = new \DateTimeZone('America/New_York');
//            $gmtTimezone = new \DateTimeZone('GMT');
//            $lessonEndGmt = new \DateTime( '+2 minutes' , $lessonTimezone);
//            $offset = $gmtTimezone->getOffset($lessonEndGmt);
//            $myInterval=\DateInterval::createFromDateString((string)$offset . 'seconds');
//            $lessonEndGmt->add($myInterval);

            $returnRoom                 = new \stdClass();
            $returnRoom->sid            = $room->sid;
            $returnRoom->status         = $room->status;
            $returnRoom->uniqueName     = $room->uniqueName;
            $returnRoom->type           = $room->type;
            $returnRoom->date_created   = $room->dateCreated->format('c');
            $returnRoom->lessonEnd      = $lesson->end->format('Y-m-d H:i:s'); //$lessonEndGmt->format('c');
            $returnRoom->extra_time_after_end      = Lesson::VIRTUAL_LESSON_EXTRA_TIME_AFTER_END;
            $returnRoom->timezone_id_name      = $lesson->timezone_id;
            $returnRoom->lesson_id      = $lesson->id;

            return $returnRoom;
        }catch (\Exception $e){
            return null;
        }
    }

    public function createRoom($lesson)
    {
        $roomName = $this->getRoomNameForLesson($lesson);
        $maxCountParticipants = $lesson->students->count() + 1; // + 1 instructor

        $roomType = null;
        if ($maxCountParticipants<=4)
            $roomType = 'group-small';
        elseif ($maxCountParticipants<=50)
            $roomType = 'group';
        else{
            throw new \Exception('too many group participants. max count is 50');
        }

        $roomType = 'peer-to-peer'; // TODO: remove

        return $this->client->video->rooms->create([
            'uniqueName'        => $roomName,
            'type'              => $roomType,
            'recordParticipantsOnConnect' => false,
            'statusCallback'    => route('twiliocallbackhandler'),
            'statusCallbackMethod' => 'POST',
            'maxParticipants'   => $maxCountParticipants,
        ]);
    }

    public function getAccessToken($identity, $roomName, $lifetime = 3600){
        // Create an Access Token
        $token = new AccessToken(
            $this->twilio_account_sid,
            $this->twilio_api_sid,
            $this->twilio_api_secret,
            $lifetime,
            $identity
        );

        // Grant access to Video
        $grant = new VideoGrant();
        $grant->setRoom($roomName);
        $token->addGrant($grant);

        // Serialize the token as a JWT
        return $token->toJWT();
    }

    public function completeRoom($room){
        try {
            $this->client->video->v1->rooms($room->sid)
                ->update("completed");
            return true;
        }catch(\Exception $e){
            \Log::error('Can\'t complete room : ' . $e->getCode() . " : " . $e->getMessage());
            return false;
        }
    }

    public function disconnectParticipant($room, $identity){
        try {
            $roomParticipants = $this->client->video->v1->rooms($room->sid)->participants->read(array("status" => "connected"));
            foreach ($roomParticipants as $participant) {
                if ($participant->identity==$identity)
                    echo $participant->update(array("status" => "disconnected"));
            }
            return true;
        }catch(\Exception $e){
            \Log::error('Can\'t disconnect participant : ' . $e->getCode() . " : " . $e->getMessage());
            return false;
        }
    }

    public function getRoomNameForLesson($lesson){
        return 'Lesson ' . $lesson->id;
    }

    public function getInstructorParticipantIdentity($user){
        return 'Instructor: ' . $user->first_name . ' ' . $user->last_name;
    }

    public function getStudentParticipantIdentity($user){
        return 'Client #' . $user->id. ':' . $user->first_name . ' ' . $user->last_name;
    }

    public function getRoomSettings($token, $lesson)
    {
        $participantsDetails = [
            [
                'identity'  => $this->getInstructorParticipantIdentity($lesson->instructor),
                'image'     => $lesson->instructor->profile->getImageUrl()
            ]
        ];

        $lesson->students()->with(['profile'])->each(function($student) use (&$participantsDetails){
            $participantsDetails[] = [
                    'id'        => $student->id,
                    'identity'  => $this->getStudentParticipantIdentity($student),
                    'image'     => $student->profile->getImageUrl()
                ];
        });

//        $lesson->bookings()
//            ->whereNotIn('bookings.status', [Booking::STATUS_CANCELLED, Booking::STATUS_PENDING])
//            ->with(['student'])->get()->each(function($booking) use (&$participantsDetails){
//                $participantsDetails[] = [
//                    'image' => $booking->student->profile->getImageUrl(),
//                    'disconnected' => $booking->disconnected,
//                    'identity' => $this->getStudentParticipantIdentity($booking->student)
//                ];
//        });


        return [
            'token'                     => $token,
            'roomName'                  => $this->getRoomNameForLesson($lesson),
            'room'                      => $this->getRoom($lesson),
            'lessonId'                  => $lesson->id,
            'instructorParticipantIdentity'  => $this->getInstructorParticipantIdentity($lesson->instructor),
            'meInstructor'              => Auth::user()->hasRole('Instructor'),
            'participantsDetails'            => $participantsDetails
        ];
    }

    public function isValidTimeForLessonStart($start, $end, $timezone_id)
    {
        // Valid when ~: (lesson_start - 5 min) <= $timeAtLessonTimezone < lesson_end + 1 min

        $timeAtLessonTimezone = Carbon::now();// UTC
        $timeAtLessonTimezone->setTimezone($timezone_id);

        $withExtraTimeFromLessonStartAtLessonTimezone = Carbon::createFromFormat('Y-m-d H:i:s', $start, $timezone_id);
        $withExtraTimeFromLessonStartAtLessonTimezone->subMinutes(Lesson::VIRTUAL_LESSON_EXTRA_TIME_BEFORE_START);

        $withExtraTimeFromLessonEndAtLessonTimezone = Carbon::createFromFormat('Y-m-d H:i:s', $end, $timezone_id);
        $withExtraTimeFromLessonEndAtLessonTimezone->addMinutes(Lesson::VIRTUAL_LESSON_EXTRA_TIME_AFTER_END);

//        dd($timeAtLessonTimezone->format('Y-m-d H:i:s'), $withExtraTimeFromLessonStartAtLessonTimezone->format('Y-m-d H:i:s'), $withExtraTimeFromLessonEndAtLessonTimezone->format('Y-m-d H:i:s'));

        return $withExtraTimeFromLessonStartAtLessonTimezone->lessThanOrEqualTo($timeAtLessonTimezone) && $withExtraTimeFromLessonEndAtLessonTimezone->greaterThanOrEqualTo($timeAtLessonTimezone);

    }
}
