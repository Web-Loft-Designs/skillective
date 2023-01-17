<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Response;
use Auth;
use Log;
use App\Facades\BraintreeProcessor;
use Illuminate\Http\Request;
use App\Http\Requests\API\BraintreeCreateMerchantRequest;
use App\Http\Requests\API\BraintreeUpdateMerchantRequest;
use App\Repositories\UserRepository;

class InstructorMerchantAPIController extends AppBaseController
{
    public function get(Request $request)
    {
		$user = Auth::user();
		$merchant = BraintreeProcessor::getMerchantAccountDetails($user);
        return $this->sendResponse($merchant);
    }


    public function create(BraintreeCreateMerchantRequest $request, UserRepository $userRepository)
    {
		$user = Auth::user();
		try{
			$merchantAccount = BraintreeProcessor::createMerchant($user, $request->all());
			if ($merchantAccount!=false)
            {
                $user->tax_id = $request->taxId;
                $user->legal_name = $request->legalName;
                $user->save();

				$userRepository->setUserSubMerchantId($user, $merchantAccount->id);
				$userRepository->updateUserSubMerchantStatus( $merchantAccount->id, \Braintree_MerchantAccount::STATUS_PENDING, '' );
				return $this->sendResponse(BraintreeProcessor::_prepareMerchantAccountOutput($merchantAccount), 'Merchant account created and will be verified soon');
			}
			return $this->sendError('Can\'t create Merchant Account', 400);
		}catch (\Exception $e){
            Log::info($e);
			return $this->sendError($e->getMessage(), 400);
		}
    }

	public function update(BraintreeUpdateMerchantRequest $request)
	{
        $request['taxId'] = str_replace(['-',',','/','.',';',':',' ','"', "'"], '', trim($request->taxId));
        if(strlen($request->taxId) == 9) {
            $request['individual_ssn'] = $request->taxId;
        }

		$user = Auth::user();
		try{
			$merchantAccount = BraintreeProcessor::updateMerchant($user, $request);
            if ($merchantAccount != false)
            {
                if($request->taxId !== null) {
                    $user->update([
                        'tax_id' => $request->taxId,
                        'legal_name' => $request->legalName,
                    ]);
                }
                return $this->sendResponse(BraintreeProcessor::_prepareMerchantAccountOutput($merchantAccount), 'Merchant account created and will be verified soon');
            }
		}catch (\Exception $e){
			Log::info($e);
			return $this->sendError($e->getMessage(), 400);
		}
		return $this->sendResponse(true, 'Merchant account updated');
	}

}
