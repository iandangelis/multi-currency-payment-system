<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Facades\ExchangeRate;
use App\Models\PaymentRequest;
use App\Models\User;

class CreatePaymentRequestAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function execute(User $requester, array $data): PaymentRequest
    {
        $currency = strtoupper($data['currency'] ?? $requester->currency);

        $exchangeRate = ExchangeRate::getRate('EUR', $currency);

        $convertedAmount = round($data['amount'] / $exchangeRate, 2);

        return PaymentRequest::create([
            'requester_id' => $requester->id,
            'status' => PaymentStatus::Pending,
            'amount' => $data['amount'],
            'currency' => $currency,
            'target_currency' => 'EUR',
            'exchange_rate' => $exchangeRate,
            'exchange_rate_source' => config('services.exchange_rate.source'),
            'exchange_rate_fetched_at' => now(),
            'converted_amount' => $convertedAmount,
            'expires_at' => now()->addHours(48),
        ]);
    }
}
