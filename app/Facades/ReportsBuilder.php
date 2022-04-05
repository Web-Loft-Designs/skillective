<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class ReportsBuilder extends Facade{
    protected static function getFacadeAccessor() { return 'ReportsBuilder'; }
}