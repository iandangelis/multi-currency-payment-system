<?php

namespace Tests\Feature;

use App\Facades\ExchangeRate;
use App\Models\PaymentRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentRequestFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_payment_request(): void
    {
        $employeeRole = Role::factory()->employee()->create();

        $user = User::factory()
            ->for($employeeRole)
            ->create([
                'currency' => 'BRL',
            ]);

        Sanctum::actingAs($user);

        ExchangeRate::shouldReceive('getRate')
            ->once()
            ->with('EUR', 'BRL')
            ->andReturn(6.30);

        $response = $this->postJson('/api/payment-requests', [
            'amount' => 630,
            'currency' => 'BRL',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.currency', 'BRL')
            ->assertJsonPath('data.target_currency', 'EUR');

        $this->assertDatabaseHas('payment_requests', [
            'requester_id' => $user->id,
            'amount' => 630,
            'currency' => 'BRL',
            'target_currency' => 'EUR',
        ]);
    }

    public function test_authenticated_user_can_list_only_own_payment_requests(): void
    {
        $employeeRole = Role::factory()->employee()->create();

        $user = User::factory()
            ->for($employeeRole)
            ->create();

        $otherUser = User::factory()
            ->for($employeeRole)
            ->create();

        Sanctum::actingAs($user);

        $ownPaymentRequest = PaymentRequest::factory()
            ->for($user, 'requester')
            ->create();

        $otherPaymentRequest = PaymentRequest::factory()
            ->for($otherUser, 'requester')
            ->create();

        $response = $this->getJson('/api/payment-requests');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $ownPaymentRequest->id,
            ])
            ->assertJsonMissing([
                'id' => $otherPaymentRequest->id,
            ]);
    }

    public function test_authenticated_user_can_show_own_payment_request(): void
    {
        $employeeRole = Role::factory()->employee()->create();

        $user = User::factory()
            ->for($employeeRole)
            ->create();

        Sanctum::actingAs($user);

        $paymentRequest = PaymentRequest::factory()
            ->for($user, 'requester')
            ->create();

        $response = $this->getJson("/api/payment-requests/{$paymentRequest->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $paymentRequest->id);
    }

    public function test_authenticated_user_cannot_show_another_users_payment_request(): void
    {
        $employeeRole = Role::factory()->employee()->create();

        $user = User::factory()
            ->for($employeeRole)
            ->create();

        $otherUser = User::factory()
            ->for($employeeRole)
            ->create();

        Sanctum::actingAs($user);

        $otherPaymentRequest = PaymentRequest::factory()
            ->for($otherUser, 'requester')
            ->create();

        $response = $this->getJson("/api/payment-requests/{$otherPaymentRequest->id}");

        $response->assertForbidden();
    }
}
