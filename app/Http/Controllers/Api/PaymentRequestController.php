<?php

namespace App\Http\Controllers\Api;

use App\Actions\ApprovePaymentRequestAction;
use App\Actions\CreatePaymentRequestAction;
use App\Actions\RejectPaymentRequestAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequestRequest;
use App\Models\PaymentRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{

    use AuthorizesRequests;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', PaymentRequest::class);

        $query = PaymentRequest::query()->with(['requester', 'approver'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->lower());
        }

        if (! $request->user()->isFinance()) {
            $query->where('requester_id', $request->user()->id);
        }

        return response()->json([
            'data' => $query->paginate(10)
        ]);
    }

    public function show(PaymentRequest $paymentRequest): JsonResponse
    {
        $this->authorize('view', $paymentRequest);

        return response()->json([
            'data' => $paymentRequest->load(['requester', 'approver'])
        ]);
    }

    public function store(StorePaymentRequestRequest $request, CreatePaymentRequestAction $action): JsonResponse
    {
        $this->authorize('create', PaymentRequest::class);

        $paymentRequest = $action->execute(
            requester: $request->user(),
            data: $request->validated()
        );

        return response()->json([
            'message' => 'Payment request created successfully!',
            'data' => $paymentRequest->load('requester')
        ], 201);
    }

    public function approve(PaymentRequest $paymentRequest, ApprovePaymentRequestAction $action): JsonResponse
    {
        $this->authorize('approve', $paymentRequest);

        $paymentRequest = $action->execute(
            $paymentRequest,
            request()->user()
        );

        return response()->json([
            'message' => 'Payment request has been approved successfully!',
            'data' => $paymentRequest->load(['requester', 'approver'])
        ]);
    }

    public function reject(PaymentRequest $paymentRequest, RejectPaymentRequestAction $action): JsonResponse
    {
        $this->authorize('reject', $paymentRequest);

        $paymentRequest = $action->execute(
            $paymentRequest,
            request()->user()
        );

        return response()->json([
            'message' => 'Payment request rejected successfully!',
            'data' => $paymentRequest->load(['requester', 'approver'])
        ]);
    }
}
