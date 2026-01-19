<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Dealer;
use App\Models\Product;
use App\Models\ProductStateRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends BaseApiController
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
            return $this->unauthorizedResponse('UNAUTHORIZED');
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

        $data = [
            'items' => $items->toArray(),
            'summary' => [
                'item_count' => $items->count(),
                'total_quantity' => $itemCount,
                'subtotal' => $subtotal,
            ],
        ];

        return $this->successResponse($data, 'CART ITEMS RETRIEVED SUCCESSFULLY');
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
            return $this->unauthorizedResponse('UNAUTHORIZED');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->is_active) {
            return $this->errorResponse('PRODUCT NOT AVAILABLE', null, 422);
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

        $data = [
            'id' => $cartItem->id,
            'product_id' => $cartItem->product_id,
            'quantity' => $cartItem->quantity,
            'product' => $cartItem->product->toArray(),
        ];

        return $this->successResponse($data, 'PRODUCT ADDED TO CART SUCCESSFULLY', 201);
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
            return $this->unauthorizedResponse('UNAUTHORIZED');
        }

        $cartItem = Cart::where('dealer_id', $dealer->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->update(['quantity' => $validated['quantity']]);
        $cartItem->load(['product.unit']);

        $data = [
            'id' => $cartItem->id,
            'product_id' => $cartItem->product_id,
            'quantity' => $cartItem->quantity,
            'product' => $cartItem->product->toArray(),
        ];

        return $this->successResponse($data, 'CART ITEM UPDATED SUCCESSFULLY');
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
            return $this->unauthorizedResponse('UNAUTHORIZED');
        }

        $cartItem = Cart::where('dealer_id', $dealer->id)
            ->findOrFail($id);

        $cartItem->delete();

        return $this->successResponse(null, 'PRODUCT REMOVED FROM CART SUCCESSFULLY');
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
            return $this->unauthorizedResponse('UNAUTHORIZED');
        }

        Cart::where('dealer_id', $dealer->id)->delete();

        return $this->successResponse(null, 'CART CLEARED SUCCESSFULLY');
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
