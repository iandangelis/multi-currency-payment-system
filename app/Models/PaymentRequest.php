<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'approver_id',
        'status',
        'amount',
        'currency',
        'target_currency',
        'exchange_rate',
        'exchange_rate_source',
        'exchange_rate_fetched_at',
        'converted_amount',
        'approved_at',
        'rejected_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status'                    => PaymentStatus::class,
            'approved_at'               => 'datetime',
            'rejected_at'               => 'datetime',
            'expires_at'                => 'datetime',
            'exchange_rate_fetched_at'  => 'datetime',
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
