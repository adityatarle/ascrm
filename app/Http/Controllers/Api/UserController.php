<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get authenticated user profile.
     *
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'user' => $user->load('organization'),
            'organization' => $user->organization,
            'roles' => $user->getRoleNames(),
        ]);
    }

    /**
     * Update user profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'mobile' => 'sometimes|string|unique:users,mobile,' . $user->id,
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'state' => 'sometimes|string',
            'pincode' => 'sometimes|string|max:10',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()->load('organization'),
        ]);
    }
}
