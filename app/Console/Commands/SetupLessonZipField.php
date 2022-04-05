<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lesson;

class SetupLessonZipField extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:lesson_zip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "get zip from location and save into separate field";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		Lesson::withTrashed()->get()->each(function($lesson){
			$lesson->save();
		});
    }
}
