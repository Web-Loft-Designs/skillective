<?php

namespace App\Console\Commands;

use App\Facades\PayPalProcessor;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PpReleaseFromEscrowHappenedLessonsPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:release_payments_from_pp_escrow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "release payments for happened lessons from PayPal marketplace escrow after 2 hours after lesson started";

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private ?BookingRepository $bookingRepository ;

    public function __construct(BookingRepository $bookingRepository)
    {
		$this->bookingRepository = $bookingRepository;
        parent::__construct();
    }


    public function handle(): void
    {
		$this->bookingRepository
			->getHappenedLessonsPayedInEscrowBookings(50)
			->each(function (Booking $booking) {
				try{

				    $response = PayPalProcessor::releaseTransactionFromEscrow($booking->pp_reference_id);

                    if(!isset($response['error'])) {

                        if (isset($response['processing_state']['reason'])) {
                            $booking->status_reason = $response['processing_state']['reason'];
                        }

                        $booking->setStatusAttribute(Booking::STATUS_ESCROW_RELEASED);
                        $booking->save();

                        Log::channel('paypal')->info("releaseTransactionFromEscrow booking:{$booking->id} , reference:{$booking->pp_reference_id}");

                    } else {
                        $booking->setStatusAttribute(Booking::STATUS_UNABLE_ESCROW_RELEASE);
                        $booking->status_reason = 'Can\'t release from escrow. ';
                        $booking->save();
                        Log::channel('paypal')->error('Booking #'.$booking->id.', reference #'.$booking->pp_reference_id.': Can\'t release from escrow. ' . $response['error']['message']);
                    }
				} catch (\Exception $e) {
					$booking->setStatusAttribute(Booking::STATUS_UNABLE_ESCROW_RELEASE);
					$booking->status_reason = 'Can\'t release from escrow. ' . $e->getMessage();
					$booking->save();
                    Log::channel('paypal')->error('Booking #'.$booking->id.', reference #'.$booking->pp_reference_id.': Can\'t release from escrow. ' . $e->getMessage());
				}
        });
    }
}
