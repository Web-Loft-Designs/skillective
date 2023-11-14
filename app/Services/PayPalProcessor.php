<?php

namespace App\Services;

use App\Repositories\UserRepository;
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
     * @return string
     */
    public static function getClientId(): string
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
    public static function getEnvironment(): string
    {
        return config('paypal.mode');
    }

    /**
     * @return string
     */
    public static function getEnvironmentUrl(): string
    {
        if (config('paypal.mod') == 'live') {
            $Url = 'https://www.paypal.com/';
        } else {
            $Url = 'https://www.sandbox.paypal.com/';
        }
        return $Url;
    }

    public static function getMasterMerchantId(): string
    {
        if (config('paypal.mod') == 'live') {
            $string = config('paypal.live.master_partner_id');
        } else {
            $string = config('paypal.sandbox.master_partner_id');
        }
        return $string;
    }

    public static function getBnCde(): string
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
                if (isset($result['error']) && $result['error']['name'] == "USER_BUSINESS_ERROR" ) {
                    // якщо є помилка значити інструктор не проходив по попередній силці генеруємо нову силку для реєстрації
                    $data = $this->getRegistrationMerchantLink($user);
                    $result = [
                        "actionUrl" =>$data['actionUrl'],
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

            if($data['permissionsGranted'] == "true" &&
                $data['consentStatus'] == "true"     &&
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
        if ( !$user->pp_merchant_id) {
            try {
                $response =  $this->payPalClient->showPartnerReferralId($user->pp_tracking_id);
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

        if($data['payments_receivable'] && $data['primary_email_confirmed']) {
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

}
