<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateOrderRequest;
use App\Models\Dealer;
use App\Models\DiscountSlab;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStateRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Create an order from cart items.
     *
     * @param CreateOrderRequest $request
     * @return JsonResponse
     */
    public function create(CreateOrderRequest $request): JsonResponse
    {
        $dealer = $request->user('sanctum');
        
        if (!$dealer instanceof Dealer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // For now, we'll use the first organization. In production, this should come from dealer's organization or request
        $organization = \App\Models\Organization::first();
        
        if (!$organization) {
            return response()->json(['message' => 'No organization found'], 404);
        }

        return DB::transaction(function () use ($request, $dealer, $organization) {
            $cartItems = $request->cart_items;
            $subtotal = 0;
            $orderItems = [];

            // Process each cart item
            foreach ($cartItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];

                // Get state-specific rate or fallback to base price
                $rate = $this->getProductRate($product, $dealer->state_id);

                $itemSubtotal = $quantity * $rate;
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(uniqid());

            // Create order (Observer will calculate GST and totals)
            $order = Order::create([
                'organization_id' => $organization->id,
                'dealer_id' => $dealer->id,
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'status' => Order::STATUS_PENDING,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            // Refresh to get calculated totals
            $order->refresh();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load(['items.product', 'dealer', 'organization']),
            ], 201);
        });
    }

    /**
     * Get product rate for a state.
     *
     * @param Product $product
     * @param int|null $stateId
     * @return float
     */
    protected function getProductRate(Product $product, ?int $stateId): float
    {
        if ($stateId) {
            $stateRate = ProductStateRate::where('product_id', $product->id)
                ->where('state_id', $stateId)
                ->first();

            if ($stateRate) {
                return $stateRate->rate;
            }
        }

        return $product->base_price;
    }

    /**
     * Get dealer's orders.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $dealer = $request->user('sanctum');

        $orders = Order::where('dealer_id', $dealer->id)
            ->with(['items.product', 'organization'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($orders);
    }

    /**
     * Get order details.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(\Illuminate\Http\Request $request, int $id): JsonResponse
    {
        $dealer = $request->user('sanctum');

        $order = Order::where('dealer_id', $dealer->id)
            ->with(['items.product', 'organization', 'dispatches'])
            ->findOrFail($id);

        return response()->json(['order' => $order]);
    }
}

