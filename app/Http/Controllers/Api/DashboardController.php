<?php

namespace App\Http\Controllers\Api;

use App\Models\Dealer;
use App\Models\Dispatch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseApiController
{
    /**
     * Get dashboard data based on user type and role.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if ($user instanceof User) {
            return $this->getUserDashboard($user);
        } else {
            return $this->getDealerDashboard($user);
        }
    }

    /**
     * Get dashboard data for User (admin/accountant/sales/dispatch).
     */
    protected function getUserDashboard(User $user): JsonResponse
    {
        $organizationId = $user->organization_id;
        $stats = [];

        // Orders stats
        if ($user->hasAnyRole(['admin', 'sales_officer', 'accountant'])) {
            $stats['orders'] = [
                'total' => Order::where('organization_id', $organizationId)->count(),
                'pending' => Order::where('organization_id', $organizationId)
                    ->where('status', Order::STATUS_PENDING)->count(),
                'confirmed' => Order::where('organization_id', $organizationId)
                    ->where('status', Order::STATUS_CONFIRMED)->count(),
                'dispatched' => Order::where('organization_id', $organizationId)
                    ->where('status', Order::STATUS_DISPATCHED)->count(),
                'delivered' => Order::where('organization_id', $organizationId)
                    ->where('status', Order::STATUS_DELIVERED)->count(),
                'total_amount' => Order::where('organization_id', $organizationId)
                    ->sum('grand_total'),
            ];
        }

        // Dispatches stats
        if ($user->hasAnyRole(['admin', 'dispatch_officer', 'sales_officer'])) {
            $stats['dispatches'] = [
                'total' => Dispatch::whereHas('order', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                })->count(),
                'pending' => Dispatch::whereHas('order', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                })->where('status', Dispatch::STATUS_PENDING)->count(),
                'dispatched' => Dispatch::whereHas('order', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                })->where('status', Dispatch::STATUS_DISPATCHED)->count(),
                'in_transit' => Dispatch::whereHas('order', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                })->where('status', Dispatch::STATUS_IN_TRANSIT)->count(),
                'delivered' => Dispatch::whereHas('order', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                })->where('status', Dispatch::STATUS_DELIVERED)->count(),
            ];
        }

        // Payments stats
        if ($user->hasAnyRole(['admin', 'accountant'])) {
            $stats['payments'] = [
                'total' => Payment::whereHas('order', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                })->count(),
                'total_amount' => Payment::whereHas('order', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                })->sum('amount'),
            ];
        }

        // Dealers stats
        if ($user->hasAnyRole(['admin', 'sales_officer'])) {
            $stats['dealers'] = [
                'total' => Dealer::count(),
                'active' => Dealer::where('is_active', true)->count(),
            ];
        }

        // Recent orders
        $recentOrders = [];
        if ($user->hasAnyRole(['admin', 'sales_officer', 'accountant'])) {
            $recentOrders = Order::where('organization_id', $organizationId)
                ->with(['dealer', 'items.product'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        $data = [
            'user_type' => 'user',
            'roles' => $user->getRoleNames(),
            'stats' => $stats,
            'recent_orders' => $recentOrders->toArray(),
        ];

        return $this->successResponse($data, 'DASHBOARD DATA RETRIEVED SUCCESSFULLY');
    }

    /**
     * Get dashboard data for Dealer.
     */
    protected function getDealerDashboard(Dealer $dealer): JsonResponse
    {
        $stats = [
            'orders' => [
                'total' => Order::where('dealer_id', $dealer->id)->count(),
                'pending' => Order::where('dealer_id', $dealer->id)
                    ->where('status', Order::STATUS_PENDING)->count(),
                'confirmed' => Order::where('dealer_id', $dealer->id)
                    ->where('status', Order::STATUS_CONFIRMED)->count(),
                'dispatched' => Order::where('dealer_id', $dealer->id)
                    ->where('status', Order::STATUS_DISPATCHED)->count(),
                'delivered' => Order::where('dealer_id', $dealer->id)
                    ->where('status', Order::STATUS_DELIVERED)->count(),
                'total_amount' => Order::where('dealer_id', $dealer->id)->sum('grand_total'),
            ],
        ];

        $recentOrders = Order::where('dealer_id', $dealer->id)
            ->with(['items.product', 'dispatches'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $data = [
            'user_type' => 'dealer',
            'stats' => $stats,
            'recent_orders' => $recentOrders->toArray(),
        ];

        return $this->successResponse($data, 'DASHBOARD DATA RETRIEVED SUCCESSFULLY');
    }
}
