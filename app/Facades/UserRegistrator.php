<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class UserRegistrator extends Facade{
    protected static function getFacadeAccessor() { return 'UserRegistrator'; }
}