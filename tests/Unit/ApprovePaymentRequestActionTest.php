<?php

namespace Tests\Unit;

use App\Actions\ApprovePaymentRequestAction;
use App\Enums\PaymentStatus;
use App\Models\PaymentRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ApprovePaymentRequestActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_user_can_approve_pending_payment_request(): void
    {
        $employeeRole = Role::factory()->create(['name' => 'employee']);
        $financeRole = Role::factory()->create(['name' => 'finance']);

        $employee = User::factory()->for($employeeRole)->create();
        $finance = User::factory()->for($financeRole)->create();

        $paymentRequest = PaymentRequest::factory()->create([
            'requester_id' => $employee->id,
            'status' => PaymentStatus::Pending,
        ]);

        $result = app(ApprovePaymentRequestAction::class)->execute(
            $paymentRequest,
            $finance
        );

        $this->assertEquals(PaymentStatus::Approved, $result->status);
        $this->assertEquals($finance->id, $result->approver_id);
        $this->assertNotNull($result->approved_at);
    }

    public function test_non_pending_payment_request_cannot_be_approved(): void
    {
        $this->expectException(ValidationException::class);

        $financeRole = Role::factory()->create(['name' => 'finance']);
        $finance = User::factory()->for($financeRole)->create();

        $employeeRole = Role::factory()->create(['name' => 'employee']);
        $employee = User::factory()->for($employeeRole)->create();

        $paymentRequest = PaymentRequest::factory()->for($employee, 'requester')->create([
            'status' => PaymentStatus::Approved,
        ]);

        app(ApprovePaymentRequestAction::class)->execute(
            $paymentRequest,
            $finance
        );
    }
}
