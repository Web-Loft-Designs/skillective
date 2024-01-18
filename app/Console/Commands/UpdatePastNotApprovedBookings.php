<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use Illuminate\Console\Command;

class UpdatePastNotApprovedBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel_past_pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cancel bookings when lesson happened and booking still pending';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
		$this->bookingRepository = $bookingRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle():void
    {
		$limit = 100;
		$this->bookingRepository
			->getPastLessonsPendingBookings($limit)
			->each(function (Booking $booking) {
				$cancelledBy = null;
				$booking->cancel($cancelledBy);
        });
    }
}
