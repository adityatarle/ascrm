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
     * Automatically detects user type based on credentials.
     * Returns user_type and roles for dashboard routing.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Try to find user first
        $user = User::where('mobile', $request->mobile)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            return $this->loginUser($request, $user);
        }
        
        // If not a user, try dealer
        $dealer = Dealer::where('mobile', $request->mobile)
            ->where('is_active', true)
            ->first();
        
        if ($dealer && Hash::check($request->password, $dealer->password)) {
            return $this->loginDealer($request, $dealer);
        }
        
        // If neither found or password incorrect
        throw ValidationException::withMessages([
            'mobile' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * Handle user (admin/accountant/sales/dispatch) login.
     */
    protected function loginUser(LoginRequest $request, User $user): JsonResponse
    {
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
     * Automatically determines organization from dealer's orders or uses first organization.
     */
    protected function loginDealer(LoginRequest $request, Dealer $dealer): JsonResponse
    {
        // Try to get organization from dealer's most recent order
        $recentOrder = $dealer->orders()
            ->with('organization')
            ->orderBy('created_at', 'desc')
            ->first();
        
        $organization = $recentOrder?->organization;

        // If no orders, get the first organization (or default organization)
        if (!$organization) {
            $organization = Organization::first();
            
            // If still no organization, throw error
            if (!$organization) {
                throw ValidationException::withMessages([
                    'mobile' => ['No organization found. Please contact administrator.'],
                ]);
            }
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

