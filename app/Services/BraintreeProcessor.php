<?php
namespace App\Services;

use App\Models\User;
use App\Models\Profile;
use App\Repositories\UserRepository;
use DB;
use Log;
use Braintree_Transaction;
use Braintree_Gateway;
use Braintree_MerchantAccount;
use Braintree_Configuration;
use Braintree_Exception_NotFound;

class BraintreeProcessor {

	/** @var  UserRepository */
	private $userRepository;

	/** @var  Braintree_Gateway */
	private $gateway;

	public function __construct(UserRepository $userRepository){
		$this->userRepository = $userRepository;
		$this->gateway = new Braintree_Gateway(Braintree_Configuration::$global);
	}

	public function generateClientToken(User $user = null){
		$options = [];

		try{
			if ($user && $user->braintree_customer_id)
				$options["customerId"] = $user->braintree_customer_id;
			$token = $this->gateway->clientToken()->generate($options);
			return $token;
		}catch (\InvalidArgumentException $e){
			Log::channel('braintree')->info($e->getMessage());
			$user->braintree_customer_id = null;
			$user->save();
			return $this->gateway->clientToken()->generate([]);
		}
	}

	public function findCustomer(User $user){
		if (!$user->braintree_customer_id)
			return null;
		try{
			return $this->gateway->customer()->find($user->braintree_customer_id);
		}catch (\Braintree_Exception_NotFound $e){
			Log::channel('braintree')->info($e->getMessage());
			$user->braintree_customer_id = null;
			$user->save();
			return null;
		}
	}

	public function getSavedCustomerPaymentMethods(User $user){
		$methods = [];
		try{
			$c = $this->findCustomer($user);

			if ($c){
				$savedMethods = $user->paymentMethods->pluck('payment_method_token')->all();

				foreach ($c->paymentMethods as $pm){

					if (!in_array($pm->token, $savedMethods)) // skip payment methods added when booking made but not added in profile
						continue;

					switch (get_class($pm)){
						case 'Braintree\CreditCard':
							$methods['CreditCard'] = [
								'last4' => $pm->last4,
								'cardholderName' => $pm->cardholderName,
								'expirationDate'=> $pm->expirationDate,
								'is_default' => $pm->default,
								'token' => $pm->token
							];
							break;
						case 'Braintree\PayPalAccount':
							$methods['PayPalAccount'] = [
								'is_default' => $pm->default,
								'token' => $pm->token
							];
							break;
						case 'Braintree\VenmoAccount':
							$methods['VenmoAccount'] = [
								'is_default' => $pm->default,
								'username' => $pm->username,
								'token' => $pm->token
							];
							break;
					}
				}
			}
			return $methods;
		}catch (\Braintree_Exception_NotFound $e){
			Log::channel('braintree')->info($e->getMessage());
			$user->braintree_customer_id = null;
			$user->save();
			return $methods;
		}
	}

	// used nowhere , just for development
	public function deleteAllCustomerPaymentMethods(User $user){
		$countDeleted = 0;
		$c = $this->findCustomer($user);
		if ($c) {
			foreach ( $c->paymentMethods as $pm ) {
				$result = $this->deletePaymentMethod($pm->token);
				if ($result->success==true){
					$user->paymentMethods()->where('payment_method_token', $pm->token)->each(function($method) {
						$method->delete();
					});
					$countDeleted++;
				}else{
					list($returnError, $codes) = $this->_resultErrorsForResponse($result);
					throw new \Exception('Can\'t delete payment method: ' . $returnError);
				}
			}
		}
		return $countDeleted;
	}

