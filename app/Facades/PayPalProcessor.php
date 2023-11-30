<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class PayPalProcessor extends Facade {
    protected static function getFacadeAccessor(): string
    {
        return 'PayPalProcessor';
    }
}
