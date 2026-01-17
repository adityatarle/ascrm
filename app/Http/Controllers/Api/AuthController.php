<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\Dealer;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Unified login for both Users and Dealers.
     * Returns user_type and roles for dashboard routing.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $userType = $request->input('user_type', 'dealer'); // Default to dealer for backward compatibility
        
        if ($userType === 'user') {
            return $this->loginUser($request);
        } else {
            return $this->loginDealer($request);
        }
    }

    /**
     * Handle user (admin/accountant/sales/dispatch) login.
     */
    protected function loginUser(LoginRequest $request): JsonResponse
    {
        $user = User::where('mobile', $request->mobile)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'mobile' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check organization if provided
        if ($request->organization_id && $user->organization_id != $request->organization_id) {
            throw ValidationException::withMessages([
                'organization_id' => ['The user does not belong to this organization.'],
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;
        $roles = $user->getRoleNames();

        return response()->json([
            'user_type' => 'user',
            'user' => $user->load('organization'),
            'organization' => $user->organization,
            'roles' => $roles,
            'permissions' => $this->getUserPermissions($user),
            'token' => $token,
        ]);
    }

    /**
     * Handle dealer login.
     */
    protected function loginDealer(LoginRequest $request): JsonResponse
    {
        if (!$request->organization_id) {
            throw ValidationException::withMessages([
                'organization_id' => ['Organization ID is required for dealer login.'],
            ]);
        }

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
            'user_type' => 'dealer',
            'dealer' => $dealer->load(['state', 'city', 'zone']),
            'organization' => $organization,
            'roles' => ['dealer'],
            'permissions' => ['view_orders', 'create_orders', 'view_profile', 'update_profile'],
            'token' => $token,
        ]);
    }

    /**
     * Get permissions based on user roles.
     */
    protected function getUserPermissions(User $user): array
    {
        $permissions = [];

        if ($user->hasRole('admin')) {
            $permissions = array_merge($permissions, [
                'manage_users', 'manage_products', 'manage_dealers', 'manage_orders',
                'manage_dispatches', 'manage_payments', 'view_reports', 'manage_settings',
            ]);
        }

        if ($user->hasRole('accountant')) {
            $permissions = array_merge($permissions, [
                'view_orders', 'view_dealers', 'manage_payments', 'view_reports',
            ]);
        }

        if ($user->hasRole('sales_officer')) {
            $permissions = array_merge($permissions, [
                'manage_orders', 'manage_dealers', 'view_products', 'view_dispatches',
            ]);
        }

        if ($user->hasRole('dispatch_officer')) {
            $permissions = array_merge($permissions, [
                'manage_dispatches', 'view_orders',
            ]);
        }

        return array_unique($permissions);
    }

    /**
     * Handle logout for both users and dealers.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if ($user) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get authenticated user/dealer profile.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Check if it's a User or Dealer
        if ($user instanceof User) {
            return response()->json([
                'user_type' => 'user',
                'user' => $user->load('organization'),
                'organization' => $user->organization,
                'roles' => $user->getRoleNames(),
                'permissions' => $this->getUserPermissions($user),
            ]);
        } else {
            return response()->json([
                'user_type' => 'dealer',
                'dealer' => $user->load(['state', 'city', 'zone']),
                'roles' => ['dealer'],
                'permissions' => ['view_orders', 'create_orders', 'view_profile', 'update_profile'],
            ]);
        }
    }
}

