<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DealerRegisterRequest;
use App\Models\City;
use App\Models\Dealer;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealerController extends Controller
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

        $dealer = Dealer::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'gstin' => $request->gstin,
            'address' => $request->address,
            'zone_id' => $zoneId,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'pincode' => $request->pincode,
            'password' => $request->password,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Dealer registered successfully',
            'dealer' => $dealer->load(['state', 'city', 'zone']),
        ], 201);
    }

    /**
     * Get authenticated dealer profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        $dealer = $request->user('sanctum');

        return response()->json([
            'dealer' => $dealer->load(['state', 'city', 'zone']),
        ]);
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

