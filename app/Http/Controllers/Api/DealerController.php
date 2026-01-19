<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DealerRegisterRequest;
use App\Models\City;
use App\Models\Dealer;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DealerController extends BaseApiController
{
    /**
     * Register a new dealer.
     *
     * @param DealerRegisterRequest $request
     * @return JsonResponse
     */
    public function register(DealerRegisterRequest $request): JsonResponse
    {
        $city = City::findOrFail($request->city_id);
        
        // Auto-assign zone from city
        $zoneId = $city->zone_id;

        // Handle image uploads
        $imagePaths = [];
        for ($i = 1; $i <= 4; $i++) {
            $imageKey = "image_{$i}";
            if ($request->hasFile($imageKey)) {
                $image = $request->file($imageKey);
                $imagePath = $image->store('dealers', 'public');
                $imagePaths[$imageKey] = $imagePath;
            }
        }

        $dealer = Dealer::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'gstin' => $request->gstin,
            'address' => $request->address,
            'zone_id' => $zoneId,
            'state_id' => $request->state_id,
            'district_id' => $request->district_id,
            'taluka_id' => $request->taluka_id,
            'city_id' => $request->city_id,
            'pincode' => $request->pincode,
            'password' => $request->password,
            'is_active' => true,
            'image_1' => $imagePaths['image_1'] ?? null,
            'image_2' => $imagePaths['image_2'] ?? null,
            'image_3' => $imagePaths['image_3'] ?? null,
            'image_4' => $imagePaths['image_4'] ?? null,
        ]);

        return $this->successResponse(
            $dealer->load(['state', 'district', 'taluka', 'city', 'zone']),
            'DEALER REGISTERED SUCCESSFULLY',
            201
        );
    }

    /**
     * Get authenticated dealer profile (dealers only).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        if (!$user instanceof Dealer) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'dealer' => $user->load(['state', 'city', 'zone']),
        ]);
    }

    /**
     * Get list of dealers (admin/sales_officer/accountant only).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->hasAnyRole(['admin', 'sales_officer', 'accountant'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $query = Dealer::query();

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by zone
        if ($request->has('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        // Filter by state
        if ($request->has('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $dealers = $query->with(['state', 'city', 'zone'])
            ->orderBy('name')
            ->paginate($request->get('per_page', 15));

        return response()->json($dealers);
    }

    /**
     * Get dealer details (admin/sales_officer/accountant only).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = request()->user('sanctum');

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->hasAnyRole(['admin', 'sales_officer', 'accountant'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $dealer = Dealer::with(['state', 'city', 'zone'])->findOrFail($id);

        return response()->json(['dealer' => $dealer]);
    }

    /**
     * Update dealer profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $dealer = $request->user('sanctum');

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:dealers,email,' . $dealer->id,
            'address' => 'sometimes|string',
            'city_id' => 'sometimes|exists:cities,id',
            'pincode' => 'sometimes|string|max:10',
        ]);

        if (isset($validated['city_id'])) {
            $city = City::findOrFail($validated['city_id']);
            $validated['zone_id'] = $city->zone_id;
            $validated['state_id'] = $city->state_id;
        }

        $dealer->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'dealer' => $dealer->fresh()->load(['state', 'city', 'zone']),
        ]);
    }
}

