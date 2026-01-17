@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Dashboard</h1>
            <p class="text-muted">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Orders</h6>
                            <h3 class="mb-0">{{ \App\Models\Order::where('organization_id', auth()->user()->organization_id)->count() }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Active Dealers</h6>
                            <h3 class="mb-0">{{ \App\Models\Dealer::where('is_active', true)->count() }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Products</h6>
                            <h3 class="mb-0">{{ \App\Models\Product::where('organization_id', auth()->user()->organization_id)->where('is_active', true)->count() }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pending Dispatches</h6>
                            <h3 class="mb-0">{{ \App\Models\Dispatch::whereHas('order', function($q) { $q->where('organization_id', auth()->user()->organization_id); })->where('status', 'pending')->count() }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
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
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Dealer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Order::where('organization_id', auth()->user()->organization_id)->latest()->take(10)->get() as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->dealer->name }}</td>
                                    <td class="text-number">₹{{ number_format($order->grand_total, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'confirmed' ? 'info' : ($order->status === 'dispatched' ? 'primary' : 'success')) }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No orders found</td>
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
@endsection

