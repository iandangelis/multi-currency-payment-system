<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\PaymentRequest;
use Illuminate\Console\Command;

class ExpirePaymentRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment-requests:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expires all payment requests which expires_at datetime is lower than or equal than now';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        PaymentRequest::query()
            ->where('status', PaymentStatus::Pending)
            ->where('expires_at', '<=', now())
            ->update([
                'status' => PaymentStatus::Expired
            ]);
    }
}
