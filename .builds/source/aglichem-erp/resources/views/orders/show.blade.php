@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Order Details</h1>
                <p class="text-muted">Order #{{ $order->order_number }}</p>
            </div>
            <div>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th class="text-number">Rate</th>
                                    <th class="text-number">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-number">₹{{ number_format($item->rate, 2) }}</td>
                                    <td class="text-number">₹{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>₹{{ number_format($order->subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount:</span>
                        <strong>₹{{ number_format($order->discount_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Taxable Amount:</span>
                        <strong>₹{{ number_format($order->taxable_amount, 2) }}</strong>
                    </div>
                    @if($order->cgst_amount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>CGST (9%):</span>
                        <strong>₹{{ number_format($order->cgst_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>SGST (9%):</span>
                        <strong>₹{{ number_format($order->sgst_amount, 2) }}</strong>
                    </div>
                    @endif
                    @if($order->igst_amount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>IGST (18%):</span>
                        <strong>₹{{ number_format($order->igst_amount, 2) }}</strong>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Grand Total:</strong>
                        <strong class="text-primary">₹{{ number_format($order->grand_total, 2) }}</strong>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Dealer Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $order->dealer->name }}</p>
                    <p><strong>Mobile:</strong> {{ $order->dealer->mobile }}</p>
                    <p><strong>Email:</strong> {{ $order->dealer->email ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $order->dealer->address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

