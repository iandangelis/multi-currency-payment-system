<?php

namespace App\Services;

use App\Contracts\ExchangeRateProviderInterface;
use App\Exceptions\ExchangeRateUnavailableException;
use Illuminate\Http\JsonResponse;
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

    public function getRate(string $fromCurrency, string $toCurrency): float | JsonResponse
    {
        $response = Http::timeout(10)->get(
            "https://api.exchangerate-api.com/v4/latest/{$fromCurrency}"
        );

        if (! $response->successful()) {
            return response()->json([
                "message" => "Unable to fetch exchange rate at this time. Please try again later.",
            ], 503);
        }

        $rate = $response->json("rates.{$toCurrency}");

        if (! $rate) {
            return response()->json([
                "message" => "Exchange rate not found for the specified currencies.",
            ], 422);
        }

        return (float) $rate;
    }
}
