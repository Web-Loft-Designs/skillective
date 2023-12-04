<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;

class PayPalProcessor
{

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
     * @throws Exception
     */
    protected function getRandomString(): string
    {
        $randomBytes = random_bytes(100);
        $randomString = base64_encode($randomBytes);
        return substr(str_replace(['/', '+', '='], '', $randomString), 0, 100);
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
            $Url = 'https://www.paypal.com/';
        } else {
            $Url = 'https://www.sandbox.paypal.com/';
        }
        return $Url;
    }

    public function getMasterMerchantId(): string
    {
        if (config('paypal.mod') == 'live') {
            $string = config('paypal.live.master_partner_id');
        } else {
            $string = config('paypal.sandbox.master_partner_id');
        }
        return $string;
    }

    public function getBnCde(): string
    {
        if (config('paypal.mod') == 'live') {
            $string = config('paypal.live.bn_code');
        } else {
            $string = config('paypal.sandbox.bn_code');
        }
        return $string;
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
                        'status' => "Not activated",
                        'ppMerchantId' => "",
                        'message' => "Go to PayPal."
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

                    return $this->parseReferralStatus($result, $user);

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
                'status' => "Not activated",
                'ppMerchantId' => "",
                'message' => "Go to PayPal."
            ];
        }
        return $result;
    }

    public function handleRegisterMerchant($data): array
    {
        if (isset($data['merchantId'])) {
            $user = $this->userRepository->where('pp_tracking_id', $data['merchantId'])->first();
            $this->userRepository->updateUserPpData(
                [
                    'merchant_id' => $data['merchantIdInPayPal'],
                    'account_status' => $data['accountStatus']
                ], $user->id);

            if ($data['permissionsGranted'] == "true" &&
                $data['consentStatus'] == "true" &&
                $data['isEmailConfirmed'] == 'true') {
                $status = "Active";
                $message = "Pay Pal is connected";
            } else {
                $status = "Pending";
                $message = "Please complete your account setup in PayPal to start receiving the payments";
            }

            return [
                'status' => $status,
                'ppMerchantId' => $data['merchantIdInPayPal'],
                'message' => $message
            ];

        } else {
            return [
                'status' => "Not activated",
                'ppMerchantId' => '',
                'message' => "incorrect data"
            ];
        }

    }


    protected function parseReferralStatus($data, $user): array
    {
//        формування інфи для фронта Статус треба допрацювати і меседж також
        // тут можна боробити більше інфи про статус інтеграції продавця та які функції йому доступні

        if ($data['payments_receivable'] && $data['primary_email_confirmed']) {
            $status = "Active";
            $message = "Pay Pal is connected";
        } else {
            $status = "Pending";
            $message = "Please complete your account setup in PayPal to start receiving the payments";
        }
        return [
            'status' => $status,
            'ppMerchantId' => $user->pp_merchant_id,
            'message' => $message,
            'activePayOutMethods' => $data['products']
        ];
    }

    protected function getRegistrationMerchantLink($user): array
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
     * @return array
     */
    protected function buildPartnerData(string $ppTrackingId): array
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
                                    "DELAY_FUNDS_DISBURSEMENT",
                                    "ADVANCED_TRANSACTIONS_SEARCH",
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
                'EXPRESS_CHECKOUT',
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


    public function createSellBookingTransactionAndHoldInEscrow($paymentMethodVaultToken, $booking, $totalServiceFee, $processorFee)
    {

        // в залежності що прийде з фронта token paypal venmo
        $userPaymentSource = [
            'card' => ['vault_id' => $paymentMethodVaultToken],
//            'paypal' => [ 'vault_id' => $paymentMethodVaultToken ],
//            'venmo' => [ 'vault_id' => $paymentMethodVaultToken ]
        ];

        $currency = $this->payPalClient->getCurrency();
        $description = "{$booking->lesson->genre->title} Lesson #{$booking->lesson_id}, booking #{$booking->id}, (instructor #{$booking->instructor_id})";
        $totalAmount = round($booking->spot_price + $totalServiceFee + $processorFee, 2);
//        $ppFee = number_format((float)$processorFee, 2, '.', '');
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
        ];
        try {

            $order = $this->payPalClient->setRequestHeaders([
                'PayPal-Request-Id' => $this->getRandomString(),
                'PayPal-Partner-Attribution-Id' => $this->getBnCde()
            ])->createOrder($data);

            if (!isset($order['error'])) {
                return $this->payPalClient->setRequestHeaders([
                    'PayPal-Request-Id' => $this->getRandomString(),
                    'PayPal-Partner-Attribution-Id' => $this->getBnCde()
                ])->capturePaymentOrder($order['id'], ['payment_source' => $userPaymentSource]);


            } else {
                Log::channel('paypal')->error('Can\'t create transaction: ' . $order['error']['message']);
                throw new Exception('Can\'t create transaction: ');
            }

        } catch (Exception $e) {
            Log::channel('paypal')->error('Can\'t create transaction: ');
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

    public function createSellPurchasereLessonTransaction($subMerchantId, $paymentMethodVaultToken, $purchasedLesson, $serviceFee, $processorFee)
    {
        // в залежності що прийде з фронта token paypal venmo
        $userPaymentSource = [
            'card' => ['vault_id' => $paymentMethodVaultToken],
//            'paypal' => ['vault_id' => $paymentMethodVaultToken],
//            'venmo' => [ 'vault_id' => $paymentMethodVaultToken ]
        ];
        $description = "{$purchasedLesson->preRecordedLesson->title} Lesson #{$purchasedLesson->pre_r_lesson_id}, booking #{$purchasedLesson->id}, (instructor #{$purchasedLesson->instructor_id})";
        $totalAmount = round($purchasedLesson->price + $serviceFee + $processorFee, 2);
        $currency = $this->payPalClient->getCurrency();
        $platformFee = number_format((float)$serviceFee, 2, '.', '');

        $data = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    'reference_id' => "booking_" . $purchasedLesson->id,
                    'description' => $description,
                    'custom_id' => "booking_" . $purchasedLesson->id,
                    'invoice_id' => "booking_" . $purchasedLesson->id,
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
        ];

        try {
            $order = $this->payPalClient->setRequestHeaders([
                'PayPal-Request-Id' => $this->getRandomString(),
                'PayPal-Partner-Attribution-Id' => $this->getBnCde()
            ])->createOrder($data);

            if (!isset($order['error'])) {

                return $this->payPalClient->setRequestHeaders([
                    'PayPal-Request-Id' => $this->getRandomString(),
                    'PayPal-Partner-Attribution-Id' => $this->getBnCde()
                ])->capturePaymentOrder($order['id'], ['payment_source' => $userPaymentSource]);

            } else {
                Log::channel('paypal')->error('Can\'t create transaction: ' . $order['error']['message']);
                throw new Exception('Can\'t create transaction: ');
            }

        } catch (Exception $e) {
            Log::channel('paypal')->error('Can\'t create transaction: ');
            throw new Exception('Can\'t create transaction: ');
        }
    }


    public function getSavedCustomerPaymentMethods(User $user): array
    {
        if (!$user->pp_customer_id) {
            return [];
        }
        $userPaymentMethods = $user->findPaymentMethod()->get();
        if (!$userPaymentMethods) {
            return [];
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
                                'type' => $key,
                                'last_digits' => "",
                                'brand' => "",
                                'is_default' => '',
                                'token' => ""
                            ];
                            break;
                        case 'venmo':
                            $methods['venmo'] = [
                                'payment_id' => $method->id,
                                'type' => $key,
                                'last_digits' => "",
                                'brand' => "",
                                'is_default' => '',
                                'token' => ""
                            ];
                            break;
                        default:
                            $methods['NaN'] = [
                                'payment_id' => $method->id,
                                'type' => $key,
                                'last_digits' => null,
                                'brand' => null,
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
//                повернкти результат
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
