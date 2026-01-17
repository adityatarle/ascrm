<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get list of products with details.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $organizationId = null;
        $dealerStateId = null;

        if ($user instanceof User) {
            $organizationId = $user->organization_id;
        } elseif ($user instanceof \App\Models\Dealer) {
            $dealerStateId = $user->state_id;
        }

        $query = Product::query();

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        // Only show active products for dealers
        if ($user instanceof \App\Models\Dealer) {
            $query->where('is_active', true);
        }

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category if category field exists (future enhancement)
        if ($request->has('category')) {
            // This can be extended when category field is added
        }

        $products = $query->with(['unit', 'stateRates.state'])
            ->orderBy('name')
            ->paginate($request->get('per_page', 15));

        // Add calculated rate for dealers
        if ($user instanceof \App\Models\Dealer && $dealerStateId) {
            $products->getCollection()->transform(function ($product) use ($dealerStateId) {
                $rate = $this->getProductRate($product, $dealerStateId);
                $product->calculated_rate = $rate;
                return $product;
            });
        }

        return response()->json($products);
    }

    /**
     * Get product categories (simple implementation - can be extended).
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        // Simple categories - can be extended with a Category model later
        $categories = [
            ['id' => 1, 'name' => 'Pesticides', 'slug' => 'pesticides'],
            ['id' => 2, 'name' => 'Fertilizers', 'slug' => 'fertilizers'],
            ['id' => 3, 'name' => 'Seeds', 'slug' => 'seeds'],
            ['id' => 4, 'name' => 'Tools & Equipment', 'slug' => 'tools-equipment'],
            ['id' => 5, 'name' => 'Other', 'slug' => 'other'],
        ];

        return response()->json([
            'categories' => $categories,
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
            $stateRate = \App\Models\ProductStateRate::where('product_id', $product->id)
                ->where('state_id', $stateId)
                ->first();

            if ($stateRate) {
                return $stateRate->rate;
            }
        }

        return $product->base_price;
    }

    /**
     * Get product details with full information.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = auth('sanctum')->user();
        $product = Product::with(['unit', 'stateRates.state', 'organization'])->findOrFail($id);

        // Add calculated rate for dealers
        if ($user instanceof \App\Models\Dealer && $user->state_id) {
            $rate = $this->getProductRate($product, $user->state_id);
            $product->calculated_rate = $rate;
        }

        return response()->json(['product' => $product]);
    }

    /**
     * Create a new product (admin/sales_officer only).
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

        if (!$user->hasAnyRole(['admin', 'sales_officer'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:products,code',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'unit_id' => 'required|exists:units,id',
            'gst_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['organization_id'] = $user->organization_id;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('unit'),
        ], 201);
    }

    /**
     * Update product (admin/sales_officer only).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product = Product::findOrFail($id);

        if ($product->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if (!$user->hasAnyRole(['admin', 'sales_officer'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:products,code,' . $id,
            'description' => 'nullable|string',
            'base_price' => 'sometimes|numeric|min:0',
            'unit_id' => 'sometimes|exists:units,id',
            'gst_rate' => 'sometimes|numeric|min:0|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->fresh()->load('unit'),
        ]);
    }

    /**
     * Delete product (admin only).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user instanceof User || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product = Product::findOrFail($id);

        if ($product->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
