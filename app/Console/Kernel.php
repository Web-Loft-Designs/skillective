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
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		$schedule->command('queue:flush')->hourly()->withoutOverlapping();
		$schedule->command('bookings:cancel_past_pending')->everyFiveMinutes();// auto cancel bookings when lesson happened and booking still pending
		$schedule->command('bookings:auto_cancel_pending')->everyFiveMinutes();// auto cancel bookings not approved during 2 hours
		$schedule->command('bookings:release_payments_from_escrow')->everyFiveMinutes();// auto cancel bookings not approved during 2 hours

        $schedule->command('past_lessons_rooms:complete')->everyMinute()->withoutOverlapping();

        $schedule->command('lesson_requests:auto_cancel_pending')->everyFiveMinutes();
        
        $schedule->command('bookings:send_regular_notifications')->everyMinute();// send notifications to users, if less than 24 hours, and 1 hour before lesson

        $schedule->command('lesson_private:auto_cancel_not_booked')->everyFiveMinutes();// auto cancel not booked private lessons after 24 hours after creation

//		$schedule->command('queue:work --once')->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
