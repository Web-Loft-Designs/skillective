<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\Test\TestSupervisor;

class TestSupervisorProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:supervisor_process {email} {--queue=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "it should create a job in queue to send an email to a provided email address";

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
		$email = $this->argument('email');
		Mail::to($email)
			->queue(new TestSupervisor());
		$this->info('Notification in queue. Check your email for this message in a few minutes');
    }
}
