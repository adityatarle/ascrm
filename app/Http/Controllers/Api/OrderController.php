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
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Create an order from cart items or cart.
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
            $cartItems = [];
            
            // Check if order should be created from cart
            if ($request->has('use_cart') && $request->use_cart === true) {
                // Get items from dealer's cart
                $cart = \App\Models\Cart::where('dealer_id', $dealer->id)->with('product')->get();
                
                if ($cart->isEmpty()) {
                    return response()->json([
                        'message' => 'Cart is empty',
                    ], 422);
                }

                foreach ($cart as $cartItem) {
                    $cartItems[] = [
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                    ];
                }
            } else {
                // Use provided cart_items
                $cartItems = $request->cart_items;
            }

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

            // Clear cart if order was created from cart
            if ($request->has('use_cart') && $request->use_cart === true) {
                \App\Models\Cart::where('dealer_id', $dealer->id)->delete();
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
     * Get orders (supports both Users and Dealers).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');
        $query = Order::query();

        if ($user instanceof Dealer) {
            // Dealers can only see their own orders
            $query->where('dealer_id', $user->id);
        } elseif ($user instanceof User) {
            // Users can see orders from their organization
            if (!$user->hasAnyRole(['admin', 'sales_officer', 'accountant'])) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            $query->where('organization_id', $user->organization_id);

            // Filter by dealer_id if provided
            if ($request->has('dealer_id')) {
                $query->where('dealer_id', $request->dealer_id);
            }

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $orders = $query->with(['items.product', 'dealer', 'organization'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($orders);
    }

    /**
     * Get order details (supports both Users and Dealers).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user('sanctum');
        $order = Order::with(['items.product', 'dealer', 'organization', 'dispatches', 'payments'])
            ->findOrFail($id);

        if ($user instanceof Dealer) {
            // Dealers can only see their own orders
            if ($order->dealer_id !== $user->id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        } elseif ($user instanceof User) {
            // Users can see orders from their organization
            if (!$user->hasAnyRole(['admin', 'sales_officer', 'accountant'])) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            if ($order->organization_id !== $user->organization_id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json(['order' => $order]);
    }

    /**
     * Update order (admin/sales_officer only).
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

        if (!$user->hasAnyRole(['admin', 'sales_officer'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $order = Order::findOrFail($id);

        if ($order->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return response()->json([
                'message' => 'Only pending orders can be updated',
            ], 422);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,cancelled',
        ]);

        $order->update($validated);

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order->fresh()->load(['items.product', 'dealer', 'organization']),
        ]);
    }

    /**
     * Delete order (admin only).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = request()->user('sanctum');

        if (!$user instanceof User || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $order = Order::findOrFail($id);

        if ($order->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return response()->json([
                'message' => 'Only pending orders can be deleted',
            ], 422);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}

