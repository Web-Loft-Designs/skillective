<?php

namespace Srmklive\PayPal\Traits;

use Srmklive\PayPal\Services\PayPal;
use Psr\Http\Message\StreamInterface;
use Throwable;

trait PayPalAPI
{
    use PayPalAPI\Trackers;
    use PayPalAPI\CatalogProducts;
    use PayPalAPI\Disputes;
    use PayPalAPI\DisputesActions;
    use PayPalAPI\Identity;
    use PayPalAPI\Invoices;
    use PayPalAPI\InvoicesSearch;
    use PayPalAPI\InvoicesTemplates;
    use PayPalAPI\Orders;
    use PayPalAPI\PartnerReferrals;
    use PayPalAPI\PaymentExperienceWebProfiles;
    use PayPalAPI\PaymentMethodsTokens;
    use PayPalAPI\PaymentAuthorizations;
    use PayPalAPI\PaymentCaptures;
    use PayPalAPI\PaymentRefunds;
    use PayPalAPI\Payouts;
    use PayPalAPI\ReferencedPayouts;
    use PayPalAPI\BillingPlans;
    use PayPalAPI\Subscriptions;
    use PayPalAPI\Reporting;
    use PayPalAPI\WebHooks;
    use PayPalAPI\WebHooksVerification;
    use PayPalAPI\WebHooksEvents;

    /**
     * Login through PayPal API to get access token.
     *
     * @return array|bool|float|int|object|string|null
     *
     * @throws Throwable
     *
     * @see https://developer.paypal.com/docs/api/get-an-access-token-curl/
     * @see https://developer.paypal.com/docs/api/get-an-access-token-postman/
     */
    public function getAccessToken()
    {
        $this->apiEndPoint = 'v1/oauth2/token';

        $this->options['auth'] = [$this->config['client_id'], $this->config['client_secret']];
        $this->options[$this->httpBodyParam] = [
            'grant_type' => 'client_credentials',
        ];

        $response = $this->doPayPalRequest();

        unset($this->options['auth']);
        unset($this->options[$this->httpBodyParam]);

        if (isset($response['access_token'])) {
            $this->setAccessToken($response);
        }

        return $response;
    }

    /**
     * @return array|bool|float|int|object|string|null
     * @throws Throwable
     */
    public function getCustomerAccessToken(string $customerId = null)
    {
        $this->apiEndPoint = 'v1/oauth2/token';

        $this->setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        $this->options['auth'] = [$this->config['client_id'], $this->config['client_secret']];

        $this->options[$this->httpBodyParam] = [
            'grant_type'    => 'client_credentials',
            'response_type' => 'id_token',
        ];

        if ($customerId) {
            $this->options[$this->httpBodyParam]['target_customer_id'] = $customerId;
        }

        return $this->doPayPalRequest();

    }

    /**
     * Set PayPal Rest API access token.
     *
     * @param array $response
     *
     * @return void
     */
    public function setAccessToken(array $response)
    {
        $this->access_token = $response['access_token'];

        $this->setPayPalAppId($response);

        $this->setRequestHeader('Authorization', "{$response['token_type']} {$this->access_token}");
    }

    /**
     * Set PayPal App ID.
     *
     * @param array $response
     *
     * @return void
     */
    private function setPayPalAppId(array $response)
    {
        $app_id = empty($response['app_id']) ? $this->config['app_id'] : $response['app_id'];

        $this->config['app_id'] = $app_id;
    }

    /**
     * Set records per page for list resources API calls.
     *
     * @param int $size
     *
     * @return PayPal
     */
    public function setPageSize(int $size): PayPal
    {
        $this->page_size = $size;

        return $this;
    }

    /**
     * Set the current page for list resources API calls.
     *
     * @param int $size
     *
     * @return PayPal
     */
    public function setCurrentPage(int $page): PayPal
    {
        $this->current_page = $page;

        return $this;
    }

    /**
     * Toggle whether totals for list resources are returned after every API call.
     *
     * @param bool $totals
     *
     * @return PayPal
     */
    public function showTotals(bool $totals): PayPal
    {
        $this->show_totals = $totals;

        return $this;
    }
}
