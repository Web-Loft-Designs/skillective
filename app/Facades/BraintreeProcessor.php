<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class BraintreeProcessor extends Facade{
    protected static function getFacadeAccessor() { return 'BraintreeProcessor'; }
}