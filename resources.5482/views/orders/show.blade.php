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
                @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                <a href="{{ route('dispatches.create-for-order', $order->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-truck me-2"></i>Create Dispatch
                </a>
                @endif
                <a href="{{ route('payments.create-for-order', $order->id) }}" class="btn btn-success me-2">
                    <i class="fas fa-money-bill me-2"></i>Record Payment
                </a>
                <a href="{{ route('returns.create-for-order', $order->id) }}" class="btn btn-warning me-2">
                    <i class="fas fa-undo me-2"></i>Create Return
                </a>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>

    @if($order->dispatches->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Dispatches</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Dispatch Number</th>
                            <th>LR Number</th>
                            <th>Transporter</th>
                            <th>Vehicle Number</th>
                            <th>Status</th>
                            <th>Dispatched At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->dispatches as $dispatch)
                        <tr>
                            <td><strong>{{ $dispatch->dispatch_number }}</strong></td>
                            <td>{{ $dispatch->lr_number ?? 'N/A' }}</td>
                            <td>{{ $dispatch->transporter_name ?? 'N/A' }}</td>
                            <td>{{ $dispatch->vehicle_number ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $dispatch->status === 'pending' ? 'warning' : ($dispatch->status === 'dispatched' ? 'primary' : ($dispatch->status === 'in_transit' ? 'info' : 'success')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $dispatch->status)) }}
                                </span>
                            </td>
                            <td>{{ $dispatch->dispatched_at ? $dispatch->dispatched_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('dispatches.show', $dispatch->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

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
                                    <th>Size/Variant</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Rate</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>
                                        @if($item->productSize)
                                            {{ $item->productSize->size_label ?: ($item->productSize->size_value . ($item->productSize->unit ? $item->productSize->unit->symbol : '')) }}
                                        @else
                                            <span class="text-muted">Base</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                                    <td class="text-end">₹{{ number_format($item->subtotal, 2) }}</td>
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
                    <h5 class="mb-0">Payment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Order Total:</span>
                        <strong>₹{{ number_format($order->grand_total, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Paid Amount:</span>
                        <strong class="text-success">₹{{ number_format($order->paid_amount, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Remaining:</strong>
                        <strong class="text-{{ $order->remaining_amount > 0 ? 'danger' : 'success' }}">₹{{ number_format($order->remaining_amount, 2) }}</strong>
                    </div>
                    @if($order->payments->count() > 0)
                    <hr>
                    <small class="text-muted">
                        <a href="{{ route('payments.index') }}?search={{ $order->order_number }}" class="text-decoration-none">
                            View {{ $order->payments->count() }} payment(s)
                        </a>
                    </small>
                    @endif
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

