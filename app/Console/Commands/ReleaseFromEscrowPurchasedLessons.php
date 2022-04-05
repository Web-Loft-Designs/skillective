<?php

namespace App\Console\Commands;

use App\Models\PurchasedLesson;
use \App\Repositories\PurchasedLessonRepository;
use Illuminate\Console\Command;
use App\Facades\BraintreeProcessor;
use Log;

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
    protected $description = "release payments for happened lessons from braintree merketplace escrow after 2 hours after lesson started";

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $purchasedLessonsRepo = null;

    public function __construct(PurchasedLessonRepository $purchasedLessonsRepo)
    {
		$this->purchasedLessonsRepo = $purchasedLessonsRepo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$limit = 200;
		$this->purchasedLessonsRepo
			->getHappenedLessonsPayedInEscrow($limit)
			->each(function (PurchasedLesson $purchasedLesson) {
				try{
					Log::channel('braintree')->info("Purchasare Lesson booking:{$purchasedLesson->id} , transaction:{$purchasedLesson->transaction_id}");
					BraintreeProcessor::releaseTransactionFromEscrow($purchasedLesson->transaction_id);
					$purchasedLesson->setStatusAttribute(PurchasedLesson::STATUS_ESCROW_RELEASED);
					$purchasedLesson->save();
				}catch (\Exception $e){
					$purchasedLesson->setStatusAttribute(PurchasedLesson::STATUS_UNABLE_ESCROW_RELEASE);
					$purchasedLesson->status_reason = 'Can\'t release from escrow. ' . $e->getMessage();
					$purchasedLesson->save();
					Log::channel('braintree')->error('Purchasare Lesson #'.$purchasedLesson->id.', Transaction #'.$purchasedLesson->transaction_id.': Can\'t release from escrow. ' . $e->getMessage());
				}
        });
    }
}
