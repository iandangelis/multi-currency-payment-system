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
        $originalCurrency = strtoupper($data['currency'] ?? $requester->currency);
        $targetCurrency = strtoupper($data['target_currency'] ?? 'EUR');

        $exchangeRate = ExchangeRate::getRate($originalCurrency, $targetCurrency);

        $convertedAmount = round(
            $data['amount'] * $exchangeRate,
            2
        );

        return PaymentRequest::create([
            'requester_id' => $requester->id,
            'status' => PaymentStatus::Pending,
            'amount' => $data['amount'],
            'currency' => $originalCurrency,
            'target_currency' => $targetCurrency,
            'exchange_rate' => $exchangeRate,
            'converted_amount' => $convertedAmount,
            'expires_at' => now()->addHours(48),
        ]);
    }
}
