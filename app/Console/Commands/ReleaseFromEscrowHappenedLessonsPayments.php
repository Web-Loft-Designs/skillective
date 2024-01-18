<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use Illuminate\Console\Command;
use App\Facades\BraintreeProcessor;
use Illuminate\Support\Facades\Log;


class ReleaseFromEscrowHappenedLessonsPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:release_payments_from_escrow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "release payments for happened lessons from braintree merketplace escrow after 2 hours after lesson started";

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
			->getHappenedLessonsPayedInEscrowBookings($limit)
			->each(function (Booking $booking) {
				try{
					Log::channel('braintree')->info("releaseTransactionFromEscrow booking:{$booking->id} , transaction:{$booking->transaction_id}");
					BraintreeProcessor::releaseTransactionFromEscrow($booking->transaction_id);
					$booking->setStatusAttribute(Booking::STATUS_ESCROW_RELEASED);
					$booking->save();
				}catch (\Exception $e){
					$booking->setStatusAttribute(Booking::STATUS_UNABLE_ESCROW_RELEASE);
					$booking->status_reason = 'Can\'t release from escrow. ' . $e->getMessage();
					$booking->save();
					Log::channel('braintree')->error('Booking #'.$booking->id.', Transaction #'.$booking->transaction_id.': Can\'t release from escrow. ' . $e->getMessage());
				}
        });
    }
}
