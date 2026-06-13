<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ExchangeRate extends Facade
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function getFacadeAccessor(): string
    {
        return \App\Contracts\ExchangeRateProviderInterface::class;
    }
}
