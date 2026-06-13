<?php

namespace App\Models;

use App\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentRequestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class
        ];
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
