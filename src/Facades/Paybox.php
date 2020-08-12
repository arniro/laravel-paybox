<?php

namespace Arniro\Paybox\Facades;

use Illuminate\Support\Facades\Facade;

class Paybox extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Arniro\Paybox\Paybox::class;
    }
}
