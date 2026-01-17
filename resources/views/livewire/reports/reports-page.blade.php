<div class="container-fluid">
    <!-- Print Header -->
    @php
        $organization = auth()->user()->organization;
        $organization->load(['state', 'city']);
    @endphp
    <x-report-header 
        :organization="$organization" 
        :title="ucfirst(str_replace('_', ' ', $activeTab)) . ' Report'"
        :dateFrom="$dateFrom"
        :dateTo="$dateTo"
    />

    <!-- Header -->
    <div class="row mb-4 no-print">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">Reports & Analytics 📊</h1>
                    <p class="text-muted mb-0">Comprehensive insights into your business performance</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('reports.export', ['type' => $activeTab, 'format' => 'pdf', 'dateFrom' => $dateFrom, 'dateTo' => $dateTo, 'dealer' => $selectedDealer, 'status' => $selectedStatus]) }}">
                                    <i class="fas fa-file-pdf me-2"></i>Export as PDF
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('reports.export', ['type' => $activeTab, 'format' => 'excel', 'dateFrom' => $dateFrom, 'dateTo' => $dateTo, 'dealer' => $selectedDealer, 'status' => $selectedStatus]) }}">
                                    <i class="fas fa-file-excel me-2"></i>Export as Excel
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4 no-print">
        <div class="col-12">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date From</label>
                            <input type="date" class="form-control" wire:model.live="dateFrom">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date To</label>
                            <input type="date" class="form-control" wire:model.live="dateTo">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dealer</label>
                            <select class="form-select" wire:model.live="selectedDealer">
                                <option value="">All Dealers</option>
                                @foreach($dealers as $dealer)
                                <option value="{{ $dealer->id }}">{{ $dealer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" wire:model.live="selectedStatus">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="dispatched">Dispatched</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Tabs -->
    <div class="row mb-4 no-print">
        <div class="col-12">
            <ul class="nav nav-pills report-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'sales' ? 'active' : '' }}" 
                            wire:click="setActiveTab('sales')" type="button">
                        <i class="fas fa-chart-line me-2"></i>Sales Report
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'orders' ? 'active' : '' }}" 
                            wire:click="setActiveTab('orders')" type="button">
                        <i class="fas fa-shopping-cart me-2"></i>Order Report
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'payments' ? 'active' : '' }}" 
                            wire:click="setActiveTab('payments')" type="button">
                        <i class="fas fa-money-bill-wave me-2"></i>Payment Report
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'products' ? 'active' : '' }}" 
                            wire:click="setActiveTab('products')" type="button">
                        <i class="fas fa-box me-2"></i>Product Report
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'dealers' ? 'active' : '' }}" 
                            wire:click="setActiveTab('dealers')" type="button">
                        <i class="fas fa-users me-2"></i>Dealer Report
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'dispatches' ? 'active' : '' }}" 
                            wire:click="setActiveTab('dispatches')" type="button">
                        <i class="fas fa-truck me-2"></i>Dispatch Report
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'gst' ? 'active' : '' }}" 
                            wire:click="setActiveTab('gst')" type="button">
                        <i class="fas fa-file-invoice-dollar me-2"></i>GST Report
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Sales Report -->
    @if($activeTab === 'sales')
    <div class="tab-content">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="report-stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h6>Total Revenue</h6>
                        <h3>₹{{ number_format($salesReport['total_revenue'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="report-stat-card stat-success">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h6>Total Orders</h6>
                        <h3>{{ number_format($salesReport['total_orders']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="report-stat-card stat-info">
                    <div class="stat-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="stat-content">
                        <h6>Avg Order Value</h6>
                        <h3>₹{{ number_format($salesReport['average_order_value'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="report-stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div class="stat-content">
                        <h6>Total Discount</h6>
                        <h3>₹{{ number_format($salesReport['total_discount'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Daily Sales Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="60"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Orders</h5>
                        <span class="badge bg-primary">{{ $salesReport['orders']->count() }} Orders</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Dealer</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-end">Discount</th>
                                        <th class="text-end">GST</th>
                                        <th class="text-end">Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesReport['orders'] as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->dealer->name ?? 'N/A' }}</td>
                                        <td class="text-end">₹{{ number_format($order->subtotal, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($order->discount_amount, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($order->cgst_amount + $order->sgst_amount + $order->igst_amount, 2) }}</td>
                                        <td class="text-end"><strong>₹{{ number_format($order->grand_total, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'confirmed' ? 'info' : ($order->status === 'dispatched' ? 'primary' : 'success')) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No orders found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Order Report -->
    @if($activeTab === 'orders')
    <div class="tab-content">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="report-stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-content">
                        <h6>Total Orders</h6>
                        <h3>{{ number_format($orderReport['total_orders']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-8 mb-3">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Orders by Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Daily Orders</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyOrdersChart" height="60"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Order Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Dealer</th>
                                        <th class="text-end">Amount</th>
                                        <th>Status</th>
                                        <th>Items</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orderReport['orders'] as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->dealer->name ?? 'N/A' }}</td>
                                        <td class="text-end">₹{{ number_format($order->grand_total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'confirmed' ? 'info' : ($order->status === 'dispatched' ? 'primary' : 'success')) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->items->count() }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No orders found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Payment Report -->
    @if($activeTab === 'payments')
    <div class="tab-content">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="report-stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-content">
                        <h6>Total Paid</h6>
                        <h3>₹{{ number_format($paymentReport['total_paid'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="report-stat-card stat-warning">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <h6>Pending</h6>
                        <h3>₹{{ number_format($paymentReport['total_pending'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Payment Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Daily Payments</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyPaymentsChart" height="60"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Payment Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment #</th>
                                        <th>Order #</th>
                                        <th>Dealer</th>
                                        <th class="text-end">Amount</th>
                                        <th>Mode</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($paymentReport['payments'] as $payment)
                                    <tr>
                                        <td><strong>#{{ $payment->id }}</strong></td>
                                        <td>{{ $payment->order->order_number ?? 'N/A' }}</td>
                                        <td>{{ $payment->order->dealer->name ?? 'N/A' }}</td>
                                        <td class="text-end">₹{{ number_format($payment->amount, 2) }}</td>
                                        <td><span class="badge bg-info">{{ ucfirst($payment->payment_mode) }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No payments found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Product Report -->
    @if($activeTab === 'products')
    <div class="tab-content">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="report-stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-box"></i></div>
                    <div class="stat-content">
                        <h6>Products Sold</h6>
                        <h3>{{ number_format($productReport['total_products_sold']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="report-stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="stat-content">
                        <h6>Total Quantity</h6>
                        <h3>{{ number_format($productReport['total_quantity']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="report-stat-card stat-info">
                    <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                    <div class="stat-content">
                        <h6>Total Revenue</h6>
                        <h3>₹{{ number_format($productReport['total_revenue'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Top Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Revenue</th>
                                        <th>Orders</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productReport['top_products'] as $index => $item)
                                    <tr>
                                        <td><strong>{{ $index + 1 }}</strong></td>
                                        <td>{{ $item['product']->name ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-primary">{{ number_format($item['total_quantity']) }}</span>
                                        </td>
                                        <td class="text-end"><strong>₹{{ number_format($item['total_revenue'], 2) }}</strong></td>
                                        <td>{{ $item['order_count'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Dealer Report -->
    @if($activeTab === 'dealers')
    <div class="tab-content">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="report-stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-content">
                        <h6>Active Dealers</h6>
                        <h3>{{ number_format($dealerReport['total_dealers']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="report-stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                    <div class="stat-content">
                        <h6>Total Revenue</h6>
                        <h3>₹{{ number_format($dealerReport['total_revenue'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top Dealers</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Dealer</th>
                                        <th class="text-end">Orders</th>
                                        <th class="text-end">Revenue</th>
                                        <th class="text-end">Avg Order Value</th>
                                        <th>Last Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dealerReport['top_dealers'] as $index => $item)
                                    <tr>
                                        <td><strong>{{ $index + 1 }}</strong></td>
                                        <td>{{ $item['dealer']->name ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-success">{{ $item['order_count'] }}</span>
                                        </td>
                                        <td class="text-end"><strong>₹{{ number_format($item['total_revenue'], 2) }}</strong></td>
                                        <td class="text-end">₹{{ number_format($item['average_order_value'], 2) }}</td>
                                        <td>{{ $item['last_order_date'] ? $item['last_order_date']->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Dispatch Report -->
    @if($activeTab === 'dispatches')
    <div class="tab-content">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="report-stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-truck"></i></div>
                    <div class="stat-content">
                        <h6>Total Dispatches</h6>
                        <h3>{{ number_format($dispatchReport['total_dispatches']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="report-stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-boxes"></i></div>
                    <div class="stat-content">
                        <h6>Items Dispatched</h6>
                        <h3>{{ number_format($dispatchReport['total_items_dispatched']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Dispatch Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dispatchStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Dispatch Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Dispatch #</th>
                                        <th>Order #</th>
                                        <th>Dealer</th>
                                        <th>LR Number</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dispatchReport['dispatches'] as $dispatch)
                                    <tr>
                                        <td><strong>#{{ $dispatch->id }}</strong></td>
                                        <td>{{ $dispatch->order->order_number ?? 'N/A' }}</td>
                                        <td>{{ $dispatch->order->dealer->name ?? 'N/A' }}</td>
                                        <td>{{ $dispatch->lr_number ?? 'N/A' }}</td>
                                        <td>{{ $dispatch->items->sum('quantity') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $dispatch->status === 'pending' ? 'warning' : ($dispatch->status === 'in_transit' ? 'info' : 'success') }}">
                                                {{ ucfirst(str_replace('_', ' ', $dispatch->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $dispatch->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No dispatches found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- GST Report -->
    @if($activeTab === 'gst')
    <div class="tab-content">
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="report-stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                    <div class="stat-content">
                        <h6>CGST</h6>
                        <h3>₹{{ number_format($gstReport['total_cgst'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="report-stat-card stat-info">
                    <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                    <div class="stat-content">
                        <h6>SGST</h6>
                        <h3>₹{{ number_format($gstReport['total_sgst'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="report-stat-card stat-warning">
                    <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                    <div class="stat-content">
                        <h6>IGST</h6>
                        <h3>₹{{ number_format($gstReport['total_igst'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="report-stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-calculator"></i></div>
                    <div class="stat-content">
                        <h6>Total GST</h6>
                        <h3>₹{{ number_format($gstReport['total_gst'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Monthly GST Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyGstChart" height="60"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>GST by State</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>State</th>
                                        <th class="text-end">CGST</th>
                                        <th class="text-end">SGST</th>
                                        <th class="text-end">IGST</th>
                                        <th class="text-end">Total GST</th>
                                        <th class="text-end">Orders</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gstReport['by_state'] as $state => $data)
                                    <tr>
                                        <td><strong>{{ $state }}</strong></td>
                                        <td class="text-end">₹{{ number_format($data['cgst'], 2) }}</td>
                                        <td class="text-end">₹{{ number_format($data['sgst'], 2) }}</td>
                                        <td class="text-end">₹{{ number_format($data['igst'], 2) }}</td>
                                        <td class="text-end"><strong>₹{{ number_format($data['cgst'] + $data['sgst'] + $data['igst'], 2) }}</strong></td>
                                        <td class="text-end">{{ $data['count'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script type="module">
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    @if($activeTab === 'sales')
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        const salesData = @json($salesReport['daily_sales'] ?? []);
        const labels = Object.keys(salesData).sort();
        const data = labels.map(date => salesData[date] || 0);

        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: labels.map(d => new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' })),
                datasets: [{
                    label: 'Revenue (₹)',
                    data: data,
                    borderColor: '#267b3f',
                    backgroundColor: 'rgba(38, 123, 63, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₹' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Order Status Chart
    @if($activeTab === 'orders')
    const orderStatusCtx = document.getElementById('orderStatusChart');
    if (orderStatusCtx) {
        const statusData = @json($orderReport['by_status'] ?? []);
        const labels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1));
        const data = Object.values(statusData);
        const colors = ['#f5a623', '#267b3f', '#0ea5e9', '#10b981'];

        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    const dailyOrdersCtx = document.getElementById('dailyOrdersChart');
    if (dailyOrdersCtx) {
        const dailyData = @json($orderReport['daily_orders'] ?? []);
        const labels = Object.keys(dailyData).sort();
        const data = labels.map(date => dailyData[date] || 0);

        new Chart(dailyOrdersCtx, {
            type: 'bar',
            data: {
                labels: labels.map(d => new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' })),
                datasets: [{
                    label: 'Orders',
                    data: data,
                    backgroundColor: '#267b3f',
                    borderColor: '#267b3f',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
    @endif

    // Payment Status Chart
    @if($activeTab === 'payments')
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
        const statusData = @json($paymentReport['by_status'] ?? []);
        const labels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1));
        const data = Object.values(statusData);
        const colors = ['#10b981', '#f59e0b'];

        new Chart(paymentStatusCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    const dailyPaymentsCtx = document.getElementById('dailyPaymentsChart');
    if (dailyPaymentsCtx) {
        const dailyData = @json($paymentReport['daily_payments'] ?? []);
        const labels = Object.keys(dailyData).sort();
        const data = labels.map(date => dailyData[date] || 0);

        new Chart(dailyPaymentsCtx, {
            type: 'line',
            data: {
                labels: labels.map(d => new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' })),
                datasets: [{
                    label: 'Payments (₹)',
                    data: data,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₹' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Dispatch Status Chart
    @if($activeTab === 'dispatches')
    const dispatchStatusCtx = document.getElementById('dispatchStatusChart');
    if (dispatchStatusCtx) {
        const statusData = @json($dispatchReport['by_status'] ?? []);
        const labels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' '));
        const data = Object.values(statusData);
        const colors = ['#f59e0b', '#0ea5e9', '#10b981'];

        new Chart(dispatchStatusCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
    @endif

    // Monthly GST Chart
    @if($activeTab === 'gst')
    const monthlyGstCtx = document.getElementById('monthlyGstChart');
    if (monthlyGstCtx) {
        const monthlyData = @json($gstReport['monthly_gst'] ?? []);
        const labels = Object.keys(monthlyData).sort();
        const cgst = labels.map(m => monthlyData[m]['cgst'] || 0);
        const sgst = labels.map(m => monthlyData[m]['sgst'] || 0);
        const igst = labels.map(m => monthlyData[m]['igst'] || 0);

        new Chart(monthlyGstCtx, {
            type: 'bar',
            data: {
                labels: labels.map(m => {
                    const [year, month] = m.split('-');
                    return new Date(year, month - 1).toLocaleDateString('en-IN', { month: 'short', year: 'numeric' });
                }),
                datasets: [
                    {
                        label: 'CGST',
                        data: cgst,
                        backgroundColor: '#267b3f'
                    },
                    {
                        label: 'SGST',
                        data: sgst,
                        backgroundColor: '#0ea5e9'
                    },
                    {
                        label: 'IGST',
                        data: igst,
                        backgroundColor: '#f59e0b'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₹' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: false,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endpush
