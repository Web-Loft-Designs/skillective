<?php

namespace App\Console\Commands;

use App\Models\Lesson;
use App\Repositories\LessonRepository;
use Illuminate\Console\Command;

class AutoCancelNotBookedPrivateLessons extends Command
{
    protected $signature = 'lesson_private:auto_cancel_not_booked';

    protected $description = "cancel private lessons if not booked in \$settings['time_to_book_lesson_request']";

    private LessonRepository $lessonRepository;

    public function __construct(LessonRepository $lessonRepository)
    {
		$this->lessonRepository = $lessonRepository;
        parent::__construct();
    }

    public function handle(): void
    {
		$this->lessonRepository
			->getTooLongTimeNotBookedPrivateLessons()
			->each(function (Lesson $lesson) {
                $lesson->autoCancel();
        });
    }
}
