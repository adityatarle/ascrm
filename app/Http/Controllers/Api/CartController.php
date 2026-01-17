<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Dealer;
use App\Models\Product;
use App\Models\ProductStateRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get cart items for authenticated dealer.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $dealer = $request->user('sanctum');

        if (!$dealer instanceof Dealer) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItems = Cart::where('dealer_id', $dealer->id)
            ->with(['product.unit', 'product.stateRates'])
            ->get();

        // Calculate totals and rates for each item
        $items = $cartItems->map(function ($item) use ($dealer) {
            $rate = $this->getProductRate($item->product, $dealer->state_id);
            $subtotal = $item->quantity * $rate;

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'rate' => $rate,
                'subtotal' => $subtotal,
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'code' => $item->product->code,
                    'description' => $item->product->description,
                    'base_price' => $item->product->base_price,
                    'gst_rate' => $item->product->gst_rate ?? 18.00,
                    'unit' => $item->product->unit,
                    'is_active' => $item->product->is_active,
                ],
            ];
        });

        $subtotal = $items->sum('subtotal');
        $itemCount = $items->sum('quantity');

        return response()->json([
            'items' => $items,
            'summary' => [
                'item_count' => $items->count(),
                'total_quantity' => $itemCount,
                'subtotal' => $subtotal,
            ],
        ]);
    }

    /**
     * Add product to cart.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $dealer = $request->user('sanctum');

        if (!$dealer instanceof Dealer) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->is_active) {
            return response()->json([
                'message' => 'Product is not available',
            ], 422);
        }

        // Check if item already exists in cart
        $cartItem = Cart::where('dealer_id', $dealer->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = Cart::create([
                'dealer_id' => $dealer->id,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
            ]);
        }

        $cartItem->load(['product.unit']);

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart_item' => [
                'id' => $cartItem->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'product' => $cartItem->product,
            ],
        ], 201);
    }

    /**
     * Update cart item quantity.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $dealer = $request->user('sanctum');

        if (!$dealer instanceof Dealer) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem = Cart::where('dealer_id', $dealer->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->update(['quantity' => $validated['quantity']]);
        $cartItem->load(['product.unit']);

        return response()->json([
            'message' => 'Cart item updated successfully',
            'cart_item' => [
                'id' => $cartItem->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'product' => $cartItem->product,
            ],
        ]);
    }

    /**
     * Remove product from cart.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $dealer = $request->user('sanctum');

        if (!$dealer instanceof Dealer) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem = Cart::where('dealer_id', $dealer->id)
            ->findOrFail($id);

        $cartItem->delete();

        return response()->json([
            'message' => 'Product removed from cart successfully',
        ]);
    }

    /**
     * Clear all cart items.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function clear(Request $request): JsonResponse
    {
        $dealer = $request->user('sanctum');

        if (!$dealer instanceof Dealer) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Cart::where('dealer_id', $dealer->id)->delete();

        return response()->json([
            'message' => 'Cart cleared successfully',
        ]);
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
}
