<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Braintree\MerchantAccount;
use App\Facades\BraintreeProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\API\BraintreeCreateMerchantRequest;
use App\Http\Requests\API\BraintreeUpdateMerchantRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstructorMerchantAPIController extends AppBaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request)
    {
		$user = Auth::user();
		$merchant = BraintreeProcessor::getMerchantAccountDetails($user);
        return $this->sendResponse($merchant);
    }


    /**
     * @param BraintreeCreateMerchantRequest $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function create(BraintreeCreateMerchantRequest $request, UserRepository $userRepository)
    {
		$user = Auth::user();

		try{
			$merchantAccount = BraintreeProcessor::createMerchant($user, $request->all());

			if ($merchantAccount!=false)
            {
                $user->update([
                    'tax_id' => isset($merchantAccount->businessDetails) ? $merchantAccount->businessDetails->taxId : null,
                    'legal_name' => isset($merchantAccount->businessDetails) ? $merchantAccount->businessDetails->legalName : null,
                ]);

				$userRepository->setUserSubMerchantId($user, $merchantAccount->id);
				$userRepository->updateUserSubMerchantStatus( $merchantAccount->id, MerchantAccount::STATUS_PENDING, '' );
				return $this->sendResponse(BraintreeProcessor::_prepareMerchantAccountOutput($merchantAccount), 'Merchant account created and will be verified soon');
			}
			return $this->sendError('Can\'t create Merchant Account', 400);
		}catch (\Exception $e){
            Log::info($e);
			return $this->sendError($e->getMessage(), 400);
		}
    }

    /**
     * @param BraintreeUpdateMerchantRequest $request
     * @return JsonResponse
     */
    public function update(BraintreeUpdateMerchantRequest $request)
	{
		$user = Auth::user();
		try{
			$merchantAccount = BraintreeProcessor::updateMerchant($user, $request);
            if ($merchantAccount != false) {

                   $user->update([
                        'tax_id' => $merchantAccount->businessDetails->taxId,
                        'legal_name' => $merchantAccount->businessDetails->legalName,
                    ]);

                return $this->sendResponse(BraintreeProcessor::_prepareMerchantAccountOutput($merchantAccount), 'Merchant account created and will be verified soon');
            }
		}catch (\Exception $e){
			Log::info($e);
			return $this->sendError($e->getMessage(), 400);
		}
		return $this->sendResponse(true, 'Merchant account updated');
	}

}
