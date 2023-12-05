<?php

namespace App\Console\Commands;

use App\Models\Lesson;
use App\Repositories\LessonRepository;
use Exception;
use Illuminate\Console\Command;

class UpdateLessonsTimezoneOffset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_lessons_timezone_offset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private LessonRepository $lessonRepository;

    public function __construct(LessonRepository $lessonRepository)
    {
		$this->lessonRepository = $lessonRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
		$limit = 100;
		$this->lessonRepository
			->all()
			->each(function (Lesson $lesson) {
                $time = new \DateTime($lesson->start, new \DateTimeZone($lesson->timezone_id));
                $lesson->update(['timezone_offset_gmt' => $time->format('P')]);
        });
    }
}
