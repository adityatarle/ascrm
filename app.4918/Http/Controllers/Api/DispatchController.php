<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateDispatchRequest;
use App\Models\Dispatch;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class DispatchController extends Controller
{
    /**
     * Create a dispatch for an order.
     *
     * @param CreateDispatchRequest $request
     * @return JsonResponse
     */
    public function create(CreateDispatchRequest $request): JsonResponse
    {
        $order = Order::findOrFail($request->order_id);

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
     * Get dispatches for an order.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $orderId
     * @return JsonResponse
     */
    public function index(\Illuminate\Http\Request $request, int $orderId): JsonResponse
    {
        $order = Order::findOrFail($orderId);

        $dispatches = Dispatch::where('order_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['dispatches' => $dispatches]);
    }

    /**
     * Update dispatch status.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(\Illuminate\Http\Request $request, int $id): JsonResponse
    {
        $dispatch = Dispatch::findOrFail($id);

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
            'dispatch' => $dispatch->fresh(),
        ]);
    }
}

