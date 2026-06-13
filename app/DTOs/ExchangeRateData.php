<?php

namespace App\DTOs;

class ExchangeRateData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $fromCurrency,
        public readonly string $toCurrency,
        public readonly float $rate
    )
    {
        //
    }
}
