<?php

namespace App\Livewire\Dashboard;

use App\Models\Dealer;
use App\Models\Dispatch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;

        // Basic Stats
        $totalOrders = Order::where('organization_id', $organizationId)->count();
        $activeDealers = Dealer::where('is_active', true)->count();
        $totalProducts = Product::where('organization_id', $organizationId)->where('is_active', true)->count();
        $pendingDispatches = Dispatch::whereHas('order', function($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })->where('status', 'pending')->count();

        // Revenue Stats
        $totalRevenue = Order::where('organization_id', $organizationId)
            ->where('status', '!=', 'cancelled')
            ->sum('grand_total');
        
        $monthlyRevenue = Order::where('organization_id', $organizationId)
            ->where('status', '!=', 'cancelled')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('grand_total');

        $totalPayments = Payment::whereHas('order', function($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })->where('status', 'completed')->sum('amount');

        $pendingPayments = Order::where('organization_id', $organizationId)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->sum(function($order) {
                return max(0, $order->grand_total - $order->paid_amount);
            });

        // Order Status Breakdown
        $orderStatuses = Order::where('organization_id', $organizationId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Monthly Revenue (Last 6 months)
        $monthlyRevenueData = Order::where('organization_id', $organizationId)
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(grand_total) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function($item) {
                return [date('M Y', strtotime($item->month . '-01')) => (float)$item->revenue];
            })
            ->toArray();

        // Top Products (by quantity sold)
        $topProducts = Order::where('organization_id', $organizationId)
            ->whereHas('items')
            ->with(['items.product'])
            ->get()
            ->flatMap(function($order) {
                return $order->items;
            })
            ->groupBy('product_id')
            ->map(function($items) {
                return [
                    'product' => $items->first()->product,
                    'total_quantity' => $items->sum('quantity'),
                    'total_revenue' => $items->sum('subtotal')
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(5)
            ->values();

        // Top Dealers (by revenue)
        $topDealers = Order::where('organization_id', $organizationId)
            ->where('status', '!=', 'cancelled')
            ->select('dealer_id', DB::raw('SUM(grand_total) as total_revenue'), DB::raw('COUNT(*) as order_count'))
            ->groupBy('dealer_id')
            ->with('dealer')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        // Recent Orders
        $recentOrders = Order::where('organization_id', $organizationId)
            ->with('dealer')
            ->latest()
            ->take(10)
            ->get();

        // Payment Status
        $paymentStatus = [
            'completed' => Payment::whereHas('order', function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })->where('status', 'completed')->count(),
            'pending' => Payment::whereHas('order', function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })->where('status', 'pending')->count(),
        ];

        return view('livewire.dashboard.dashboard', [
            'totalOrders' => $totalOrders,
            'activeDealers' => $activeDealers,
            'totalProducts' => $totalProducts,
            'pendingDispatches' => $pendingDispatches,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'totalPayments' => $totalPayments,
            'pendingPayments' => $pendingPayments,
            'orderStatuses' => $orderStatuses,
            'monthlyRevenueData' => $monthlyRevenueData,
            'topProducts' => $topProducts,
            'topDealers' => $topDealers,
            'recentOrders' => $recentOrders,
            'paymentStatus' => $paymentStatus,
        ]);
    }
}
