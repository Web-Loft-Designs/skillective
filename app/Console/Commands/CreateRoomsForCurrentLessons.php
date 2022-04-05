<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Lesson;
use \App\Repositories\LessonRepository;
use Illuminate\Console\Command;
use TwilioVideo;

class CreateRoomsForCurrentLessons extends Command
{
    protected $signature = 'current_lessons_rooms:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "close rooms for past lessons";

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $lessonRepository = null;

    public function __construct(LessonRepository $lessonRepository)
    {
		$this->lessonRepository = $lessonRepository;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $this->lessonRepository->getShouldBeStartedLessons()->each( function($lesson){
//            $roomName = TwilioVideo::getRoomNameForLesson($lesson);
//            if ( empty($room = TwilioVideo::getRoom($lesson)) ) {
//                try {
//                    $room = TwilioVideo::createRoom($lesson);
//                } catch (Exception $e) {
//                    return $this->sendError("Error: " . $e->getMessage());
//                }
//                $lesson->update(['room_sid' => $room->sid]);
//                \Log::info('AutoStarted Room for Lesson #' . $lesson->id);
//            }else{
//                \Log::debug('room already exists for Lesson #' . $lesson->id);
//            }
//        } );
        return null;
    }
}
