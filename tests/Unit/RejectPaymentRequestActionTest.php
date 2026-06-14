<?php

namespace Tests\Unit;

use App\Actions\RejectPaymentRequestAction;
use App\Enums\PaymentStatus;
use App\Models\PaymentRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RejectPaymentRequestActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_user_can_reject_pending_payment_request(): void
    {
        $employeeRole = Role::factory()->employee()->create();
        $financeRole = Role::factory()->finance()->create();

        $employee = User::factory()->for($employeeRole)->create();
        $finance = User::factory()->for($financeRole)->create();

        $paymentRequest = PaymentRequest::factory()
            ->for($employee, 'requester')
            ->create([
                'status' => PaymentStatus::Pending,
            ]);

        $result = app(RejectPaymentRequestAction::class)->execute(
            $paymentRequest,
            $finance
        );

        $this->assertEquals(PaymentStatus::Rejected, $result->status);
        $this->assertEquals($finance->id, $result->approver_id);
        $this->assertNotNull($result->rejected_at);
    }

    public function test_non_pending_payment_request_cannot_be_rejected(): void
    {
        $this->expectException(ValidationException::class);

        $employeeRole = Role::factory()->employee()->create();
        $financeRole = Role::factory()->finance()->create();

        $employee = User::factory()->for($employeeRole)->create();
        $finance = User::factory()->for($financeRole)->create();

        $paymentRequest = PaymentRequest::factory()
            ->for($employee, 'requester')
            ->approved()
            ->create();

        app(RejectPaymentRequestAction::class)->execute(
            $paymentRequest,
            $finance
        );
    }
}