	public function getAllCustomerPaymentMethods(User $user){
		$methods = [];
		$c = $this->findCustomer($user);
		if ($c){
			foreach ($c->paymentMethods as $pm){
				switch ( get_class($pm) ){
					case 'Braintree\CreditCard':
						$methods[$pm->token] = [
							'type' => $this->_getPaymentMethodType($pm),
							'last4' => $pm->last4,
							'cardholderName' => $pm->cardholderName,
							'expirationDate'=> $pm->expirationDate,
							'is_default' => $pm->default,
							'token' => $pm->token
						];
//						}
						break;
					case 'Braintree\PayPalAccount':
						$methods[$pm->token] = [
							'type' => $this->_getPaymentMethodType($pm),
							'is_default' => $pm->default,
							'token' => $pm->token
						];
						break;
					case 'Braintree\VenmoAccount':
						$methods[$pm->token] = [
							'type' => $this->_getPaymentMethodType($pm),
							'is_default' => $pm->default,
							'token' => $pm->token
						];
						break;
				}
			}
		}
		return $methods;
	}

	public function createCustomer(User $user){
		if (!$user->braintree_customer_id){
			$result = $this->gateway->customer()->create([
				'firstName' => $user->first_name,
				'lastName' => $user->last_name,
//			'company' => '',
				'email' => $user->email,
				'phone' => presentMobilePhone($user->profile->mobile_phone)
			]);

			if ($result && $result->success){
				$user->braintree_customer_id = $result->customer->id;
				$user->save();
			}else{
				list($returnError, $codes) = $this->_resultErrorsForResponse($result);
				throw new \Exception('Can\'t create customer: ' . $returnError);
			}
		}
		return $user;
	}

	public function updateCustomer(User $user){
		$result = $this->gateway->customer()->update($user->braintree_customer_id,
			[
			'firstName' => $user->first_name,
			'lastName' => $user->last_name,
			'email' => $user->email,
			'phone' => presentMobilePhone($user->profile->mobile_phone)
		]);

		if ($result && $result->success){
		}else{
			list($returnError, $codes) = $this->_resultErrorsForResponse($result);
			throw new \Exception('Can\'t update customer:' . $returnError);
		}
	}

	public function createPaymentMethod(User $user, $paymentMethodNonce, $deviceData = null){
		if (!$user->braintree_customer_id){
			$user = self::createCustomer($user);
		}
//		$oldListMethods = BraintreeProcessor::getAllCustomerPaymentMethods($user);
		$options = [
			'customerId' => $user->braintree_customer_id,
			'paymentMethodNonce' => $paymentMethodNonce
		];
		if ($deviceData)
			$options['deviceData'] = $deviceData;
		$result = $this->gateway->paymentMethod()->create($options);
		if ($result->success==true){

			return ['token' => $result->paymentMethod->token, 'type' => $this->_getPaymentMethodType($result->paymentMethod)];
		}else{
			list($returnError, $codes) = $this->_resultErrorsForResponse($result);
			throw new \Exception('Can\'t create payment method: ' . $returnError);
		}
		return null;
	}

	public function deletePaymentMethod($token){
		return $this->gateway->paymentMethod()->delete($token); // Braintree_Exception_NotFound
	}

	public function findPaymentMethod($token){
		return $this->gateway->paymentMethod()->find($token); // Braintree_Exception_NotFound
	}

	public function setAsDefaultMethod($token){
		return $this->gateway->paymentMethod()->update(
			$token,
			[
				'options' => [
					'makeDefault' => true
				]
			]
		);
	}

