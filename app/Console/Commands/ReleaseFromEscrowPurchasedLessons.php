<?php

namespace App\Console\Commands;

use App\Facades\PayPalProcessor;
use App\Models\PurchasedLesson;
use App\Repositories\PurchasedLessonRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
class ReleaseFromEscrowPurchasedLessons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchased_lessons:release_payments_from_escrow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "release purchased lessons from escrow";

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private PurchasedLessonRepository $purchasedLessonsRepo;

    public function __construct(PurchasedLessonRepository $purchasedLessonsRepo)
    {
		$this->purchasedLessonsRepo = $purchasedLessonsRepo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
		$limit = 200;
		$this->purchasedLessonsRepo
			->getHappenedLessonsPayedInEscrow($limit)
			->each(function (PurchasedLesson $purchasedLesson) {
                try{
                    $response = PayPalProcessor::releaseTransactionFromEscrow($purchasedLesson->pp_reference_id);

                    if(!isset($response['error'])) {

                        if (isset($response['processing_state']['reason'])) {
                            $purchasedLesson->status_reason = $response['processing_state']['reason'];
                        }
                        $purchasedLesson->setStatusAttribute(PurchasedLesson::STATUS_ESCROW_RELEASED);
                        $purchasedLesson->save();

                        Log::channel('paypal')->info("releaseTransactionFromEscrow purchasedLesson:{$purchasedLesson->id} , reference:{$purchasedLesson->pp_reference_id}");

                    } else {
                        $purchasedLesson->setStatusAttribute(PurchasedLesson::STATUS_UNABLE_ESCROW_RELEASE);
                        $purchasedLesson->status_reason = 'Can\'t release from escrow. ';
                        $purchasedLesson->save();
                        Log::channel('paypal')->error('purchasedLesson #'.$purchasedLesson->id.', reference #'.$purchasedLesson->pp_reference_id.': Can\'t release from escrow. ' . $response['error']['message']);
                    }
                } catch (\Exception $e) {
                    $purchasedLesson->setStatusAttribute(PurchasedLesson::STATUS_UNABLE_ESCROW_RELEASE);
                    $purchasedLesson->status_reason = 'Can\'t release from escrow. ' . $e->getMessage();
                    $purchasedLesson->save();
                    Log::channel('paypal')->error('purchasedLesson #'.$purchasedLesson->id.', reference #'.$purchasedLesson->pp_reference_id.': Can\'t release from escrow. ' . $e->getMessage());
                }

        });
    }
}
