<?php

namespace App\Console\Commands;

use App\Facades\TwilioVideo;
use App\Models\Lesson;
use App\Repositories\LessonRepository;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CompleteRoomsForPastLessons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'past_lessons_rooms:complete';

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
        $this->lessonRepository
            ->getEndedLessons()
            ->each(function (Lesson $lesson) {
                try{

                    $timeAtLessonTimezone = Carbon::now();// UTC
                    $timeAtLessonTimezone->setTimezone($lesson->timezone_id);

                    $withExtraTimeFromLessonEndAtLessonTimezone = Carbon::createFromFormat('Y-m-d H:i:s', $lesson->end, $lesson->timezone_id);
                    $withExtraTimeFromLessonEndAtLessonTimezone->addMinutes(Lesson::VIRTUAL_LESSON_EXTRA_TIME_AFTER_END);

                    if ($withExtraTimeFromLessonEndAtLessonTimezone->greaterThanOrEqualTo($timeAtLessonTimezone)){
                        Log::channel('twilio')->info("server: try to stop virtual lesson #{$lesson->id}");
                        $lessonRoom = TwilioVideo::getRoom($lesson);
                        if ($lessonRoom){
                            $completed = TwilioVideo::completeRoom($lessonRoom);
                            if($completed){
                                $lesson->update(['room_completed' => true]);
                            }else{
                                Log::channel('twilio')->info("server: can\'t stop virtual lesson #{$lesson->id}");
                            }
                        }else{
                            Log::channel('twilio')->info("server: virtual lesson #{$lesson->id} already stopped");
                            $lesson->update(['room_completed' => true]);
                        }
                    }
                }catch (\Exception $e){
                    Log::channel('twilio')->error('server: Can\'t stop lesson #'.$lesson->id . ' : ' . $e->getMessage());
                }
            });

		return null;
    }
}
