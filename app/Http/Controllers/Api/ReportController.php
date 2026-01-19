<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseApiController
{
    /**
     * Get sales report (admin/accountant only).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sales(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->hasAnyRole(['admin', 'accountant'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $orders = Order::where('organization_id', $user->organization_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['dealer', 'items.product'])
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_amount' => $orders->sum('grand_total'),
            'total_paid' => Payment::whereHas('order', function ($q) use ($user, $startDate, $endDate) {
                $q->where('organization_id', $user->organization_id)
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })->sum('amount'),
            'by_status' => $orders->groupBy('status')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('grand_total'),
                ];
            }),
        ];

        return response()->json([
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => $summary,
            'orders' => $orders,
        ]);
    }

    /**
     * Get dealer performance report (admin/sales_officer only).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function dealerPerformance(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->hasAnyRole(['admin', 'sales_officer'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $dealerStats = Order::where('organization_id', $user->organization_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('dealer_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(grand_total) as total_amount'))
            ->groupBy('dealer_id')
            ->with('dealer')
            ->get();

        return response()->json([
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'dealers' => $dealerStats,
        ]);
    }
}
