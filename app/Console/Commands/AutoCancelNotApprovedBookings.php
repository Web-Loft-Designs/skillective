<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Lesson;
use \App\Repositories\BookingRepository;
use Illuminate\Console\Command;

class AutoCancelNotApprovedBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:auto_cancel_pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "cancel bookings if not approved in \$settings['time_to_approve_booking']";

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $bookingRepository = null;

    public function __construct(BookingRepository $bookingRepository)
    {
		$this->bookingRepository = $bookingRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$limit = 50;
		$this->bookingRepository
			->getTooLongTimePendingBookings($limit)
			->each(function (Booking $booking) {
				$booking->autoCancel();
        });
    }
}
