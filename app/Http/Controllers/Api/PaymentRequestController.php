<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreatePaymentRequestAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequestRequest;
use App\Models\PaymentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = PaymentRequest::query()->with(['requester','approver'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->lower());
        }

        return response()->json([
            'data' => $query->paginate(10)
        ]);
    }

    public function show(PaymentRequest $paymentRequest): JsonResponse
    {
        return response()->json([
            'data' => $paymentRequest->load(['requester', 'approver'])
        ]);
    }

    public function store(StorePaymentRequestRequest $request, CreatePaymentRequestAction $action): JsonResponse
    {
        $paymentRequest = $action->execute(
            requester: $request->user(),
            data: $request->validated()
        );

        return response()->json([
            'message' => 'Payment request created successfully!',
            'data' => $paymentRequest->load('requester')
        ], 201);
    }
}
