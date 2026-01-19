<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends BaseApiController
{
    /**
     * Get list of active banners.
     * Returns only banners that are currently active and within their date range.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Banner::active()
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc');

        // Optional: Get all banners including inactive (for admin)
        if ($request->has('include_inactive') && $request->boolean('include_inactive')) {
            $user = auth('sanctum')->user();
            if ($user instanceof \App\Models\User && $user->hasRole('admin')) {
                $query = Banner::query()
                    ->orderBy('sort_order')
                    ->orderBy('created_at', 'desc');
            }
        }

        $banners = $query->get();

        // Transform to include full image URLs
        $banners->transform(function ($banner) {
            $banner->image_url = $banner->image ? asset('storage/' . $banner->image) : null;
            return $banner;
        });

        return $this->successResponse($banners->toArray(), 'BANNERS RETRIEVED SUCCESSFULLY');
    }

    /**
     * Get a specific banner.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $banner = Banner::findOrFail($id);
        
        // Add full image URL
        $banner->image_url = $banner->image ? asset('storage/' . $banner->image) : null;

        return $this->successResponse($banner->toArray(), 'BANNER RETRIEVED SUCCESSFULLY');
    }
}
