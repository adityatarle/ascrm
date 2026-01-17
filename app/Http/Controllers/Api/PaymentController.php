<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get list of payments.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->hasAnyRole(['admin', 'accountant'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $query = Payment::whereHas('order', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        });

        // Filter by order_id
        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->with(['order.dealer'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($payments);
    }

    /**
     * Get payment details.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payment = Payment::with(['order.dealer', 'order.items.product'])
            ->findOrFail($id);

        if ($payment->order->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(['payment' => $payment]);
    }

    /**
     * Create a new payment (admin/accountant only).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->hasAnyRole(['admin', 'accountant'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,online',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if ($order->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => Payment::STATUS_COMPLETED,
        ]);

        return response()->json([
            'message' => 'Payment created successfully',
            'payment' => $payment->load(['order.dealer']),
        ], 201);
    }
}
