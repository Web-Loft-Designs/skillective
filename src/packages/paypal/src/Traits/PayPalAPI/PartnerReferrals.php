<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;
use Throwable;

trait PartnerReferrals
{
    /**
     * Create a Partner Referral.
     *
     * @param array $partner_data
     *
     * @return array|bool|float|int|object|string|null
     *
     * @throws Throwable
     *
     * @see https://developer.paypal.com/docs/api/partner-referrals/v2/#partner-referrals_create
     */
    public function createPartnerReferral(array $partner_data)
    {
        $this->apiEndPoint = 'v2/customer/partner-referrals';

        $this->options['json'] = $partner_data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Get Partner Referral Details.
     *
     * @param string $partner_referral_id
     *
     * @return array|bool|float|int|object|string|null
     *
     * @throws Throwable
     *
     * @see https://developer.paypal.com/docs/api/partner-referrals/v2/#partner-referrals_read
     */
    public function showReferralData(string $partner_referral_id)
    {
        $this->apiEndPoint = "v2/customer/partner-referrals/{$partner_referral_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * @param string $referral_tracking_id
     * @return array|bool|float|int|object|string|null
     * @throws Throwable
     */
    public function showPartnerReferralId(string $referral_tracking_id)
    {
        $master_partner_id = $this->getMasterPartnerId();

        $this->apiEndPoint = "v1/customer/partners/{$master_partner_id}/merchant-integrations?tracking_id={$referral_tracking_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * @param string $referral_id
     * @return array|bool|float|int|object|string|null
     * @throws Throwable
     */
    public function showReferralStatus(string $referral_id)
    {
        $master_partner_id = $this->getMasterPartnerId();
        $this->apiEndPoint = "v1/customer/partners/{$master_partner_id}/merchant-integrations/{$referral_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
