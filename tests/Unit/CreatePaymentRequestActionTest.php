<?php

namespace Tests\Unit;

use App\Actions\CreatePaymentRequestAction;
use App\Enums\PaymentStatus;
use App\Facades\ExchangeRate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePaymentRequestActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_payment_request_using_eur_to_local_currency_rate(): void
    {
        $role = Role::factory()->create([
            'name' => 'employee',
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'country' => 'Brazil',
            'currency' => 'BRL',
        ]);

        ExchangeRate::shouldReceive('getRate')
            ->once()
            ->with('EUR', 'BRL')
            ->andReturn(6.30);

        $paymentRequest = app(CreatePaymentRequestAction::class)->execute(
            requester: $user,
            data: [
                'amount' => 630,
                'currency' => 'BRL',
            ]
        );

        $this->assertEquals($user->id, $paymentRequest->requester_id);
        $this->assertEquals(PaymentStatus::Pending, $paymentRequest->status);
        $this->assertEquals(630.00, (float) $paymentRequest->amount);
        $this->assertEquals('BRL', $paymentRequest->currency);
        $this->assertEquals('EUR', $paymentRequest->target_currency);
        $this->assertEquals(6.30, (float) $paymentRequest->exchange_rate);
        $this->assertEquals(100.00, (float) $paymentRequest->converted_amount);
        $this->assertNotNull($paymentRequest->exchange_rate_source);
        $this->assertNotNull($paymentRequest->exchange_rate_fetched_at);
        $this->assertNotNull($paymentRequest->expires_at);
    }
}
