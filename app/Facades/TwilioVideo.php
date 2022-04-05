<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class TwilioVideo extends Facade{
    protected static function getFacadeAccessor() { return 'TwilioVideo'; }
}