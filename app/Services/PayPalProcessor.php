<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;

class PayPalProcessor
{

    private UserRepository $userRepository;
    private PayPalClient $payPalClient;

    /**
     * @throws Throwable
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->payPalClient = new PayPalClient();
        $this->payPalClient->getAccessToken();
    }

    /**
     * @throws Throwable
     */
    public function getMerchantDetail($user): float|object|int|bool|array|string|null
    {
        try {
            if ($user->pp_merchant_id) {
                // якщо є pp_merchant_id провіримо статус в вернем деталі
                $result = $this->payPalClient->showReferralStatus($user->pp_merchant_id);
                $result = $this->preparationPpMerchantAccount($result);

            } else {
                // перевіряємо  pp_tracking_id
                if ($user->pp_tracking_id) {
                    // якщо є pp_tracking_id пробуємо получити  pp_merchant_id
                    $result = $this->payPalClient->showPartnerReferralId($user->pp_tracking_id);

                    if (isset($result['error'])) {
                        // якщо є помилка значити інструктор не проходив по попередній силці генеруємо нову силку для реєстрації
                        $result = [
                            "actionUrl" => $this->getRegistrationMerchantLink($user),
                            'status' => "Not activated",
                            'ppMerchantId' => "",
                            'message' => "Go to PayPal."
                        ];

                    } else {
                        // перевіримо pp_merchant_id і оновим дані інструктора і повернемо результати інтеграції
                        if ( isset($result['tracking_id']) && isset($result['merchant_id']) &&  $result['tracking_id'] == $user->pp_tracking_id  ) {
                            $this->userRepository->updateUserPpData(
                                [
                                    'tracking_id' => $result['tracking_id'],
                                    'merchant_id' => $result['merchant_id'],
                                ], $user->id);
                            $result = $this->payPalClient->showReferralStatus($result['merchant_id']);
                            $result = $this->preparationPpMerchantAccount($result);
                        }
                    } // end 3 step

                } else {
                    // якщо нема вертаємо силку для реєстрації
                    $result = [
                        "actionUrl" => $this->getRegistrationMerchantLink($user),
                        'status' => "Not activated",
                        'ppMerchantId' => "",
                        'message' => "Go to PayPal."
                    ];
                } // end 2 step
            } // end 1 step

            if (isset($result['error'])) {
                Log::channel('paypal')->error("createPartnerReferral for {$user->id} is fail", $result['error']);
                throw new \Exception($result['error']['message']);
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            Log::channel('paypal')->error("createPartnerReferral for {$user->id} is fail");
            throw new \Exception("createPartnerReferral for {$user->id} is fail");
        }

    }

    /**
     * @param $data
     * @return array
     */
    protected function preparationPpMerchantAccount($data): array
    {
        // $data['primary_email_confirmed'] email на aypal підтверджений
        // $data['payments_receivable']
        $status = "Active"; // Pending
        $message = "";

        if ($data['payments_receivable'] != true || $data['primary_email_confirmed'] != true ) {
            $status = "Pending";
            $message = "Please complete your account setup in PayPal to start receiving the payments";
        }

        return [
            'status' => $status,
            'ppMerchantId' => $data['merchant_id'],
            'message' => $message
        ];

    }

    /**
     * @param $user
     * @return mixed|string
     * @throws Throwable
     */
    public function getRegistrationMerchantLink($user): mixed
    {
        $ppTrackingId = "pp_tracking_id_" . $user->id;
        $partnerParams = $this->buildPartnerData($ppTrackingId);

        try {
            $result = $this->payPalClient->createPartnerReferral($partnerParams);

            if (isset($result['error'])) {
                Log::channel('paypal')->error("createPartnerReferral for {$user->id} is fail", $result['error']);
                throw new \Exception($result['error']['message']);
            } else {
                $link = "";
                foreach ($result['links'] as $item) {
                    if ($item['rel'] == "action_url"){
                        $link = $item['href'];
                    }
                }
                $ppReferralId = $this->parserReferralId($link);
                $this->userRepository->updateUserPpTrackingId($ppTrackingId, $user->id);
                $this->userRepository->updateUserPpReferralId($ppReferralId, $user->id);
                return $link;
            }
        } catch (\Exception $e) {
            Log::channel('paypal')->error("createPartnerReferral for {$user->id} is fail");
            throw new \Exception("createPartnerReferral for {$user->id} is fail");
        }

    }

    /**
     * @param string $link
     * @return string|null
     */
    private function parserReferralId(string $link): ?string
    {
        if (preg_match('/referralToken=([^ ]+)/', $link, $matches)) {
            return $matches[1];
        } else {
            return null;
        }

    }

    /**
     * @param string $ppTrackingId
     * @return array
     */
    private function buildPartnerData(string $ppTrackingId): array
    {
        return [
            "tracking_id" => $ppTrackingId,
            'operations' => [
                [
                    "operation" => "API_INTEGRATION",
                    "api_integration_preference" => [
                        "rest_api_integration" => [
                            "integration_method" => "PAYPAL",
                            "integration_type" => "THIRD_PARTY",
                            "third_party_details" => [
                                "features" => [
                                    "PAYMENT",
                                    "REFUND",
                                    "PARTNER_FEE",
                                    "ACCESS_MERCHANT_INFORMATION",
                                ],
                            ]
                        ]
                    ]
                ]
            ],
            'legal_consents' => [
                [
                    "type" => "SHARE_DATA_CONSENT",
                    "granted" => true
                ]
            ],
            'products' => [
                'EXPRESS_CHECKOUT'
            ],
            "partner_config_override" => [
                "partner_logo_url" => asset('uploads/favicon.png'),
                "return_url" => config('app.url') . 'profile/edit',
                "return_url_description" => "return to the profile .",
                "action_renewal_url" => config('app.url') . 'profile/edit',
                "show_add_credit_card" => true
            ]

        ];
    }


    public function getMerchantAccountDetails($user, $request)
    {
        if ($user->bt_submerchant_id) {
            try {
                $merchantAccount = $this->gateway->merchantAccount()->find($user->bt_submerchant_id);
                return $this->_prepareMerchantAccountOutput($merchantAccount);
            } catch (\Exception $e) {
                Log::channel('braintree')->info("Submerchant Account with id {$user->bt_submerchant_id} Not Found. User Merchant Data reset");
                return null;
            }
        }
        return null;
    }



    public function _prepareMerchantAccountOutput($merchantAccount)
    {
        return [
            'id' => $merchantAccount->id,
            'status' => $merchantAccount->status,
            'individual_firstName' => isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->firstName : '',
            'individual_lastName' => isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->lastName : '',
            'individual_email' => isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->email : '',
            'individual_phone' => isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->phone : '',
            'individual_dateOfBirth' => isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->dateOfBirth : '',
            'individual_ssn' => isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->ssnLast4 : '',
            'individual_streetAddress' => isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->addressDetails->streetAddress : '',
            'individual_locality' => isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->addressDetails->locality : '',
            'individual_region' => isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->addressDetails->region : '',
            'individual_postalCode' => isset($merchantAccount->individualDetails) ? $merchantAccount->individualDetails->addressDetails->postalCode : '',

            'funding_email' => isset($merchantAccount->fundingDetails) ? $merchantAccount->fundingDetails->email : '',
            'funding_mobilePhone' => isset($merchantAccount->fundingDetails) ? $merchantAccount->fundingDetails->mobilePhone : '',
            'funding_accountNumber' => isset($merchantAccount->fundingDetails) ? $merchantAccount->fundingDetails->accountNumberLast4 : '',
            'funding_routingNumber' => isset($merchantAccount->fundingDetails) ? $merchantAccount->fundingDetails->routingNumber : '',

            'business_legalName' => isset($merchantAccount->businessDetails) ? $merchantAccount->businessDetails->legalName : '',
            'business_dbaName' => isset($merchantAccount->businessDetails) ? $merchantAccount->businessDetails->dbaName : '',
            'business_taxId' => isset($merchantAccount->businessDetails) ? $merchantAccount->businessDetails->taxId : '',
            'business_streetAddress' => isset($merchantAccount->businessDetails) ? $merchantAccount->businessDetails->addressDetails->streetAddress : '',
            'business_address_locality' => isset($merchantAccount->businessDetails) ? $merchantAccount->businessDetails->addressDetails->locality : '',
            'business_address_region' => isset($merchantAccount->businessDetails) ? $merchantAccount->businessDetails->addressDetails->region : '',
            'business_address_postalCode' => isset($merchantAccount->businessDetails) ? $merchantAccount->businessDetails->addressDetails->postalCode : '',
        ];
    }

    public function createSampleWebhookNotification($type, $subMerchantId)
    {
        $sampleNotification = $this->gateway->webhookTesting()->sampleNotification(
            $type,
            null,
            $subMerchantId
        );

        return $sampleNotification;
    }


    public function _getPaymentMethodType($paymentMethod)
    {
        return str_replace('Braintree\\', '', get_class($paymentMethod));
    }


    private function _buildMerchantAccountParametersFromInputData($inputData)
    {
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
                'ssn' => isset($inputData['individual_ssn']) ? $inputData['individual_ssn'] : ""
            ],
            'business' => [
                'legalName' => $inputData['legalName'],
                'taxId' => $inputData['taxId'],
                'address' => [
                    'streetAddress' => $inputData['individual_streetAddress'],
                    'locality' => $inputData['individual_locality'],
                    'region' => $inputData['individual_region'],
                    'postalCode' => $inputData['individual_postalCode']
                ]
            ],
            'funding' => [
//                'destination' => MerchantAccount::FUNDING_DESTINATION_BANK,
                'destination' => 'bank',
                'email' => $inputData['funding_email'] ?? null, // optional
                'mobilePhone' => isset($inputData['funding_mobilePhone']) ? trim(prepareMobileForTwilio($inputData['funding_mobilePhone']), '+') : null, // optional
                'accountNumber' => isset($inputData['funding_accountNumber']) ? $inputData['funding_accountNumber'] : null, // TODO : required with  Braintree_MerchantAccount::FUNDING_DESTINATION_BANK , instructor must provide
                'routingNumber' => isset($inputData['funding_routingNumber']) ? $inputData['funding_routingNumber'] : null // TODO : required with  Braintree_MerchantAccount::FUNDING_DESTINATION_BANK , instructor must provide
            ]
        ];
        return $merchantAccountParams;
    }


    private function _resultErrorsForResponse($result)
    {
        $returnError = '';
        $codes = [];
        foreach ($result->errors->deepAll() as $error) {
            Log::error($error->code . ": " . $error->message);
            $returnError .= $error->code . ": " . $error->message . "\n<br>";
            $codes[] = $error->code;
        }
        if ($returnError == '') {
            $returnError = $result->message;
            $codes[] = 400;
        }
        return [$returnError, $codes];
    }



    public function generateClientToken(User $user = null)
    {
        $options = [];

//        dd($user);
        try {

            if ($user && $user->braintree_customer_id) {
                $options["customerId"] = $user->braintree_customer_id;
            }

            $token = $this->gateway->clientToken()->generate($options);

            dd($token);

            return $token;


        } catch (\InvalidArgumentException $e) {
            Log::channel('braintree')->info($e->getMessage());
            $user->braintree_customer_id = null;
            $user->save();
            return $this->gateway->clientToken()->generate([]);
        }
    }

    public function findCustomer(User $user)
    {
        if (!$user->braintree_customer_id)
            return null;
        try {
            return $this->gateway->customer()->find($user->braintree_customer_id);
        } catch (NotFound $e) {
            Log::channel('braintree')->info($e->getMessage());
            $user->braintree_customer_id = null;
            $user->save();
            return null;
        }
    }


    public function getSavedCustomerPaymentMethods(User $user)
    {
        $methods = [];
        try {
            $c = $this->findCustomer($user);

            if ($c) {
                $savedMethods = $user->paymentMethods->pluck('payment_method_token')->all();

                foreach ($c->paymentMethods as $pm) {

                    if (!in_array($pm->token, $savedMethods)) // skip payment methods added when booking made but not added in profile
                        continue;

                    switch (get_class($pm)) {
                        case 'Braintree\CreditCard':
                            $methods['CreditCard'] = [
                                'last4' => $pm->last4,
                                'cardholderName' => $pm->cardholderName,
                                'expirationDate' => $pm->expirationDate,
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
        } catch (NotFound$e) {
            Log::channel('braintree')->info($e->getMessage());
            $user->braintree_customer_id = null;
            $user->save();
            return $methods;
        }
    }


    public function deleteAllCustomerPaymentMethods(User $user)
    {
        $countDeleted = 0;
        $c = $this->findCustomer($user);
        if ($c) {
            foreach ($c->paymentMethods as $pm) {
                $result = $this->deletePaymentMethod($pm->token);
                if ($result->success == true) {
                    $user->paymentMethods()->where('payment_method_token', $pm->token)->each(function ($method) {
                        $method->delete();
                    });
                    $countDeleted++;
                } else {
                    list($returnError, $codes) = $this->_resultErrorsForResponse($result);
                    throw new \Exception('Can\'t delete payment method: ' . $returnError);
                }
            }
        }
        return $countDeleted;
    }


    public function getAllCustomerPaymentMethods(User $user)
    {
        $methods = [];
        $c = $this->findCustomer($user);
        if ($c) {
            foreach ($c->paymentMethods as $pm) {
                switch (get_class($pm)) {
                    case 'Braintree\CreditCard':
                        $methods[$pm->token] = [
                            'type' => $this->_getPaymentMethodType($pm),
                            'last4' => $pm->last4,
                            'cardholderName' => $pm->cardholderName,
                            'expirationDate' => $pm->expirationDate,
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


    public function createCustomer(User $user)
    {
        if (!$user->braintree_customer_id) {
            $result = $this->gateway->customer()->create([
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'email' => $user->email,
                'phone' => presentMobilePhone($user->profile->mobile_phone)
            ]);

            if ($result && $result->success) {
                $user->braintree_customer_id = $result->customer->id;
                $user->save();
            } else {
                list($returnError, $codes) = $this->_resultErrorsForResponse($result);
                throw new \Exception('Can\'t create customer: ' . $returnError);
            }
        }
        return $user;
    }


    public function updateCustomer(User $user)
    {
        $result = $this->gateway->customer()->update($user->braintree_customer_id,
            [
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'email' => $user->email,
                'phone' => presentMobilePhone($user->profile->mobile_phone)
            ]);

        if ($result && $result->success) {
        } else {
            list($returnError, $codes) = $this->_resultErrorsForResponse($result);
            throw new \Exception('Can\'t update customer:' . $returnError);
        }
    }


    public function createPaymentMethod(User $user, $paymentMethodNonce, $deviceData = null)
    {
        if (!$user->braintree_customer_id) {
            $user = self::createCustomer($user);
        }
        $options = [
            'customerId' => $user->braintree_customer_id,
            'paymentMethodNonce' => $paymentMethodNonce
        ];
        if ($deviceData)
            $options['deviceData'] = $deviceData;
        $result = $this->gateway->paymentMethod()->create($options);
        if ($result->success == true) {

            return ['token' => $result->paymentMethod->token, 'type' => $this->_getPaymentMethodType($result->paymentMethod)];
        } else {
            list($returnError, $codes) = $this->_resultErrorsForResponse($result);
            throw new \Exception('Can\'t create payment method: ' . $returnError);
        }
        return null;
    }


    public function deletePaymentMethod($token)
    {
        return $this->gateway->paymentMethod()->delete($token); // Braintree_Exception_NotFound
    }


    public function findPaymentMethod($token)
    {
        return $this->gateway->paymentMethod()->find($token); // Braintree_Exception_NotFound
    }


    public function setAsDefaultMethod($token)
    {
        return $this->gateway->paymentMethod()->update(
            $token,
            [
                'options' => [
                    'makeDefault' => true
                ]
            ]
        );
    }


    public function createSellBookingTransactionAndHoldInEscrow($subMerchantId, $paymentMethodVaultToken, $booking, $serviceFee, $expectedBrainTreeFee)
    {
        $description = "{$booking->lesson->genre->title} Lesson #{$booking->lesson_id}, booking #{$booking->id}, (instructor #{$booking->instructor_id})";
        $lineItems = [
            [
                'description' => $description,
                'kind' => 'debit',
                'name' => "Lesson #{$booking->lesson_id} Booking ",
                'quantity' => 1,
                'totalAmount' => round($booking->spot_price + $serviceFee + $expectedBrainTreeFee, 2),
                'unitAmount' => round($booking->spot_price + $serviceFee + $expectedBrainTreeFee, 2)
            ]
        ];
        $options = [
            'submitForSettlement' => true,
            'holdInEscrow' => true,
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
            'merchantAccountId' => $subMerchantId,
            'amount' => number_format((float)$booking->spot_price + $serviceFee + $expectedBrainTreeFee, 2, '.', ''),
            'paymentMethodToken' => $paymentMethodVaultToken,
            'serviceFeeAmount' => number_format((float)$serviceFee + $expectedBrainTreeFee, 2, '.', ''),
            'options' => $options,
            'orderId' => str_pad($booking->id, 6, "0", STR_PAD_LEFT),
            'customer' => $customer,
            'descriptor' => $descriptor,
            'lineItems' => $lineItems
        ]);

        if ($result->success) {
            return $result->transaction;
        } else {
            list($returnError, $codes) = $this->_resultErrorsForResponse($result);
            throw new \Exception('Can\'t create transaction: ' . $returnError);
        }
    }


    public function createPurchasereLessonTransactionAndHoldInEscrow($subMerchantId, $paymentMethodVaultToken, $purchasedLesson, $serviceFee = 0, $expectedBrainTreeFee = 0)
    {

        $description = "{$purchasedLesson->preRecordedLesson->genre->title} Pre Recorded Lesson #{$purchasedLesson->pre_r_lesson_id}, booking #{$purchasedLesson->id}, (instructor #{$purchasedLesson->preRecordedLesson->instructor_id})";
        $lineItems = [
            [
                'description' => $description,
                'kind' => 'debit',
                'name' => "Lesson #{$purchasedLesson->pre_r_lesson_id} Booking ",
                'quantity' => 1,
                'totalAmount' => $purchasedLesson->preRecordedLesson->price + $serviceFee + $expectedBrainTreeFee,
                'unitAmount' => $purchasedLesson->preRecordedLesson->price + $serviceFee + $expectedBrainTreeFee
            ]
        ];
        $options = [
            'submitForSettlement' => true,
            'holdInEscrow' => true,
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
            'merchantAccountId' => $subMerchantId,
            'amount' => number_format((float)$purchasedLesson->preRecordedLesson->price + $serviceFee + $expectedBrainTreeFee, 2, '.', ''),
            'paymentMethodToken' => $paymentMethodVaultToken,
            'serviceFeeAmount' => number_format((float)$serviceFee + $expectedBrainTreeFee, 2, '.', ''),
            'options' => $options,
            'orderId' => str_pad($purchasedLesson->id, 6, "0", STR_PAD_LEFT),
            'customer' => $customer,
            'descriptor' => $descriptor,
            'lineItems' => $lineItems
        ]);


        if ($result->success) {
            return $result->transaction;
        } else {
            list($returnError, $codes) = $this->_resultErrorsForResponse($result);
            throw new \Exception('Can\'t create transaction: ' . $returnError);
        }
    }


    public function cancelTransaction($transactionId)
    {
        try {
            $transaction = $this->gateway->transaction()->find($transactionId);
            $transaction->escrowStatus; // \Braintree_Transaction::ESCROW_HELD
            if (in_array($transaction->status, [Braintree\Transaction::SETTLED, Braintree\Transaction::SETTLING])) {
                $result = $this->gateway->transaction()->refund($transactionId);
            } else {
                $result = $this->gateway->transaction()->void($transactionId);
            }

            if (!$result->success) {
                list($returnError, $codes) = $this->_resultErrorsForResponse($result);
                throw new \Exception($returnError, $codes[0]);
            }
            return true;
        } catch (Braintree\Exception\NotFound $e) {
            throw new \Exception('Can\'t cancel transaction. Transaction ' . $transactionId . ' not found', 404);
        }


        $result = $this->gateway->transaction()->refund($transactionId);
        if ($result->success) {
            return true;
        } else {
            list($returnError, $codes) = $this->_resultErrorsForResponse($result);
            throw new \Exception($returnError, $codes[0]);
        }
    }

    public function releaseTransactionFromEscrow($transactionId)
    {
        try {
            $result = $this->gateway->transaction()->releaseFromEscrow($transactionId);
            if ($result->success) {
                return true;
            } else {
                list($returnError, $codes) = $this->_resultErrorsForResponse($result);
                throw new \Exception($returnError, $codes[0]);
            }
        } catch (Braintree\Exception\NotFound $e) {
            throw new \Exception('Can\'t release transaction. Transaction ' . $transactionId . ' not found', 404);
        }
    }


}
