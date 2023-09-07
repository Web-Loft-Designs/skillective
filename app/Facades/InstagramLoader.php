<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class InstagramLoader extends Facade{
    protected static function getFacadeAccessor(): string
    { return 'InstagramLoader'; }
}
