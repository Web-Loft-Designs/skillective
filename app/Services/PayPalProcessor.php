<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\PurchasedLesson;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;

class PayPalProcessor
{
    const STATUS_ACTIVE = 'active';
    const STATUS_NOT_CONNECTED = 'not connected';
    const STATUS_SUSPENDED = 'suspended';
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var PayPalClient
     */
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
     * @return bool
     * @throws Throwable
     */
    public function checkConnect(): bool
    {
        $response = $this->payPalClient->getAccessToken();
        if (!isset($response['error']) && isset($response['access_token']) ) {
            return true;
        }
        return false;
    }
    /**
     * @throws Exception
     */
    protected function getRandomString(): string
    {
        $randomBytes = random_bytes(100);
        $randomString = base64_encode($randomBytes);
        return substr(str_replace(['/', '+', '='], '', $randomString), 0, 80);
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        if (config('paypal.mod') == 'live') {
            $string = config('paypal.live.client_id');
        } else {
            $string = config('paypal.sandbox.client_id');
        }
        return $string;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return config('paypal.mode');
    }

    /**
     * @return string
     */
    public function getEnvironmentUrl(): string
    {
        if (config('paypal.mod') == 'live') {
            return 'https://www.paypal.com/';
        } else {
            return 'https://www.sandbox.paypal.com/';
        }
    }

    public function getMasterMerchantId(): string
    {
        if (config('paypal.mod') == 'live') {
            return config('paypal.live.master_partner_id');
        } else {
            return config('paypal.sandbox.master_partner_id');
        }
    }

    public function getBnCde(): string
    {
        if (config('paypal.mod') == 'live') {
            return config('paypal.live.bn_code');
        } else {
            return config('paypal.sandbox.bn_code');
        }
    }

    public function checkMerchantStatus(User $user): void
    {
        if ($user->pp_merchant_id) {
            try {
                $result = $this->payPalClient->showReferralStatus($user->pp_merchant_id);
                if (!isset($result['error'])) {
                    if (!$result['payments_receivable']) {
                        $user->pp_account_status = self::STATUS_SUSPENDED;
                        $user->bt_submerchant_status_reason = "You have not provided the necessary permissions for Skillective";
                        $user->save();
                    } elseif (!$result['primary_email_confirmed']) {
                        $user->pp_account_status = self::STATUS_SUSPENDED;
                        $user->bt_submerchant_status_reason = "You have not verified your PaPal account";
                        $user->save();
                    }elseif (!isset($result['oauth_integrations'])) {
                        $user->pp_account_status = self::STATUS_SUSPENDED;
                        $user->bt_submerchant_status_reason = "You have not provided the necessary permissions for Skillective";
                        $user->save();
                    } else {
                        $user->pp_account_status = self::STATUS_ACTIVE;
                        $user->bt_submerchant_status_reason = null;
                        $user->save();
                    }
                } else {
                    Log::channel('paypal')->error("show Referral Status PayPal for user {$user->id} is fail" . $result['error']['message']);
                    throw new Exception("show Referral Status PayPal for user {$user->id} is fail" . $result['error']['message']);
                }
            } catch (Exception $e) {
                Log::channel('paypal')->error("show Referral Status PayPal for user {$user->id} is fail");
                throw new Exception("show Referral Status PayPal for user {$user->id} is fail");
            }
        }
    }

