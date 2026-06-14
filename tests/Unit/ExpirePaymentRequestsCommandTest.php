<?php

namespace Tests\Unit;

use App\Enums\PaymentStatus;
use App\Models\PaymentRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpirePaymentRequestsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_expires_pending_payment_requests_past_expiration_date(): void
    {
        $requester = User::factory()->for(Role::factory()->employee()->create())->create();

        $expiredRequest = PaymentRequest::factory()->for($requester, 'requester')->create([
            'status' => PaymentStatus::Pending,
            'expires_at' => now()->subHour(),
        ]);

        $activeRequest = PaymentRequest::factory()->for($requester, 'requester')->create([
            'status' => PaymentStatus::Pending,
            'expires_at' => now()->addHour(),
        ]);

        $approvedRequest = PaymentRequest::factory()->for($requester, 'requester')->approved()->create([
            'expires_at' => now()->subHour(),
        ]);

        $this->artisan('payment-requests:expire')
            ->assertExitCode(0);

        $this->assertEquals(
            PaymentStatus::Expired,
            $expiredRequest->fresh()->status
        );

        $this->assertEquals(
            PaymentStatus::Pending,
            $activeRequest->fresh()->status
        );

        $this->assertEquals(
            PaymentStatus::Approved,
            $approvedRequest->fresh()->status
        );
    }
}
