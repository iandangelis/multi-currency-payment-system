<?php

namespace App\Contracts;

use Illuminate\Http\JsonResponse;

interface ExchangeRateProviderInterface
{
    public function getRate(string $fromCurrency, string $toCurrency): float | JsonResponse;
}