	public function createSellBookingTransactionAndHoldInEscrow($subMerchantId, $paymentMethodVaultToken, $booking, $serviceFee, $expectedBrainTreeFee){
		$description = "{$booking->lesson->genre->title} Lesson #{$booking->lesson_id}, booking #{$booking->id}, (instructor #{$booking->instructor_id})";
		$lineItems = [
			[
				'description'	=> $description,
				'kind'			=> 'debit',
				'name'			=> "Lesson #{$booking->lesson_id} Booking ",
				'quantity'		=> 1,
				'totalAmount'	=> $booking->spot_price + $serviceFee + $expectedBrainTreeFee,
				'unitAmount'	=> $booking->spot_price + $serviceFee + $expectedBrainTreeFee
			]
		];
		$options = [
			'submitForSettlement'	=> true,
			'holdInEscrow'			=> true,
		];

		$studentMobilePhone = $booking->student->profile->mobile_phone;
		$customer = [
			'firstName' => $booking->student->first_name,
			'lastName' => $booking->student->last_name,
			'phone' => $studentMobilePhone,
			'email' => $booking->student->email
		];

		$descriptor = [
			'name' => "instructor{$booking->instructor_id}*lesson{$booking->lesson_id}",
			'phone' => $studentMobilePhone,
		];

		$result = $this->gateway->transaction()->sale([
			'merchantAccountId'		=> $subMerchantId,
			'amount'				=> round($booking->spot_price + $serviceFee + $expectedBrainTreeFee, 2),
			'paymentMethodToken'	=> $paymentMethodVaultToken,
			'serviceFeeAmount'		=> round($serviceFee + $expectedBrainTreeFee, 2),
			'options'				=> $options,
			'orderId'				=> str_pad($booking->id, 6, "0", STR_PAD_LEFT),
			'customer'				=> $customer,
			'descriptor'			=> $descriptor,
			'lineItems'				=> $lineItems
		]);

		if ($result->success) {
			return $result->transaction;
		} else {
			list($returnError, $codes) = $this->_resultErrorsForResponse($result);
			throw new \Exception('Can\'t create transaction: ' . $returnError);
		}
	}

	public function createPurchasereLessonTransactionAndHoldInEscrow($subMerchantId, $paymentMethodVaultToken, $purchasedLesson, $serviceFee = 0, $expectedBrainTreeFee = 0){

		$description = "{$purchasedLesson->preRecordedLesson->genre->title} Pre Recorded Lesson #{$purchasedLesson->pre_r_lesson_id}, booking #{$purchasedLesson->id}, (instructor #{$purchasedLesson->preRecordedLesson->instructor_id})";
		$lineItems = [
			[
				'description'	=> $description,
				'kind'			=> 'debit',
				'name'			=> "Lesson #{$purchasedLesson->pre_r_lesson_id} Booking ",
				'quantity'		=> 1,
				'totalAmount'	=> $purchasedLesson->preRecordedLesson->price + $serviceFee + $expectedBrainTreeFee,
				'unitAmount'	=> $purchasedLesson->preRecordedLesson->price + $serviceFee + $expectedBrainTreeFee
			]
		];
		$options = [
			'submitForSettlement'	=> true,
			'holdInEscrow'			=> true,
		];

		$studentMobilePhone = $purchasedLesson->student->profile->mobile_phone;
		$customer = [
			'firstName' => $purchasedLesson->student->first_name,
			'lastName' => $purchasedLesson->student->last_name,
			'phone' => $studentMobilePhone,
			'email' => $purchasedLesson->student->email
		];

		$descriptor = [
			'name' => "instructor{$purchasedLesson->instructor_id}*lesson{$purchasedLesson->pre_r_lesson_id}",
			'phone' => $studentMobilePhone,
		];

		$result = $this->gateway->transaction()->sale([
			'merchantAccountId'		=> $subMerchantId,
			'amount'				=> round($purchasedLesson->preRecordedLesson->price + $serviceFee + $expectedBrainTreeFee, 2),
			'paymentMethodToken'	=> $paymentMethodVaultToken,
			'serviceFeeAmount'		=> round($serviceFee + $expectedBrainTreeFee, 2),
			'options'				=> $options,
			'orderId'				=> str_pad($purchasedLesson->id, 6, "0", STR_PAD_LEFT),
			'customer'				=> $customer,
			'descriptor'			=> $descriptor,
			'lineItems'				=> $lineItems
		]);

		if ($result->success) {
			return $result->transaction;
		} else {
			list($returnError, $codes) = $this->_resultErrorsForResponse($result);
			throw new \Exception('Can\'t create transaction: ' . $returnError);
		}
	}

