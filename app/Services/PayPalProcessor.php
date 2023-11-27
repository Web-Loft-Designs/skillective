<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPaymentMethod;
use App\Repositories\UserRepository;
use Braintree\Exception\NotFound;
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


    public function getPpAccessToken()
    {
        $payPalClient = new PayPalClient();
        $response = $payPalClient->getAccessToken();
        $token = $response['access_token'];
        return $token;
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
        if ($user->pp_referral_id) {
            try {
                $ppMerchantId = $this->getPpMerchantId($user);
                $result = $this->payPalClient->showReferralStatus($ppMerchantId);
                if (isset($result['error']) && $result['error']['name'] == "USER_BUSINESS_ERROR") {
                    // якщо є помилка значити інструктор не проходив по попередній силці генеруємо нову силку для реєстрації
                    $data = $this->getRegistrationMerchantLink($user);
                    $result = [
                        "actionUrl" => $data['actionUrl'],
                        'status' => "Not activated",
                        'ppMerchantId' => "",
                        'message' => "Go to PayPal."
                    ];
                } else {
                    $result = $this->parseReferralStatus($result, $user);
                }

            } catch (\Exception $e) {
                Log::channel('paypal')->error("show Referral Status PayPal for user {$user->id} is fail");
                throw new \Exception("show Referral Status PayPal for user {$user->id} is fail");
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

            $result = [
                'status' => $status,
                'ppMerchantId' => $data['merchantIdInPayPal'],
                'message' => $message
            ];

        } else {
            $result = [
                'status' => "Not activated",
                'ppMerchantId' => '',
                'message' => "incorrect data"
            ];
        }

        return $result;

    }

    protected function getPpMerchantId($user): string
    {
        if (!$user->pp_merchant_id) {
            try {
                $response = $this->payPalClient->showPartnerReferralId($user->pp_tracking_id);
                $this->userRepository->updateUserPpMerchantId($response['merchant_id'], $user->id);
                return $response['merchant_id'];

            } catch (\Exception $e) {
                Log::channel('paypal')->error("get getPpMerchantId for {$user->id} is fail");
                throw new \Exception("server error");
            }
        } else {
            return $user->pp_merchant_id;
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
                Log::channel('paypal')->error("createPartnerReferral for {$user->id} is fail", $result['error']);
                throw new \Exception($result['error']['message']);
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
        } catch (\Exception $e) {
            Log::channel('paypal')->error("createPartnerReferral for {$user->id} is fail");
            throw new \Exception("createPartnerReferral for {$user->id} is fail");
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
                                    "ACCESS_MERCHANT_INFORMATION",
                                    "ADVANCED_TRANSACTIONS_SEARCH",
                                    "VAULT"
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
//                'PAYMENT_METHODS', при першій інтеграції можна запростити тільки 1 параметр остальні окремо
//                'PPCP',
//                'ADVANCED_VAULTING'
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


    public function createSellBookingTransactionAndHoldInEscrow($paymentMethodVaultToken, $booking, $serviceFee, $expectedBrainTreeFee)
    {
        // в залежності що прийде з фронта token paypal venmo
//        $paymentSource = [
//            'card' => [
//                'vault_id' => $paymentMethodVaultToken
//            ],
//            'paypal' => [
//                'vault_id' => $paymentMethodVaultToken
//            ],
//            'venmo' => [
//                'vault_id' => $paymentMethodVaultToken
//            ]
//        ];


        $description = "{$booking->lesson->genre->title} Lesson #{$booking->lesson_id}, booking #{$booking->id}, (instructor #{$booking->instructor_id})";
        $totalAmount = round($booking->spot_price + $serviceFee + $expectedBrainTreeFee, 2);
        $currency = $this->payPalClient->getCurrency();
        $handlingFee = number_format((float)$expectedBrainTreeFee, 2, '.', '');
        $platformFee = number_format((float)$serviceFee, 2, '.', '');
        $subMerchantId = $booking->instructor->pp_merchant_id;
        $subMerchantEmail = $booking->instructor->email;

        $data = [
            "intent" => "CAPTURE",
            'payment_source' => [
                'card' => [
                    'vault_id' => $paymentMethodVaultToken
                ],
            ],
            "purchase_units" => [
                [
                    'reference_id' => "booking_" . $booking->id,
                    'description' => $description,
                    'custom_id' => "booking_" . $booking->id,
                    'invoice_id' => "booking_" . $booking->id,
                    'soft_descriptor' => "*lesson*" . $booking->lesson_id,
                    'items' => [
                        [
                            'name' => $booking->lesson->genre->title . " Lesson",
                            'quantity' => 1,
                            'description' => $description,
                            'sku' => "lesson_" . $booking->lesson_id,
                            'unit_amount' => [
                                "currency_code" => $currency,
                                "value" => $booking->spot_price,
                            ],
                            'tax' => [
                                "currency_code" => $currency,
                                "value" => $platformFee,
                            ]
                        ]
                    ],
                    "amount" => [
                        "currency_code" => $currency,
                        "value" => $totalAmount,
                        'breakdown' => [
                            'item_total' => [
                                "currency_code" => $currency,
                                "value" => $booking->spot_price,
                            ],
                            'handling' => [
                                "currency_code" => $currency,
                                "value" => $handlingFee,
                            ],
                            'tax_total' => [
                                "currency_code" => $currency,
                                "value" => $platformFee,
                            ]
                        ],
                    ],
                    'payer' => [
                        'email_address' => $subMerchantEmail,
                        'merchant_id' => $subMerchantId
                    ],
                    'payment_instruction' => [
                        'platform_fees' => [
                            [
                                'amount' => [
                                    "currency_code" => $currency,
                                    "value" => $platformFee,
                                ],
                                'pater' => [
                                    'merchant_id' => $this->getMasterMerchantId()
                                ]
                            ]
                        ],
                        'disbursement_mode' => "DELAYED",
                    ]

                ],
            ],
        ];
        try {
            $transaction = $this->payPalClient->setRequestHeaders([
                'PayPal-Request-Id' => config('app.key'),
                'PayPal-Partner-Attribution-Id' => $this->getBnCde()
                ])->createOrder($data);
            if (!isset($transaction['error'])) {
                return $transaction;
            } else {
                Log::channel('paypal')->error('Can\'t create transaction: ' . $transaction['error']['message']);
                throw new \Exception('Can\'t create transaction: ');
            }

        } catch (\Exception $e) {
            Log::channel('paypal')->error('Can\'t create transaction: ');
            throw new \Exception('Can\'t create transaction: ');
        }

    }


    public function getAllCustomerPaymentMethods(User $user): array
    {
        if (!$user->pp_customer_id) {
            return [];
        }

        try {

            $result = $this->payPalClient->setCustomerSource($user->pp_customer_id)->listPaymentSourceTokens(totals: false);
            dd($result);
            foreach ($result['payment_tokens'] as $paymentToken) {
//dd($paymentToken);
                // звірити з нашою базою даних і також удалити токени
                $deleteR = $this->payPalClient->deletePaymentSourceToken($paymentToken['id']);

//                dd($deleteR);

            }


            dd("stop");
            return $result;

        } catch (\Exception $e) {
            Log::channel('paypal')->error("found payment method for {$user->id} is fail");
            throw new \Exception("found payment method for {$user->id} is fail");
        }
    }

    public function getSavedCustomerPaymentMethods(User $user): array
    {
        if (!$user->pp_customer_id) {
            return [];
        }
        $issetTokens = $user->findPaymentMethod()->get();
        if (!$issetTokens) {
            return [];
        }

        try {
            $methods = [];
            foreach ($issetTokens as $token) {

                $result = $this->payPalClient->showPaymentSourceTokenDetails($token->payment_method_token);

                foreach ($result['payment_source'] as $key => $source) {

                    switch ($key) {
                        case 'card':
                            $item = [
                                'payment_id' => $token->id,
                                'type' => $key,
                                'last_digits' => $source['last_digits'],
                                'brand' => $source['brand'],
                            ];
                            break;
                        default:
                            $item = [
                                'payment_id' => $token->id,
                                'type' => $key,
                                'last_digits' => null,
                                'brand' => null,
                            ];
                    }
                    $methods[] = $item;
                }

            }

            return $methods;

        } catch (\Exception $e) {
            Log::channel('paypal')->error("found payment method for {$user->id} is fail");
            throw new \Exception("found payment method for {$user->id} is fail");
        }
    }

    public function createPaymentMethod(User $user, $paymentMethodNonce): array
    {
        $data = [
            "payment_source" => [
                "token" => [
                    'id' => $paymentMethodNonce,
                    'type' => "SETUP_TOKEN"
                ]
            ],
            'customer' => [
                'id' => $user->pp_customer_id
            ],
        ];

        try {

            $result = $this->payPalClient->createPaymentSourceToken($data);

            if (!isset($result['error'])) {

                $paymentMethodType = $this->parseMethodType($result['payment_source']);

                $this->userRepository->savePaymentMethod($user, ['token' => $result['id'], 'type' => $paymentMethodType]);
                return ['token' => $result['id'], 'type' => $paymentMethodType];
            } else {
                Log::channel('paypal')->error("create payment method for {$user->id} is fail  " . $result['error']['message']);
                throw new \Exception('Can\'t create payment method: ' . $user->id);
            }

        } catch (\Exception $e) {
            Log::channel('paypal')->error("create payment method for {$user->id} is fail");
            throw new \Exception("'Can\'t create payment method for {$user->id} ");
        }

    }

    public function getVaultSetupToken($user): string
    {
        $data = [
            'customer' => [
                'id' => 'customer_' . $user->id,
                "merchant_customer_id" => $user->email,
            ],
            'payment_source' => [
                'card' => (object)[],
            ]
        ];

        try {

            $response = $this->payPalClient->createPaymentSetupToken($data);
            if (!isset($response['error'])) {
                $user->pp_customer_id = $response['customer']['id'];
                $user->save();
                return $response['id'];
            } else {
                Log::channel('paypal')->error("create Payment Setup Token for {$user->id} is fail  " . $response['error']['message']);
                throw new \Exception("create Payment Setup Token for {$user->id} is fail");
            }

        } catch (\Exception $e) {
            Log::channel('paypal')->error("create Payment Setup Token for {$user->id} is fail");
            throw new \Exception("create Payment Setup Token for {$user->id} is fail");
        }

    }


    protected function parseMethodType($data): string
    {
        foreach ($data as $key => $item) {

            $type = match ($key) {
                "card" => $item['brand'],
                'paypal' => "PayPal",
                'venmo' => 'Venmo',
                default => "Unknown Method",
            };
            break;

        }

        return $type;
    }

    public function releaseTransactionFromEscrow($transactionId): array
    {
        $order = $this->payPalClient->showOrderDetails($transactionId);
        $referenceId = $order['purchase_units'][0]['payments']['captures'][0]['id'];

        return $this->payPalClient->setRequestHeaders([
            'PayPal-Request-Id' => config('app.key'),
            'PayPal-Partner-Attribution-Id' => $this->getBnCde()
        ])->createReferencedBatchPayoutItem([
            "reference_id" => $referenceId,
            "reference_type" => "TRANSACTION_ID"
        ]);

    }


}
