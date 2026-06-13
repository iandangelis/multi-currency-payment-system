<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RejectPaymentRequestAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function execute(PaymentRequest $paymentRequest, User $approver): PaymentRequest
    {
        if ($paymentRequest->status !== PaymentStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => 'Only pending payment requests can be rejected.',
            ]);
        }

        $paymentRequest->update([
            'status' => PaymentStatus::Rejected,
            'approver_id' => $approver->id,
            'rejected_at' => now(),
        ]);

        return $paymentRequest->refresh();
    }
}