	public function cancelTransaction($transactionId){
		try{
			$transaction = $this->gateway->transaction()->find($transactionId);
			$transaction->escrowStatus; // \Braintree_Transaction::ESCROW_HELD
			if ( in_array($transaction->status, [\Braintree_Transaction::SETTLED, \Braintree_Transaction::SETTLING]) ) {
				$result = $this->gateway->transaction()->refund( $transactionId );
			}else {
				$result = $this->gateway->transaction()->void( $transactionId );
			}

			if (!$result->success){
				list($returnError, $codes) = $this->_resultErrorsForResponse($result);
				throw new \Exception($returnError, $codes[0]);
			}
			return true;
		}catch (\Braintree_Exception_NotFound $e){
			throw new \Exception('Can\'t cancel transaction. Transaction '.$transactionId.' not found', 404);
		}


		$result = $this->gateway->transaction()->refund($transactionId);
		if ($result->success){
			return true;
		}else{
			list($returnError, $codes) = $this->_resultErrorsForResponse($result);
			throw new \Exception($returnError, $codes[0]);
		}
	}

	public function releaseTransactionFromEscrow($transactionId){
		try{
			$result = $this->gateway->transaction()->releaseFromEscrow($transactionId);
			if ($result->success){
				return true;
			}else{
				list($returnError, $codes) = $this->_resultErrorsForResponse($result);
				throw new \Exception($returnError, $codes[0]);
			}
		}catch (\Braintree_Exception_NotFound $e){
			throw new \Exception('Can\'t release transaction. Transaction '.$transactionId.' not found', 404);
		}
	}

	public function createMerchant($user, $inputData){
		if (!$user->hasRole(User::ROLE_INSTRUCTOR)){
			throw new \Exception('Merchant accounts can be created for Instructors only');
		}

		if($inputData['funding_accountNumber_confirmation'] !== $inputData['funding_accountNumber']){
			throw new \Exception('Bank Account Number and Bank Account Number Confirmation do not match');
		}

		$merchantAccountParams = $this->_buildMerchantAccountParametersFromInputData($inputData);
		$merchantAccountParams['funding']['descriptor'] = 'Skillective Instructor #' . $user->id;
		$merchantAccountParams['tosAccepted'] = $inputData['tosAccepted'];
		$merchantAccountParams['masterMerchantAccountId'] = config('services.braintree.master_merchant_account_id');
		$merchantAccountParams['id'] = 'instructor_' . $user->id;


		$result = $this->gateway->merchantAccount()->create($merchantAccountParams);
		if ($result->success){
			return $result->merchantAccount;
		}else{
			list($returnError, $codes) = $this->_resultErrorsForResponse($result);
			throw new \Exception($returnError, $codes[0]);
		}
	}

	public function updateMerchant($user, $inputData){

		if($inputData['funding_accountNumber_confirmation'] !== $inputData['funding_accountNumber']){
			throw new \Exception('Bank Account Number and Bank Account Number Confirmation do not match');
		}

		if (!$user->hasRole(User::ROLE_INSTRUCTOR)){
			throw new \Exception('Merchant accounts can be created for Instructors only');
		}

		$merchantAccountParams = $this->_buildMerchantAccountParametersFromInputData($inputData);
		if ($merchantAccountParams['funding']['accountNumber']==null){
			unset($merchantAccountParams['funding']['destination']);
			unset($merchantAccountParams['funding']['accountNumber']);
		}
		if ($merchantAccountParams['individual']['ssn']==null)
			unset($merchantAccountParams['individual']['ssn']);

		$result = $this->gateway->merchantAccount()->update($user->bt_submerchant_id, $merchantAccountParams);
		if ($result->success){
			return $result->merchantAccount;
		}else{
			list($returnError, $codes) = $this->_resultErrorsForResponse($result);
			throw new \Exception($returnError, $codes[0]);
		}
	}

	public function getMerchantAccountDetails($user){
		if ($user->bt_submerchant_id){
			try{
				/*
				 * @var \Braintree_MerchantAccount $merchantAccount
				 */
				$merchantAccount = $this->gateway->merchantAccount()->find( $user->bt_submerchant_id );

				return $this->_prepareMerchantAccountOutput($merchantAccount);
			}catch (\Exception $e){
				Log::channel('braintree')->info("Submerchant Account with id {$user->bt_submerchant_id} Not Found. User Merchant Data reset");
				$user->resetBraintreeData();
				return null;
			}
		}
		return null;
	}

