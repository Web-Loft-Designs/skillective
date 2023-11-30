<?php

namespace Srmklive\PayPal\Tests\Feature;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use PHPUnit\Framework\TestCase;
use Srmklive\PayPal\Tests\MockClientClasses;
use Srmklive\PayPal\Tests\MockRequestPayloads;
use Srmklive\PayPal\Tests\MockResponsePayloads;

class AdapterPaymentMethodTokensHelpersTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    /** @var string */
    protected static $access_token = '';

    /** @var \Srmklive\PayPal\Services\PayPal */
    protected $client;

    protected function setUp(): void
    {
        $this->client = new PayPalClient($this->getApiCredentials());

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAccessTokenResponse()
            )
        );
        $response = $this->client->getAccessToken();

        self::$access_token = $response['access_token'];

        parent::setUp();
    }

    /** @test */
    public function it_can_create_payment_token_from_a_vault_token()
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreatePaymentMethodsTokenResponse()
            )
        );

        $this->client = $this->client->setTokenSource('5C991763VB2781612', 'SETUP_TOKEN')
        ->setCustomerSource('customer_4029352050');

        $response = $this->client->sendPaymentMethodRequest();

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('customer', $response);
    }

    /** @test */
    public function it_can_create_payment_source_from_a_vault_token()
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreatePaymentSetupTokenResponse()
            )
        );

        $this->client = $this->client->setTokenSource('5C991763VB2781612', 'SETUP_TOKEN')
        ->setCustomerSource('customer_4029352050');

        $response = $this->client->sendPaymentMethodRequest(true);

        $this->assertArrayHasKey('payment_source', $response);
    }

    /** @test */
    public function it_can_create_payment_source_from_a_credit_card()
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreatePaymentSetupTokenResponse()
            )
        );

        $this->client = $this->client->setPaymentSourceCard($this->mockCreatePaymentSetupTokensParams()['payment_source']['card'])
        ->setCustomerSource('customer_4029352050');

        $response = $this->client->sendPaymentMethodRequest(true);

        $this->assertArrayHasKey('payment_source', $response);
    }

    /** @test */
    public function it_can_create_payment_source_from_a_paypal_account()
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $response_data = $this->mockCreatePaymentSetupTokenResponse();
        $response_data['payment_source']['paypal'] = $this->mockCreatePaymentSetupPayPalParams()['payment_source']['paypal'];
        unset($response_data['payment_source']['card']);

        $this->client->setClient(
            $this->mock_http_client($response_data)
        );

        $this->client = $this->client->setPaymentSourcePayPal($this->mockCreatePaymentSetupPayPalParams()['payment_source']['paypal'])
        ->setCustomerSource('customer_4029352050');

        $response = $this->client->sendPaymentMethodRequest(true);

        $this->assertArrayHasKey('payment_source', $response);
    }

    /** @test */
    public function it_can_create_payment_source_from_a_venmo_account()
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $response_data = $this->mockCreatePaymentSetupTokenResponse();
        $response_data['payment_source']['venmo'] = $this->mockCreatePaymentSetupPayPalParams()['payment_source']['paypal'];
        unset($response_data['payment_source']['card']);

        $this->client->setClient(
            $this->mock_http_client($response_data)
        );

        $this->client = $this->client->setPaymentSourceVenmo($this->mockCreatePaymentSetupPayPalParams()['payment_source']['paypal'])
        ->setCustomerSource('customer_4029352050');

        $response = $this->client->sendPaymentMethodRequest(true);

        $this->assertArrayHasKey('payment_source', $response);
    }
}