    public function getMerchantDetail($user): array
    {

        if ($user->pp_referral_id && !$user->pp_merchant_id) {
            try {
                $result = $this->payPalClient->showReferralData($user->pp_referral_id);

                if (!isset($result['error'])) {

                    $link = [];
                    foreach ($result['links'] as $item) {
                        if ($item['rel'] == "action_url") {
                            $link['actionUrl'] = $item['href'];
                        }
                    }

                    return [
                        "actionUrl" => $link['actionUrl'],
                        'status' => self::STATUS_NOT_CONNECTED,
                    ];
                } else {
                    Log::channel('paypal')->error("show Referral Status PayPal for user {$user->id} is fail" . $result['error']['message']);
                    throw new Exception("show Referral Status PayPal for user {$user->id} is fail" . $result['error']['message']);
                }
            } catch (Exception $e) {
                Log::channel('paypal')->error("show Referral Status PayPal for user {$user->id} is fail");
                throw new Exception("show Referral Status PayPal for user {$user->id} is fail");
            }

        } elseif ($user->pp_merchant_id) {
            try {
                $result = $this->payPalClient->showReferralStatus($user->pp_merchant_id);

                if (!isset($result['error'])) {

                    if ($result['payments_receivable'] && $result['primary_email_confirmed'] && isset($result['oauth_integrations'])) {
                        return [
                            'status' => self::STATUS_ACTIVE,
                            'merchantId' => $user->pp_merchant_id,
                            ];
                    } else {
                        $data = $this->getRegistrationMerchantLink($user);
                        return [
                            'status' => self::STATUS_SUSPENDED,
                            'merchantId' => $user->pp_merchant_id,
                            "actionUrl" => $data['actionUrl'],
                            ];
                    }

                } else {
                    Log::channel('paypal')->error("show Referral Status PayPal for user {$user->id} is fail" . $result['error']['message']);
                    throw new Exception("show Referral Status PayPal for user {$user->id} is fail" . $result['error']['message']);
                }
            } catch (Exception $e) {
                Log::channel('paypal')->error("show Referral Status PayPal for user {$user->id} is fail");
                throw new Exception("show Referral Status PayPal for user {$user->id} is fail");
            }
        } else {

            $data = $this->getRegistrationMerchantLink($user);
            $result = [
                "actionUrl" => $data['actionUrl'],
                'status' => self::STATUS_NOT_CONNECTED,
            ];
        }
        return $result;
    }

    public function handleRegisterMerchant($data): array
    {
        if (isset($data['merchantId'])) {
            $user = $this->userRepository->where('pp_tracking_id', $data['merchantId'])->first();

            if ($data['permissionsGranted'] == "true" && $data['consentStatus'] == "true" && $data['isEmailConfirmed'] == 'true') {
                $this->userRepository->updateUserPpData(
                    [
                        'merchant_id' => $data['merchantIdInPayPal'],
                        'account_status' => self::STATUS_ACTIVE
                    ], $user->id);

                return [
                    'status' => self::STATUS_ACTIVE,
                    'merchantId' => $user->pp_merchant_id,
                    ];

            } else {
                $data = $this->getRegistrationMerchantLink($user);
                return [
                    'status' => self::STATUS_SUSPENDED,
                    "actionUrl" => $data['actionUrl'],
                ];
            }
        } else {
            return [
                'status' => self::STATUS_NOT_CONNECTED,
            ];
        }
    }

    protected function getRegistrationMerchantLink(User $user): array
    {
        $ppTrackingId = "pp_tracking_id_" . $user->id;
        $partnerParams = $this->buildPartnerData($ppTrackingId);

        try {
            $result = $this->payPalClient->createPartnerReferral($partnerParams);

            if (isset($result['error'])) {
                Log::channel('paypal')->error("createPartnerReferral for {$user->id} is fail", $result['error']['message']);
                throw new Exception($result['error']['message']);
            } else {
                $link = [];
                foreach ($result['links'] as $item) {
                    if ($item['rel'] == "action_url") {
                        $link['actionUrl'] = $item['href'];
                    }
                }
                $ppReferralId = $this->parserReferralId($link['actionUrl']);
                $this->userRepository->updateUserPpTrackingId($ppTrackingId, $user->id);
                $this->userRepository->updateUserPpReferralId($ppReferralId, $user->id);
                return $link;
            }
        } catch (Exception $e) {
            Log::channel('paypal')->error("createPartnerReferral for {$user->id} is fail");
            throw new Exception("createPartnerReferral for {$user->id} is fail");
        }

    }

    /**
     * @param string $link
     * @return string|null
     */
    protected function parserReferralId(string $link): ?string
    {
        if (preg_match('/referralToken=([^ ]+)/', $link, $matches)) {
            return $matches[1];
        } else {
            return null;
        }

    }

