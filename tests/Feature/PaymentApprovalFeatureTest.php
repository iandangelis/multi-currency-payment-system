<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\PaymentRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentApprovalFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_user_can_approve_payment_request(): void
    {
        $employeeRole = Role::factory()->employee()->create();
        $financeRole = Role::factory()->finance()->create();

        $employee = User::factory()->for($employeeRole)->create();
        $finance = User::factory()->for($financeRole)->create();

        Sanctum::actingAs($finance);

        $paymentRequest = PaymentRequest::factory()
            ->for($employee, 'requester')
            ->create(['status' => PaymentStatus::Pending]);

        $response = $this->patchJson("/api/payment-requests/{$paymentRequest->id}/approve");

        $response->assertOk();

        $this->assertEquals(PaymentStatus::Approved, $paymentRequest->fresh()->status);
        $this->assertEquals($finance->id, $paymentRequest->fresh()->approver_id);
        $this->assertNotNull($paymentRequest->fresh()->approved_at);
    }

    public function test_finance_user_can_reject_payment_request(): void
    {
        $employeeRole = Role::factory()->employee()->create();
        $financeRole = Role::factory()->finance()->create();

        $employee = User::factory()->for($employeeRole)->create();
        $finance = User::factory()->for($financeRole)->create();

        Sanctum::actingAs($finance);

        $paymentRequest = PaymentRequest::factory()
            ->for($employee, 'requester')
            ->create(['status' => PaymentStatus::Pending]);

        $response = $this->patchJson("/api/payment-requests/{$paymentRequest->id}/reject");

        $response->assertOk();

        $this->assertEquals(PaymentStatus::Rejected, $paymentRequest->fresh()->status);
        $this->assertEquals($finance->id, $paymentRequest->fresh()->approver_id);
        $this->assertNotNull($paymentRequest->fresh()->rejected_at);
    }

    public function test_employee_cannot_approve_payment_request(): void
    {
        $employeeRole = Role::factory()->employee()->create();

        $requester = User::factory()->for($employeeRole)->create();
        $employee = User::factory()->for($employeeRole)->create();

        Sanctum::actingAs($employee);

        $paymentRequest = PaymentRequest::factory()
            ->for($requester, 'requester')
            ->create(['status' => PaymentStatus::Pending]);

        $response = $this->patchJson("/api/payment-requests/{$paymentRequest->id}/approve");

        $response->assertForbidden();

        $this->assertEquals(PaymentStatus::Pending, $paymentRequest->fresh()->status);
        $this->assertNull($paymentRequest->fresh()->approver_id);
    }

    public function test_employee_cannot_reject_payment_request(): void
    {
        $employeeRole = Role::factory()->employee()->create();

        $requester = User::factory()->for($employeeRole)->create();
        $employee = User::factory()->for($employeeRole)->create();

        Sanctum::actingAs($employee);

        $paymentRequest = PaymentRequest::factory()
            ->for($requester, 'requester')
            ->create(['status' => PaymentStatus::Pending]);

        $response = $this->patchJson("/api/payment-requests/{$paymentRequest->id}/reject");

        $response->assertForbidden();

        $this->assertEquals(PaymentStatus::Pending, $paymentRequest->fresh()->status);
        $this->assertNull($paymentRequest->fresh()->approver_id);
    }

    public function test_finance_can_show_any_payment_request(): void
    {
        $employeeRole = Role::factory()->employee()->create();
        $financeRole = Role::factory()->finance()->create();

        $employee = User::factory()->for($employeeRole)->create();
        $finance = User::factory()->for($financeRole)->create();

        Sanctum::actingAs($finance);

        $paymentRequest = PaymentRequest::factory()
            ->for($employee, 'requester')
            ->create();

        $response = $this->getJson("/api/payment-requests/{$paymentRequest->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $paymentRequest->id);
    }

    public function test_finance_can_list_all_payment_requests(): void
    {
        $employeeRole = Role::factory()->employee()->create();
        $financeRole = Role::factory()->finance()->create();

        $employeeOne = User::factory()->for($employeeRole)->create();
        $employeeTwo = User::factory()->for($employeeRole)->create();
        $finance = User::factory()->for($financeRole)->create();

        Sanctum::actingAs($finance);

        $firstPaymentRequest = PaymentRequest::factory()
            ->for($employeeOne, 'requester')
            ->create();

        $secondPaymentRequest = PaymentRequest::factory()
            ->for($employeeTwo, 'requester')
            ->create();

        $response = $this->getJson('/api/payment-requests');

        $response
            ->assertOk()
            ->assertJsonFragment(['id' => $firstPaymentRequest->id])
            ->assertJsonFragment(['id' => $secondPaymentRequest->id]);
    }
}