	public function createSampleWebhookNotification($type, $subMerchantId){
		$sampleNotification = $this->gateway->webhookTesting()->sampleNotification(
			$type,
			null,
			$subMerchantId
		);

		return $sampleNotification;
	}

	public function _prepareMerchantAccountOutput($merchantAccount){
		return [
			'id' => $merchantAccount->id,
			'status' => $merchantAccount->status,
			'individual_firstName'		=> isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->firstName :'',
			'individual_lastName'		=> isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->lastName : '',
			'individual_email'			=> isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->email : '',
			'individual_phone'			=> isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->phone : '',
			'individual_dateOfBirth'	=> isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->dateOfBirth : '',
			'individual_ssn' 			=> isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->ssnLast4 : '',
			'individual_streetAddress'	=> isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->addressDetails->streetAddress : '',
			'individual_locality'		=> isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->addressDetails->locality : '',
			'individual_region'			=> isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->addressDetails->region : '',
			'individual_postalCode'		=> isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->addressDetails->postalCode : '',

			'funding_email'				=> isset($merchantAccount->fundingDetails) ? $merchantAccount->fundingDetails->email : '',
			'funding_mobilePhone'		=> isset($merchantAccount->fundingDetails) ? $merchantAccount->fundingDetails->mobilePhone : '',
			'funding_accountNumber'		=> isset($merchantAccount->fundingDetails) ? $merchantAccount->fundingDetails->accountNumberLast4 : '',
			'funding_routingNumber'		=> isset($merchantAccount->fundingDetails) ? $merchantAccount->fundingDetails->routingNumber : '',
		];
	}

	public function _getPaymentMethodType($paymentMethod){
		return str_replace('Braintree\\', '', get_class($paymentMethod));
	}

	private function _buildMerchantAccountParametersFromInputData($inputData){
		$merchantAccountParams = [
			'individual' => [
				'firstName' => $inputData['individual_firstName'],
				'lastName' => $inputData['individual_lastName'],
				'email' => $inputData['individual_email'],
				'phone' => trim(prepareMobileForTwilio($inputData['individual_phone']), '+'),
				'dateOfBirth' => $inputData['individual_dateOfBirth'],
				'address' => [
					'streetAddress' => $inputData['individual_streetAddress'],
					'locality' => $inputData['individual_locality'],
					'region' => $inputData['individual_region'],
					'postalCode' => $inputData['individual_postalCode']
				],
				'ssn' => isset($inputData['individual_ssn']) ? $inputData['individual_ssn'] : null
			],
			'funding' => [
				'destination' => Braintree_MerchantAccount::FUNDING_DESTINATION_BANK, // TODO: Bank or Venmo instructor to decide
				'email' => isset($inputData['funding_email']) ? $inputData['funding_email'] :null, // optional
				'mobilePhone' => isset($inputData['funding_mobilePhone']) ? trim(prepareMobileForTwilio($inputData['funding_mobilePhone']), '+') :null, // optional
				'accountNumber' => isset($inputData['funding_accountNumber']) ? $inputData['funding_accountNumber'] : null, // TODO : required with  Braintree_MerchantAccount::FUNDING_DESTINATION_BANK , instructor must provide
				'routingNumber' => isset($inputData['funding_routingNumber']) ? $inputData['funding_routingNumber'] : null // TODO : required with  Braintree_MerchantAccount::FUNDING_DESTINATION_BANK , instructor must provide
			]
		];

		return $merchantAccountParams;
	}

	private function _resultErrorsForResponse($result){
		$returnError = '';
		$codes = [];
		foreach($result->errors->deepAll() AS $error) {
			Log::error($error->code . ": " . $error->message);
			$returnError .= $error->code . ": " . $error->message . "\n<br>";
			$codes[] = $error->code;
		}
		if ($returnError==''){
			$returnError = $result->message;
			$codes[] = 400;
		}
		return [$returnError, $codes];
	}
}
