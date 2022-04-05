<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class IncomesCalculator extends Facade{
    protected static function getFacadeAccessor() { return 'IncomesCalculator'; }
}