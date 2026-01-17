<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-header">
                <h1 class="h2 mb-1">Welcome back, {{ auth()->user()->name }}! 👋</h1>
                <p class="text-muted mb-0">{{ now()->format('l, F j, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-primary">
                <div class="stat-card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-card-content">
                    <h6 class="stat-card-label">Total Orders</h6>
                    <h2 class="stat-card-value">{{ number_format($totalOrders) }}</h2>
                    <small class="stat-card-change text-success">
                        <i class="fas fa-arrow-up"></i> All time
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-success">
                <div class="stat-card-icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="stat-card-content">
                    <h6 class="stat-card-label">Total Revenue</h6>
                    <h2 class="stat-card-value">₹{{ number_format($totalRevenue, 2) }}</h2>
                    <small class="stat-card-change text-success">
                        <i class="fas fa-arrow-up"></i> ₹{{ number_format($monthlyRevenue, 2) }} this month
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-info">
                <div class="stat-card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-content">
                    <h6 class="stat-card-label">Active Dealers</h6>
                    <h2 class="stat-card-value">{{ number_format($activeDealers) }}</h2>
                    <small class="stat-card-change text-info">
                        <i class="fas fa-user-check"></i> Active
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-card-content">
                    <h6 class="stat-card-label">Pending Dispatches</h6>
                    <h2 class="stat-card-value">{{ number_format($pendingDispatches) }}</h2>
                    <small class="stat-card-change text-warning">
                        <i class="fas fa-clock"></i> Needs attention
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 mb-4">
            <div class="card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Revenue Trend (Last 6 Months)
                    </h5>
                    <div class="chart-legend">
                        <span class="legend-item">
                            <span class="legend-color" style="background: #267b3f;"></span>
                            Revenue
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Order Status Chart -->
        <div class="col-xl-4 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Order Status
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <!-- Payment Stats -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-mini-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-mini-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Total Payments</h6>
                            <h4 class="mb-0">₹{{ number_format($totalPayments, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-mini-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-mini-icon bg-warning">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Pending Payments</h6>
                            <h4 class="mb-0">₹{{ number_format($pendingPayments, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-mini-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-mini-icon bg-info">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Total Products</h6>
                            <h4 class="mb-0">{{ number_format($totalProducts) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-mini-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-mini-icon bg-primary">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Monthly Revenue</h6>
                            <h4 class="mb-0">₹{{ number_format($monthlyRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="row">
        <!-- Top Products -->
        <div class="col-xl-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>Top Products
                    </h5>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item['product']->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ number_format($item['total_quantity']) }}</span>
                                    </td>
                                    <td class="text-end">₹{{ number_format($item['total_revenue'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No products data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Dealers -->
        <div class="col-xl-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>Top Dealers
                    </h5>
                    <a href="{{ route('dealers.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Dealer</th>
                                    <th class="text-end">Orders</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topDealers as $dealer)
                                <tr>
                                    <td>
                                        <strong>{{ $dealer->dealer->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success">{{ $dealer->order_count }}</span>
                                    </td>
                                    <td class="text-end">₹{{ number_format($dealer->total_revenue, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No dealers data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Orders
                    </h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Dealer</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="text-decoration-none">
                                            <strong>{{ $order->order_number }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ $order->dealer->name ?? 'N/A' }}</td>
                                    <td class="text-end">₹{{ number_format($order->grand_total, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'confirmed' ? 'info' : ($order->status === 'dispatched' ? 'primary' : 'success')) }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No orders found</td>
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

@push('scripts')
<script type="module">
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueData = @json($monthlyRevenueData ?? []);
        const labels = Object.keys(revenueData).length > 0 ? Object.keys(revenueData) : ['No Data'];
        const data = Object.keys(revenueData).length > 0 ? Object.values(revenueData) : [0];

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: data,
                    borderColor: '#267b3f',
                    backgroundColor: 'rgba(38, 123, 63, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#267b3f',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₹' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2});
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
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Order Status Chart
    const statusCtx = document.getElementById('orderStatusChart');
    if (statusCtx) {
        const statusData = @json($orderStatuses ?? []);
        const labels = Object.keys(statusData).length > 0 
            ? Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1))
            : ['No Data'];
        const data = Object.keys(statusData).length > 0 ? Object.values(statusData) : [1];
        const colors = ['#f5a623', '#267b3f', '#0ea5e9', '#10b981', '#ef4444'];

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
