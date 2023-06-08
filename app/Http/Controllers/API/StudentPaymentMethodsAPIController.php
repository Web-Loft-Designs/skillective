<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Facades\BraintreeProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\API\BraintreeTransactionRequest;
use App\Models\UserPaymentMethod;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class StudentPaymentMethodsAPIController extends AppBaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
		$user = Auth::user();
		$methods = BraintreeProcessor::getSavedCustomerPaymentMethods($user);
        return $this->sendResponse($methods);
    }

    /**
     * @param BraintreeTransactionRequest $request
     * @return JsonResponse
     */
    public function store(BraintreeTransactionRequest $request)
    {
		$paymentMethodNonce = $this->_getPaymentMethodNonceFromRequest($request);
		$user = Auth::user();
		try{
			$device_data = $request->input('device_data', null);
			$newMethod = BraintreeProcessor::createPaymentMethod($user, $paymentMethodNonce, $device_data);
			if ($newMethod){
				// remove other methods of this type if exist
				$user->paymentMethods()->where('payment_method_type', $newMethod['type'])->each(function($method) {
					try{
						// remove method if no not finished Bookings created using this method exist
						if ( $method->user->bookings()
										  ->where('payment_method_token', $method->payment_method_token)
										  ->whereNotIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETE])
										  ->count()==0
						){
							BraintreeProcessor::deletePaymentMethod($method->payment_method_token);
						}
					}catch (\Exception $e){}
					$method->delete();
				});

				$userPaymentMethod = new UserPaymentMethod();
				$userPaymentMethod->payment_method_token = $newMethod['token'];
				$userPaymentMethod->payment_method_type = $newMethod['type'];
				Auth::user()->paymentMethods()->save($userPaymentMethod);
			}
		}catch (\Exception $e){
			return $this->sendError($e->getMessage(), 400);
		}

        return $this->sendResponse(true, 'Payment method saved');
    }

    /**
     * @param Request $request
     * @param $paymentMethodToken
     * @return JsonResponse
     */
    public function setAsDefaultMethod(Request $request, $paymentMethodToken)
	{
		$updateResult = BraintreeProcessor::setAsDefaultMethod($paymentMethodToken);
		if ($updateResult->success)
			return $this->sendResponse(true, 'Payment method was updated');
		else
			return $this->sendError('Can\'t update payment method');
	}

    /**
     * @param Request $request
     * @param $paymentMethodToken
     * @return JsonResponse|void
     */
    public function delete(Request $request, $paymentMethodToken)
	{
		try{
			Auth::user()->paymentMethods()->where('payment_method_token', $paymentMethodToken)->each(function($method) use ($paymentMethodToken) {
				try{
					// remove method if no not finished Bookings created using this method exist
					if ( $method->user->bookings()
									  ->where('payment_method_token', $paymentMethodToken)
									  ->whereNotIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETE])
									  ->count()==0
					){
						$deleteResult = BraintreeProcessor::deletePaymentMethod($method->payment_method_token);
					}
				}catch (\Exception $e){}
				$method->delete();
				if (isset($deleteResult) && $deleteResult->success)
					return $this->sendResponse(true, 'Payment method was deleted');
				else
					return $this->sendError('Can\'t delete payment method', 400);
			});
		}catch (\Exception $e){
			return $this->sendError('Payment method not found');
		}
	}

    /**
     * @param $request
     * @return mixed
     */
    private function _getPaymentMethodNonceFromRequest($request){
		return $request->input('payment_method_nonce', null);
	}
}
