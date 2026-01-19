<?php

namespace App\Http\Controllers\Api;

use App\Models\Crop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CropController extends BaseApiController
{
    /**
     * Get list of all active crops with their products.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Crop::where('is_active', true)
            ->with(['products' => function ($q) {
                $q->where('is_active', true)
                    ->with('unit')
                    ->orderBy('crop_product.sort_order')
                    ->orderBy('name');
            }])
            ->orderBy('sort_order')
            ->orderBy('name');

        // Search by crop name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $crops = $query->get();

        // Transform products to include calculated rates for dealers
        $user = auth('sanctum')->user();
        if ($user instanceof \App\Models\Dealer && $user->state_id) {
            $crops->transform(function ($crop) use ($user) {
                $crop->products->transform(function ($product) use ($user) {
                    $rate = $this->getProductRate($product, $user->state_id);
                    $product->calculated_rate = $rate;
                    return $product;
                });
                return $crop;
            });
        }

        return $this->successResponse($crops->toArray(), 'CROPS RETRIEVED SUCCESSFULLY');
    }

    /**
     * Get a specific crop with its products.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $crop = Crop::where('is_active', true)
            ->with(['products' => function ($q) {
                $q->where('is_active', true)
                    ->with('unit')
                    ->orderBy('crop_product.sort_order')
                    ->orderBy('name');
            }])
            ->findOrFail($id);

        // Add calculated rates for dealers
        $user = auth('sanctum')->user();
        if ($user instanceof \App\Models\Dealer && $user->state_id) {
            $crop->products->transform(function ($product) use ($user) {
                $rate = $this->getProductRate($product, $user->state_id);
                $product->calculated_rate = $rate;
                return $product;
            });
        }

        return $this->successResponse($crop->toArray(), 'CROP RETRIEVED SUCCESSFULLY');
    }

    /**
     * Get products for a specific crop.
     *
     * @param Request $request
     * @param int $cropId
     * @return JsonResponse
     */
    public function products(Request $request, int $cropId): JsonResponse
    {
        $crop = Crop::where('is_active', true)->findOrFail($cropId);

        $user = auth('sanctum')->user();
        $organizationId = null;
        $dealerStateId = null;

        if ($user instanceof \App\Models\User) {
            $organizationId = $user->organization_id;
        } elseif ($user instanceof \App\Models\Dealer) {
            $dealerStateId = $user->state_id;
        }

        $query = $crop->products()
            ->where('is_active', true)
            ->with('unit');

        // Filter by organization for users
        if ($organizationId) {
            $query->where('products.organization_id', $organizationId);
        }

        // Search
        if ($request->has('search')) {
            $query->where('products.name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('crop_product.sort_order')
            ->orderBy('products.name')
            ->paginate($request->get('per_page', 15));

        // Add calculated rate for dealers
        if ($user instanceof \App\Models\Dealer && $dealerStateId) {
            $products->getCollection()->transform(function ($product) use ($dealerStateId) {
                $rate = $this->getProductRate($product, $dealerStateId);
                $product->calculated_rate = $rate;
                return $product;
            });
        }

        return $this->successResponse($products->toArray(), 'PRODUCTS RETRIEVED SUCCESSFULLY');
    }

    /**
     * Get product rate for a state.
     *
     * @param \App\Models\Product $product
     * @param int|null $stateId
     * @return float
     */
    protected function getProductRate(\App\Models\Product $product, ?int $stateId): float
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
}
