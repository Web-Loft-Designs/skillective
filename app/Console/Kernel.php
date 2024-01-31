<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {// Flush all of the failed queue jobs
        $schedule->command('queue:flush')
            ->hourly()
            ->withoutOverlapping();
        // auto cancel bookings when lesson happened and booking still pending
        $schedule->command('bookings:cancel_past_pending')
            ->everyMinute()
            ->withoutOverlapping();
        // auto cancel bookings not approved during 2 hours
        $schedule->command('bookings:auto_cancel_pending')
            ->everyFiveMinutes()
            ->withoutOverlapping();
        //release payments for happened lessons from paypal marketplace escrow after 8 hours after lesson started
        $schedule->command('bookings:release_payments_from_pp_escrow')
            ->hourly()
            ->withoutOverlapping();
        $schedule->command('purchased_lessons:pp_release_payments_from_escrow')
            ->hourly()
            ->withoutOverlapping();
        // close rooms for past lessons
        $schedule->command('past_lessons_rooms:complete')
            ->everyMinute()
            ->withoutOverlapping();
        // cancel lesson requests if not approved in \$settings['time_to_approve_lesson_request']
        $schedule->command('lesson_requests:auto_cancel_pending')
            ->everyTenMinutes()
            ->withoutOverlapping();
        // send notifications to users, if less than 24 hours, and 1 hour before lesson
        $schedule->command('bookings:send_regular_notifications')
            ->everyMinute()
            ->withoutOverlapping();
        // auto cancel not booked private lessons after 24 hours after creation
        $schedule->command('lesson_private:auto_cancel_not_booked')
            ->everyFiveMinutes()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
