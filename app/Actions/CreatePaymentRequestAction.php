<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
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
        $originalCurrency = strtoupper($data['original_currency'] ?? $requester->currency_code);
        $targetCurrency = strtoupper($data['target_currency'] ?? 'EUR');

        // Temporário. Depois trocamos pela Facade/Service de câmbio.
        $exchangeRate = 1.00;

        $convertedAmount = round(
            $data['amount'] * $exchangeRate,
            2
        );

        return PaymentRequest::create([
            'requester_id' => $requester->id,
            'status' => PaymentStatus::Pending,
            'original_amount' => $data['amount'],
            'original_currency' => $originalCurrency,
            'target_currency' => $targetCurrency,
            'exchange_rate' => $exchangeRate,
            'converted_amount' => $convertedAmount,
            'expires_at' => now()->addHours(48),
        ]);
    }
}
