<?php

namespace App\Livewire\Reports;

use App\Models\Dealer;
use App\Models\Dispatch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReportsPage extends Component
{
    public $activeTab = 'sales';
    public $dateFrom;
    public $dateTo;
    public $selectedDealer = '';
    public $selectedStatus = '';

    public function mount()
    {
        // Set default date range to last 30 days
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedDateFrom()
    {
        $this->dispatch('dateRangeChanged');
    }

    public function updatedDateTo()
    {
        $this->dispatch('dateRangeChanged');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    protected function getBaseQuery()
    {
        $query = Order::where('organization_id', Auth::user()->organization_id)
            ->where('status', '!=', 'cancelled');

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->selectedDealer) {
            $query->where('dealer_id', $this->selectedDealer);
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        return $query;
    }

    public function getSalesReportProperty()
    {
        $orders = $this->getBaseQuery()->with('dealer')->get();

        return [
            'total_revenue' => $orders->sum('grand_total'),
            'total_orders' => $orders->count(),
            'average_order_value' => $orders->count() > 0 ? $orders->sum('grand_total') / $orders->count() : 0,
            'total_discount' => $orders->sum('discount_amount'),
            'total_taxable' => $orders->sum('taxable_amount'),
            'daily_sales' => $orders->groupBy(function($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function($dayOrders) {
                return $dayOrders->sum('grand_total');
            }),
            'orders' => $orders->take(50),
        ];
    }

    public function getOrderReportProperty()
    {
        $orders = $this->getBaseQuery()->with(['dealer', 'items.product'])->get();

        return [
            'total_orders' => $orders->count(),
            'by_status' => $orders->groupBy('status')->map->count(),
            'by_dealer' => $orders->groupBy('dealer_id')->map(function($dealerOrders) {
                return [
                    'count' => $dealerOrders->count(),
                    'revenue' => $dealerOrders->sum('grand_total'),
                    'dealer' => $dealerOrders->first()->dealer ?? null,
                ];
            })->sortByDesc('count')->take(10),
            'daily_orders' => $orders->groupBy(function($order) {
                return $order->created_at->format('Y-m-d');
            })->map->count(),
            'orders' => $orders->take(50),
        ];
    }

    public function getPaymentReportProperty()
    {
        $orders = $this->getBaseQuery()->get();
        $orderIds = $orders->pluck('id');

        $payments = Payment::whereIn('order_id', $orderIds)
            ->with(['order.dealer'])
            ->get();

        return [
            'total_paid' => $payments->where('status', 'completed')->sum('amount'),
            'total_pending' => $orders->sum(function($order) {
                return max(0, $order->grand_total - $order->paid_amount);
            }),
            'by_status' => $payments->groupBy('status')->map->count(),
            'by_mode' => $payments->groupBy('payment_mode')->map->sum('amount'),
            'daily_payments' => $payments->where('status', 'completed')
                ->groupBy(function($payment) {
                    return $payment->created_at->format('Y-m-d');
                })->map->sum('amount'),
            'payments' => $payments->take(50),
        ];
    }

    public function getProductReportProperty()
    {
        $orders = $this->getBaseQuery()->with(['items.product'])->get();
        
        $productData = $orders->flatMap(function($order) {
            return $order->items;
        })->groupBy('product_id')->map(function($items) {
            $product = $items->first()->product;
            return [
                'product' => $product,
                'total_quantity' => $items->sum('quantity'),
                'total_revenue' => $items->sum('subtotal'),
                'order_count' => $items->groupBy('order_id')->count(),
            ];
        })->sortByDesc('total_revenue');

        return [
            'top_products' => $productData->take(20),
            'total_products_sold' => $productData->count(),
            'total_quantity' => $productData->sum('total_quantity'),
            'total_revenue' => $productData->sum('total_revenue'),
        ];
    }

    public function getDealerReportProperty()
    {
        $orders = $this->getBaseQuery()->with('dealer')->get();

        $dealerData = $orders->groupBy('dealer_id')->map(function($dealerOrders) {
            $dealer = $dealerOrders->first()->dealer;
            return [
                'dealer' => $dealer,
                'order_count' => $dealerOrders->count(),
                'total_revenue' => $dealerOrders->sum('grand_total'),
                'average_order_value' => $dealerOrders->count() > 0 
                    ? $dealerOrders->sum('grand_total') / $dealerOrders->count() 
                    : 0,
                'last_order_date' => $dealerOrders->max('created_at'),
            ];
        })->sortByDesc('total_revenue');

        return [
            'top_dealers' => $dealerData->take(20),
            'total_dealers' => $dealerData->count(),
            'total_revenue' => $dealerData->sum('total_revenue'),
        ];
    }

    public function getDispatchReportProperty()
    {
        $orders = $this->getBaseQuery()->get();
        $orderIds = $orders->pluck('id');

        $dispatches = Dispatch::whereIn('order_id', $orderIds)
            ->with(['order.dealer', 'items'])
            ->get();

        return [
            'total_dispatches' => $dispatches->count(),
            'by_status' => $dispatches->groupBy('status')->map->count(),
            'total_items_dispatched' => $dispatches->sum(function($dispatch) {
                return $dispatch->items->sum('quantity');
            }),
            'daily_dispatches' => $dispatches->groupBy(function($dispatch) {
                return $dispatch->created_at->format('Y-m-d');
            })->map->count(),
            'dispatches' => $dispatches->take(50),
        ];
    }

    public function getGstReportProperty()
    {
        $orders = $this->getBaseQuery()->with(['dealer.state'])->get();

        return [
            'total_cgst' => $orders->sum('cgst_amount'),
            'total_sgst' => $orders->sum('sgst_amount'),
            'total_igst' => $orders->sum('igst_amount'),
            'total_gst' => $orders->sum(function($order) {
                return $order->cgst_amount + $order->sgst_amount + $order->igst_amount;
            }),
            'by_state' => $orders->groupBy(function($order) {
                return $order->dealer->state->name ?? 'Unknown';
            })->map(function($stateOrders) {
                return [
                    'cgst' => $stateOrders->sum('cgst_amount'),
                    'sgst' => $stateOrders->sum('sgst_amount'),
                    'igst' => $stateOrders->sum('igst_amount'),
                    'count' => $stateOrders->count(),
                ];
            }),
            'monthly_gst' => $orders->groupBy(function($order) {
                return $order->created_at->format('Y-m');
            })->map(function($monthOrders) {
                return [
                    'cgst' => $monthOrders->sum('cgst_amount'),
                    'sgst' => $monthOrders->sum('sgst_amount'),
                    'igst' => $monthOrders->sum('igst_amount'),
                ];
            }),
        ];
    }

    public function render()
    {
        $dealers = Dealer::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.reports.reports-page', [
            'dealers' => $dealers,
            'salesReport' => $this->salesReport,
            'orderReport' => $this->orderReport,
            'paymentReport' => $this->paymentReport,
            'productReport' => $this->productReport,
            'dealerReport' => $this->dealerReport,
            'dispatchReport' => $this->dispatchReport,
            'gstReport' => $this->gstReport,
        ]);
    }
}
