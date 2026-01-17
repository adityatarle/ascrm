@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Dispatch Details</h1>
                <p class="text-muted">Dispatch #{{ $dispatch->dispatch_number }}</p>
            </div>
            <div>
                <a href="{{ route('dispatches.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dispatches
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Dispatch Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Dispatch Number:</strong> {{ $dispatch->dispatch_number }}</p>
                    <p><strong>Order Number:</strong> {{ $dispatch->order->order_number }}</p>
                    <p><strong>LR Number:</strong> {{ $dispatch->lr_number ?? 'N/A' }}</p>
                    <p><strong>Transporter:</strong> {{ $dispatch->transporter_name ?? 'N/A' }}</p>
                    <p><strong>Vehicle Number:</strong> {{ $dispatch->vehicle_number ?? 'N/A' }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $dispatch->status === 'pending' ? 'warning' : ($dispatch->status === 'dispatched' ? 'primary' : ($dispatch->status === 'in_transit' ? 'info' : 'success')) }}">
                            {{ ucfirst(str_replace('_', ' ', $dispatch->status)) }}
                        </span>
                    </p>
                    <p><strong>Dispatched At:</strong> {{ $dispatch->dispatched_at ? $dispatch->dispatched_at->format('d/m/Y H:i') : 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Dealer:</strong> {{ $dispatch->order->dealer->name }}</p>
                    <p><strong>Grand Total:</strong> ₹{{ number_format($dispatch->order->grand_total, 2) }}</p>
                    <p><strong>Order Status:</strong> 
                        <span class="badge bg-{{ $dispatch->order->status === 'pending' ? 'warning' : ($dispatch->order->status === 'confirmed' ? 'info' : ($dispatch->order->status === 'dispatched' ? 'primary' : 'success')) }}">
                            {{ ucfirst($dispatch->order->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