    /**
     * @param string $ppTrackingId
     * @param string $step
     * @return array
     * @throws Exception
     */
    protected function buildPartnerData(string $ppTrackingId): array
    {
        $data = [
            "tracking_id" => $ppTrackingId,
            'operations' => [
                [
                    "operation" => "API_INTEGRATION",
                    "api_integration_preference" => [
                        "rest_api_integration" =>  [
                            "integration_method" => "PAYPAL",
                            "integration_type" => "THIRD_PARTY",
                            "third_party_details" => [
                                "features" => [
                                    "PAYMENT",
                                    "REFUND",
                                    "PARTNER_FEE",
                                    "DELAY_FUNDS_DISBURSEMENT",
                                    "ADVANCED_TRANSACTIONS_SEARCH",
                                    "VAULT",
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
                'PPCP',
                'ADVANCED_VAULTING'
            ],
            'capabilities' => [
                'PAYPAL_WALLET_VAULTING_ADVANCED'
            ],
            "partner_config_override" => [
                "partner_logo_url" => asset('uploads/favicon.png'),
                "return_url" => config('app.url') . 'profile/edit',
                "return_url_description" => "return to the profile .",
                "action_renewal_url" => config('app.url') . 'profile/edit',
                "show_add_credit_card" => true
            ]
        ];

        return $data;
    }

    public function createOrder($bookings)
    {
        $currency = $this->payPalClient->getCurrency();
        $data = [
            "intent" => "CAPTURE",
            "application_context" => [
                "shipping_preference" => "NO_SHIPPING"
            ],
            "payment_source" => [
                "paypal" => [
                    "attributes" => [
                        "vault" => [
                            "store_in_vault" => "ON_SUCCESS",
                            "usage_type" => "PLATFORM",
                            "customer_type" => "CONSUMER"
                        ]
                    ],
                    'experience_context' => [
                        "return_url" => config('app.url') . '/checkout',
                        "cancel_url" => config('app.url') . '/checkout'
                    ]
                ]
            ],
        ];

        foreach ($bookings as $booking) {
            if (get_class($booking) === Booking::class) {
                $description = "{$booking->lesson->genre->title} Lesson #{$booking->lesson_id}, booking #{$booking->id}, (instructor #{$booking->instructor_id})";
                $serviceFee = $booking->getBookingServiceFeeAmount();
                $virtualLessonFee = $booking->getBookingVirtualFeeAmount();
                $sklFee = number_format($serviceFee + (float) $virtualLessonFee, 2); ;
                $processorFee = $booking->getBookingPaymentProcessingFeeAmount($booking->spot_price, $sklFee);
                $totalAmount = number_format((float) $booking->spot_price + (float) $sklFee + (float) $processorFee, 2);
                $subMerchantId = $booking->instructor->pp_merchant_id;
                $subMerchantEmail = $booking->instructor->email;

                $purchaseUnits = [
                        'reference_id' => "booking_" . $booking->id,
                        'description' => $description,
                        'custom_id' => "booking_" . $booking->id,
                        'invoice_id' => "booking_" . $booking->id,
                        'soft_descriptor' => "*lesson*" . $booking->lesson_id,
                        "amount" => [
                            "currency_code" => $currency,
                            "value" => $totalAmount,
                        ],
                        'payee' => [
                            'email_address' => $subMerchantEmail,
                            'merchant_id' => $subMerchantId
                        ],
                        'payment_instruction' => [
                            'platform_fees' => [
                                [
                                    'amount' => [
                                        "currency_code" => $currency,
                                        "value" => $sklFee,
                                    ],
                                ]
                            ],
                            'disbursement_mode' => "DELAYED",
                        ]
                    ];
                $data['purchase_units'][] = $purchaseUnits;

            } elseif (get_class($booking) === PurchasedLesson::class) {

                $description = "{$booking->preRecordedLesson->title} Lesson #{$booking->pre_r_lesson_id}, booking #{$booking->id}, (instructor #{$booking->instructor_id})";
                $totalAmount = round((float) $booking->price + (float) $booking->service_fee + (float) $processorFee, 2);
                $sklFee = number_format($booking->service_fee, 2);

                $purchaseUnits =  [
                        'reference_id' => "pRlesson_" . $booking->id,
                        'description' => $description,
                        'custom_id' => "pRlesson_" . $booking->id,
                        'invoice_id' => "pRlesson_" . $booking->id,
                        'soft_descriptor' => "*lesson*" . $booking->pre_r_lesson_id,
                        "amount" => [
                            "currency_code" => $currency,
                            "value" => $totalAmount,
                        ],
                        'payee' => [
                            'email_address' => $subMerchantEmail,
                            'merchant_id' => $subMerchantId
                        ],
                        'payment_instruction' => [
                            'platform_fees' => [
                                [
                                    'amount' => [
                                        "currency_code" => $currency,
                                        "value" => $sklFee,
                                    ],
                                ]
                            ],
                            'disbursement_mode' => "DELAYED",
                        ]
                    ];
                $data['purchase_units'][] = $purchaseUnits;

            } else {
                throw new Exception('Error unknown class name');
            }

        }  // foreach end


        try {
            $order = $this->payPalClient->setRequestHeaders([
                'PayPal-Request-Id' => $this->getRandomString(),
                'PayPal-Partner-Attribution-Id' => $this->getBnCde()
            ])->createOrder($data);

            if (isset($order['error'])) {
                Log::channel('paypal')->error('Can\'t create order: ' . $order);
                throw new Exception('payment service is not available try again later');
            } else {
                return $order;
            }

        } catch (Exception $e) {
            Log::channel('paypal')->error('Can\'t create order: ' . $e->getTraceAsString());
            throw new Exception('payment service is not available try again later');
        }

    }

    public function captureOrder(string $orderId)
    {
        try {
            $order = $this->payPalClient->setRequestHeaders([
                'PayPal-Request-Id' => $this->getRandomString(),
                'PayPal-Partner-Attribution-Id' => $this->getBnCde()
            ])->capturePaymentOrder($orderId);

            if (isset($order['error'])) {
                Log::channel('paypal')->error('Can\'t create transaction: ' . $order);
                throw new Exception('Can\'t create transaction: ');
            } else {
                return $order;
            }

        } catch (Exception $e) {
            Log::channel('paypal')->error('Can\'t create transaction: '. json_encode($order));
            throw new Exception('Can\'t create transaction: ');
        }

    }

    public function createSellBookingTransactionAndHoldInEscrow($booking, $totalServiceFee, $processorFee)
    {
        $currency = $this->payPalClient->getCurrency();
        $description = "{$booking->lesson->genre->title} Lesson #{$booking->lesson_id}, booking #{$booking->id}, (instructor #{$booking->instructor_id})";
        $totalAmount = round($booking->spot_price + $totalServiceFee + $processorFee, 2);
        $sklFee = number_format((float)$totalServiceFee, 2, '.', '');
        $subMerchantId = $booking->instructor->pp_merchant_id;
        $subMerchantEmail = $booking->instructor->email;

        $data = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    'reference_id' => "booking_" . $booking->id,
                    'description' => $description,
                    'custom_id' => "booking_" . $booking->id,
                    'invoice_id' => "booking_" . $booking->id,
                    'soft_descriptor' => "*lesson*" . $booking->lesson_id,
                    "amount" => [
                        "currency_code" => $currency,
                        "value" => $totalAmount,

                    ],
                    'payee' => [
                        'email_address' => $subMerchantEmail,
                        'merchant_id' => $subMerchantId
                    ],
                    'payment_instruction' => [
                        'platform_fees' => [
                            [
                                'amount' => [
                                    "currency_code" => $currency,
                                    "value" => $sklFee,
                                ],
                            ]
                        ],
                        'disbursement_mode' => "DELAYED",
                    ]
                ],
            ],
            'payment_source' => [
                'card' => [
                    'vault_id' => $booking->payment_method_token
                ]
            ],
            "application_context" => [
                "shipping_preference" => "NO_SHIPPING"
            ],

        ];
        try {

            $order = $this->payPalClient->setRequestHeaders([
                'PayPal-Request-Id' => $this->getRandomString(),
                'PayPal-Partner-Attribution-Id' => $this->getBnCde()
            ])->createOrder($data);

            if (!isset($order['error'])) {

             return $order;

            } else {
                Log::channel('paypal')->error('Can\'t create transaction: ' . $order);
                throw new Exception('Can\'t create transaction: ');
            }

        } catch (Exception $e) {
            Log::channel('paypal')->error('Can\'t create transaction: '. json_encode($order));
            throw new Exception('Can\'t create transaction: ');
        }

    }


    /**
     * @throws Throwable
     */
    public function releaseTransactionFromEscrow($referenceId): array
    {
        return $this->payPalClient->setRequestHeaders([
            'PayPal-Request-Id' => $this->getRandomString(),
            'PayPal-Partner-Attribution-Id' => $this->getBnCde()
        ])->createReferencedBatchPayoutItem([
            "reference_id" => $referenceId,
            "reference_type" => "TRANSACTION_ID"
        ]);

    }

    public function cancelTransaction($transactionId): bool
    {
        try {
            $order = $this->payPalClient->showOrderDetails($transactionId);

            if (isset($order['purchase_units'][0]['payments']['captures'][0]['id'])) {
                $merchantId = $order['purchase_units'][0]['payee']['merchant_id'];
              $this->payPalClient->setRequestHeaders([
                    'PayPal-Request-Id' => $this->getRandomString(),
                    'PayPal-Auth-Assertion' => $this->getAuthAssertionValue($this->getClientId(), $merchantId)
                ])->refundAllCapturedPayment($order['purchase_units'][0]['payments']['captures'][0]['id']);

                return true;
            } else {
                return false;
            }

        } catch (Exception|Throwable $e) {
            Log::channel('paypal')->error('Can\'t cancel transaction. Transaction ' . $transactionId . ' not found');
            throw new Exception('Can\'t cancel transaction. Transaction ' . $transactionId . ' not found');
        }

    }

    /**
     * @param $clientId
     * @param string $sellerPayerId
     * @return string
     * create jwt token
     */
    protected function getAuthAssertionValue($clientId, string $sellerPayerId): string
    {
        $header = [
            "alg" => "none"
        ];
        $encodedHeader = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $payload = [
            "iss" => $clientId,
            "payer_id" => $sellerPayerId
        ];
        $encodedPayload = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
        return $encodedHeader . '.' . $encodedPayload . '.';
    }

    public function createSellPurchasereLessonTransaction($subMerchantId, $purchasedLesson)
    {
        $description = $purchasedLesson->preRecordedLesson->title . " Lesson #" . $purchasedLesson->pre_r_lesson_id . " purchasedLesson #" . $purchasedLesson->id . " instructor #" . $purchasedLesson->instructor_id;
        $totalAmount = number_format($purchasedLesson->price + $purchasedLesson->service_fee + $purchasedLesson->processor_fee, 2);
        $currency = $this->payPalClient->getCurrency();
        $platformFee = number_format((float)$purchasedLesson->service_fee, 2);

        $data = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    'reference_id' => "pRlesson_" . $purchasedLesson->id,
                    'description' => $description,
                    'custom_id' => "pRlesson_" . $purchasedLesson->id,
                    'invoice_id' => "pRlesson_" . $purchasedLesson->id,
                    'soft_descriptor' => "*lesson*" . $purchasedLesson->pre_r_lesson_id,
                    "amount" => [
                        "currency_code" => $currency,
                        "value" => $totalAmount,
                    ],
                    'payee' => [
                        'merchant_id' => $subMerchantId
                    ],
                    'payment_instruction' => [
                        'platform_fees' => [
                            [
                                'amount' => [
                                    "currency_code" => $currency,
                                    "value" => $platformFee,
                                ],
                            ]
                        ],
                        'disbursement_mode' => "DELAYED",
                    ]
                ],
            ],
            'payment_source' => [
                'card' => [
                    'vault_id' => $purchasedLesson->payment_method_token
                ]
            ],
            "application_context" => [
                "shipping_preference" => "NO_SHIPPING"
            ],
        ];

