@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Edit Order</h1>
            <p class="text-muted">Order #{{ $order->order_number }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-muted">Order editing functionality will be implemented here.</p>
            <p class="text-muted">For now, orders can only be viewed. Edit functionality can be added based on business requirements.</p>
            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary">View Order</a>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
        </div>
    </div>
</div>
@endsection

