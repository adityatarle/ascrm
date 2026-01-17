@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Reports</h1>
            <p class="text-muted">View reports and analytics</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Sales</h6>
                            <h3 class="mb-0">₹{{ number_format(\App\Models\Order::where('organization_id', auth()->user()->organization_id)->sum('grand_total'), 2) }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-rupee-sign fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Total GST</h6>
                            <h3 class="mb-0">₹{{ number_format(\App\Models\Order::where('organization_id', auth()->user()->organization_id)->sum(\DB::raw('cgst_amount + sgst_amount + igst_amount')), 2) }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-file-invoice-dollar fa-2x"></i>
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
                        <div class="text-warning">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Sales Report</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Sales reports and analytics will be displayed here.</p>
            <p class="text-muted">This section can be extended with charts, graphs, and detailed reports.</p>
        </div>
    </div>
</div>
@endsection

