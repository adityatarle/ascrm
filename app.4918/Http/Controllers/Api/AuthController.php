<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\Dealer;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle dealer login via API.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $organization = Organization::findOrFail($request->organization_id);

        $dealer = Dealer::where('mobile', $request->mobile)
            ->where('is_active', true)
            ->first();

        if (!$dealer || !Hash::check($request->password, $dealer->password)) {
            throw ValidationException::withMessages([
                'mobile' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $dealer->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'dealer' => $dealer->load(['state', 'city', 'zone']),
            'organization' => $organization,
            'token' => $token,
        ]);
    }

    /**
     * Handle dealer logout.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $dealer = auth('sanctum')->user();

        if ($dealer) {
            $dealer->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }
}

