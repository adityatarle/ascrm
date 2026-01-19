<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\LoginRequest;
use App\Models\Dealer;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
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
        return $this->errorResponse('INVALID CREDENTIALS', ['mobile' => ['The provided credentials are incorrect.']], 401);
    }

    /**
     * Handle user (admin/accountant/sales/dispatch) login.
     */
    protected function loginUser(LoginRequest $request, User $user): JsonResponse
    {
        $token = $user->createToken('mobile-app')->plainTextToken;
        $roles = $user->getRoleNames();

        // Get user data without relationships
        $userData = $user->makeHidden(['organization', 'organization_id', 'password'])->toArray();
        
        // Remove roles from user data as we'll include it separately
        unset($userData['roles']);

        $data = [
            'user_type' => 'user',
            'user' => $userData,
            'roles' => $roles,
            'token' => $token,
        ];

        return $this->successResponse($data, 'LOGIN SUCCESSFUL');
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
            
            // If still no organization, return error
            if (!$organization) {
                return $this->errorResponse('NO ORGANIZATION FOUND', ['mobile' => ['No organization found. Please contact administrator.']], 400);
            }
        }

        $token = $dealer->createToken('mobile-app')->plainTextToken;

        // Get dealer data without relationships
        $dealerData = $dealer->makeHidden(['password'])->toArray();

        $data = [
            'user_type' => 'dealer',
            'dealer' => $dealerData,
            'roles' => ['dealer'],
            'token' => $token,
        ];

        return $this->successResponse($data, 'LOGIN SUCCESSFUL');
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

        return $this->successResponse(null, 'LOGGED OUT SUCCESSFULLY');
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
            return $this->unauthorizedResponse('UNAUTHENTICATED');
        }

        // Check if it's a User or Dealer
        if ($user instanceof User) {
            $userData = $user->makeHidden(['organization', 'organization_id', 'password'])->toArray();
            unset($userData['roles']); // Remove nested roles
            
            $data = [
                'user_type' => 'user',
                'user' => $userData,
                'roles' => $user->getRoleNames(),
            ];
        } else {
            $dealerData = $user->makeHidden(['password'])->toArray();
            
            $data = [
                'user_type' => 'dealer',
                'dealer' => $dealerData,
                'roles' => ['dealer'],
            ];
        }

        return $this->successResponse($data, 'USER PROFILE RETRIEVED');
    }
}

