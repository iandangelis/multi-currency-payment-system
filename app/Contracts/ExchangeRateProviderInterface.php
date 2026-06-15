<?php

namespace App\Contracts;

interface ExchangeRateProviderInterface
{
    public function getRate(string $fromCurrency, string $toCurrency): float;
}
