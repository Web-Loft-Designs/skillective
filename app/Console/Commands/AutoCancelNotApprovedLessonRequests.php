<?php

namespace App\Console\Commands;

use App\Models\LessonRequest;
use \App\Repositories\LessonRequestRepository;
use Illuminate\Console\Command;

class AutoCancelNotApprovedLessonRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lesson_requests:auto_cancel_pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "cancel lesson requests if not approved in \$settings['time_to_approve_lesson_request']";

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $lessonRequestsRepository = null;

    public function __construct(LessonRequestRepository $lessonRequestsRepository)
    {
		$this->lessonRequestsRepository = $lessonRequestsRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$this->lessonRequestsRepository
			->getTooLongTimePendingLessonRequests()
			->each(function (LessonRequest $lessonRequest) {
				$lessonRequest->autoCancel();
        });
    }
}
