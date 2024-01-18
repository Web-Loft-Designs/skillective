<?php

namespace Srmklive\PayPal\Facades;

/*
 * Class Facade
 * @package Sliver\PayPal\Facades
 * @see Sliver\PayPal\ExpressCheckout
 */

use Illuminate\Support\Facades\Facade;

class PayPal extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'PayPal\PayPalFacadeAccessor';
    }
}
