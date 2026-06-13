<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ApprovePaymentRequestAction
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
                'status' => 'Only pending payment requests can be approved.',
            ]);
        }

        $paymentRequest->update([
            'status' => PaymentStatus::Approved,
            'approver_id' => $approver->id,
            'approved_at' => now(),
        ]);

        return $paymentRequest->refresh();
    }
}