        try {
            $order = $this->payPalClient->setRequestHeaders([
                'PayPal-Request-Id' => $this->getRandomString(),
                'PayPal-Partner-Attribution-Id' => $this->getBnCde()
            ])->createOrder($data);

            if (isset($order['error'])) {
                Log::channel('paypal')->error('Can\'t create transaction: ' . $order);
                throw new Exception('Can\'t create transaction: ');
            } else {
                return $order;
            }

        } catch (Exception $e) {
            Log::channel('paypal')->error('Can\'t create transaction: '. json_encode($order));
            throw new Exception('Can\'t create transaction: ');
        }
    }


    public function getSavedCustomerPaymentMethods(User $user): ?array
    {
        if (!$user->pp_customer_id) {
            return null;
        }
        $userPaymentMethods = $user->findPaymentMethod()->get();
        if (!$userPaymentMethods) {
            return null;
        }

        try {
            $methods = [];
            foreach ($userPaymentMethods as $method) {
                $result = $this->payPalClient->showPaymentSourceTokenDetails($method->payment_method_token);

                foreach ($result['payment_source'] as $key => $source) {

                    switch ($key) {
                        case 'card':
                            $methods['card'] = [
                                'payment_id' => $method->id,
                                'type' => $key,
                                'last_digits' => $source['last_digits'],
                                'brand' => $source['brand'],
                            ];
                            break;
                        case 'paypal':
                            $methods['paypal'] = [
                                'payment_id' => $method->id,
                                'name' => $source['name']['full_name'],
                                'email' => $source['email_address'],
                            ];
                            break;
                        case 'venmo':
                            $methods['venmo'] = [
                                'payment_id' => $method->id,
                                'name' => $source['name']['full_name'],
                                'email' => $source['email_address'],

                            ];
                            break;
                        default:
                            $methods['NaN'] = [
                                'payment_id' => $method->id,
                            ];
                    }

                }

            }
            return $methods;

        } catch (Exception $e) {
            Log::channel('paypal')->error("found payment method for {$user->id} is fail");
            throw new Exception("found payment method for {$user->id} is fail");
        }
    }

    public function createPaymentMethod(User $user, string $paymentMethodNonce): array
    {
        $data = [
            "payment_source" => [
                "token" => [
                    'id' => $paymentMethodNonce,
                    'type' => "SETUP_TOKEN"
                ]
            ],
        ];

        try {
            $result = $this->payPalClient->createPaymentSourceToken($data);
            if (!isset($result['error'])) {
                $source = $result['payment_source'][array_key_first($result['payment_source'])];
                // зберегти або оновити
                $this->userRepository->savePaymentMethod($user, $result['id'], array_key_first($result['payment_source']));
                // зберегти pp_customer_id
                $user->pp_customer_id = $result['customer']['id'];
                $user->save();
                return ['token' => $result['id'], 'type' => array_key_first($result['payment_source']), 'source' => $source];
            } else {
                Log::channel('paypal')->error("create payment method for {$user->id} is fail  " . $result['error']['message']);
                throw new Exception('Can\'t create payment method: ' . $user->id);
            }

        } catch (Exception $e) {
            Log::channel('paypal')->error("create payment method for {$user->id} is fail");
            throw new Exception("'Can\'t create payment method for {$user->id} ");
        }

    }

    public function getVaultSetupToken($user, string $type): string
    {
        switch ($type) {
            case 'card':
                $data['payment_source'] = ['card' => (object)[]];
                break;
            case 'paypal':
                $data['payment_source'] = [
                                'paypal' => [
                                    "usage_type" => "PLATFORM",
                                    "experience_context" => [
                                        "return_url" => config('app.url') . 'profile/edit',
                                        "cancel_url" => config('app.url') . 'profile/edit'
                                    ]
                                ]
                            ];
                break;
            case 'venmo':
                $data['payment_source'] = [
                    'venmo' => [
                        "usage_type" => "PLATFORM",
                        "experience_context" => [
                            "return_url" => config('app.url') . 'profile/edit',
                            "cancel_url" => config('app.url') . 'profile/edit'
                        ]
                    ]
                ];
                break;
            default:
                throw new Exception("create Payment Setup Token for {$user->id} is fail");
        }
        if ($user->pp_customer_id) {
            $data['customer'] = [ 'id' => $user->pp_customer_id];
        }
        try {
            $response = $this->payPalClient->createPaymentSetupToken($data);
            if (!isset($response['error'])) {
                return $response['id'];
            } else {
                Log::channel('paypal')->error("create Payment Setup Token for {$user->id} is fail  " . $response['error']['message']);
                throw new Exception("create Payment Setup Token for {$user->id} is fail");
            }
        } catch (Exception $e) {
            Log::channel('paypal')->error("create Payment Setup Token for {$user->id} is fail");
            throw new Exception("create Payment Setup Token for {$user->id} is fail");
        }

    }

    public function deletePaymentMethod(string $customerId, string $token): bool
    {
        try {
           $this->payPalClient->deletePaymentSourceToken($token);
            return true;
        } catch (Exception $e) {
            Log::channel('paypal')->error("delete Payment method is fail");
            throw new Exception("delete Payment method is fail");
        }

    }


    /**
     * @param User $user
     * @return string
     * @throws Throwable
     */
    public function getDataUserIdToken(User $user): string
    {
        try {
            $client = new PayPalClient();
            $response = $client->getCustomerAccessToken($user->pp_customer_id ?? null);

            return $response['id_token'];

        } catch (Exception $e) {
            Log::channel('paypal')->error("get user id token is fail");
            throw new Exception("get user id token is fail");
        }

    }


}
