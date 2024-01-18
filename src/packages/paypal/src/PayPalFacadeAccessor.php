<?php

namespace Srmklive\PayPal;

use Exception;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalFacadeAccessor
{
    /**
     * PayPal API provider object.
     *
     * @var
     */
    public static $provider;

    /**
     * Get specific PayPal API provider object to use.
     *
     * @return PayPalClient
     *@throws Exception
     *
     */
    public static function getProvider()
    {
        return self::$provider;
    }

    /**
     * Set PayPal API Client to use.
     *
     * @return PayPalClient
     *@throws Exception
     *
     */
    public static function setProvider()
    {
        // Set default provider. Defaults to ExpressCheckout
        self::$provider = new PayPalClient();

        return self::getProvider();
    }
}
