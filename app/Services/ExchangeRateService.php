<?php

namespace App\Services;

use App\Contracts\ExchangeRateProviderInterface;
use App\Exceptions\ExchangeRateUnavailableException;
use Illuminate\Support\Facades\Http;

class ExchangeRateService implements ExchangeRateProviderInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getRate(string $fromCurrency, string $toCurrency): float
    {
        $response = Http::timeout(10)->get(
            "https://api.exchangerate-api.com/v4/latest/{$fromCurrency}"
        );

        if (! $response->successful()) {
            throw new ExchangeRateUnavailableException(
                'Unable to fetch exchange rate.'
            );
        }

        $rate = $response->json("rates.{$toCurrency}");

        if (! $rate) {
            throw new ExchangeRateUnavailableException(
                "Exchange rate not found for {$fromCurrency} to {$toCurrency}."
            );
        }

        return (float) $rate;
    }
}
