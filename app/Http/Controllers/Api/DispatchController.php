<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateDispatchRequest;
use App\Models\Dealer;
use App\Models\Dispatch;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DispatchController extends BaseApiController
{
    /**
     * Create a dispatch for an order (admin/dispatch_officer only).
     *
     * @param CreateDispatchRequest $request
     * @return JsonResponse
     */
    public function create(CreateDispatchRequest $request): JsonResponse
    {
        $user = $request->user('sanctum');

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->hasAnyRole(['admin', 'dispatch_officer'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $order = Order::findOrFail($request->order_id);

        if ($order->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Check if order can be dispatched
        if ($order->status === Order::STATUS_CANCELLED) {
            return response()->json([
                'message' => 'Cannot dispatch a cancelled order',
            ], 422);
        }

        $dispatchNumber = 'DISP-' . strtoupper(Str::random(8));

        $dispatch = Dispatch::create([
            'order_id' => $order->id,
            'dispatch_number' => $dispatchNumber,
            'lr_number' => $request->lr_number,
            'transporter_name' => $request->transporter_name,
            'vehicle_number' => $request->vehicle_number,
            'dispatched_at' => $request->dispatched_at ?? now(),
            'status' => Dispatch::STATUS_DISPATCHED,
        ]);

        // Update order status
        if ($order->status === Order::STATUS_PENDING || $order->status === Order::STATUS_CONFIRMED) {
            $order->update(['status' => Order::STATUS_DISPATCHED]);
        }

        return response()->json([
            'message' => 'Dispatch created successfully',
            'dispatch' => $dispatch->load(['order.items.product']),
        ], 201);
    }

    /**
     * Get dispatches for an order (supports both Users and Dealers).
     *
     * @param Request $request
     * @param int $orderId
     * @return JsonResponse
     */
    public function index(Request $request, int $orderId): JsonResponse
    {
        $user = $request->user('sanctum');
        $order = Order::findOrFail($orderId);

        // Check access
        if ($user instanceof Dealer) {
            if ($order->dealer_id !== $user->id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        } elseif ($user instanceof User) {
            if (!$user->hasAnyRole(['admin', 'dispatch_officer', 'sales_officer'])) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            if ($order->organization_id !== $user->organization_id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $dispatches = Dispatch::where('order_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['dispatches' => $dispatches]);
    }

    /**
     * Update dispatch status (admin/dispatch_officer only).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user('sanctum');

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->hasAnyRole(['admin', 'dispatch_officer'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $dispatch = Dispatch::findOrFail($id);

        if ($dispatch->order->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,dispatched,in_transit,delivered',
            'lr_number' => 'sometimes|string|max:255',
            'transporter_name' => 'sometimes|string|max:255',
            'vehicle_number' => 'sometimes|string|max:255',
        ]);

        $dispatch->update($validated);

        // Update order status if dispatch is delivered
        if ($dispatch->status === Dispatch::STATUS_DELIVERED) {
            $dispatch->order->update(['status' => Order::STATUS_DELIVERED]);
        }

        return response()->json([
            'message' => 'Dispatch updated successfully',
            'dispatch' => $dispatch->fresh()->load(['order.items.product']),
        ]);
    }
}

