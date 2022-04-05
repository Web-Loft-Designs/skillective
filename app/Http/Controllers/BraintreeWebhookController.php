<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\BraintreeProcessor;
use Log;
use Auth;
use App\Repositories\UserRepository;
use App\Repositories\BookingRepository;

class BraintreeWebhookController extends AppBaseController
{
    public function index(Request $request, UserRepository $userRepository, BookingRepository $bookingRepository)
    {
		Log::channel('braintree')->info('Webhook call');

		$test_webhook = $request->get('test_webhook', 0); // https://skill-test2.org/braintree/webhook?test_webhook=1
		if(
			(config('app.env')=='local' && $test_webhook==1)
		||
			($request->has('bt_signature') && $request->has('bt_payload'))
		) {

			if (config('app.env')=='local' && $test_webhook==1){ // part for testing purposes
				$user = Auth::user();
				if ($user && $user->bt_submerchant_id){
//					$sampleNotification = BraintreeProcessor::createSampleWebhookNotification( \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_DECLINED, $user->bt_submerchant_id );
//					$sampleNotification = BraintreeProcessor::createSampleWebhookNotification( \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_APPROVED, $user->bt_submerchant_id );
					$sampleNotification = BraintreeProcessor::createSampleWebhookNotification( \Braintree_WebhookNotification::DISBURSEMENT_EXCEPTION, $user->bt_submerchant_id );
					$sampleNotification = BraintreeProcessor::createSampleWebhookNotification( \Braintree_WebhookNotification::DISBURSEMENT, $user->bt_submerchant_id );
					$webhookNotification = \Braintree_WebhookNotification::parse(
						$sampleNotification['bt_signature'],
						$sampleNotification['bt_payload']
					);
				}
			}else{ // real live/sandbox requests
				$webhookNotification = \Braintree_WebhookNotification::parse(
					$request->input('bt_signature'), $request->input('bt_payload')
				);
			}

			Log::channel('braintree')->info("Webhook Received , Kind = " . $webhookNotification->kind);
			if ($webhookNotification){
				switch ($webhookNotification->kind){
					case \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_APPROVED:
						$userRepository->updateUserSubMerchantStatus( $webhookNotification->merchantAccount->id, \Braintree_MerchantAccount::STATUS_ACTIVE );
						break;
					case \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_DECLINED:
						$userRepository->updateUserSubMerchantStatus( $webhookNotification->merchantAccount->id, \Braintree_MerchantAccount::STATUS_SUSPENDED, $webhookNotification->message );
						break;


					case \Braintree_WebhookNotification::DISBURSEMENT:
						foreach ($webhookNotification->disbursement->transactionIds as $transactionId){
							$bookingRepository->markBookingTransactionAsComplete($transactionId);
						}
						break;
					case \Braintree_WebhookNotification::DISBURSEMENT_EXCEPTION:
						foreach ($webhookNotification->disbursement->transactionIds as $transactionId) {
							$bookingRepository->markBookingTransactionAsFailedDisbursement( $transactionId, $webhookNotification->disbursement->exceptionMessage, $webhookNotification->disbursement->followUpAction );
						}
						break;

					case \Braintree_WebhookNotification::CHECK:
						Log::channel('braintree')->info('Webhook check successfull');
						break;
				}

				return response('OK');
			}
		}
		return abort(403);
    }
}
