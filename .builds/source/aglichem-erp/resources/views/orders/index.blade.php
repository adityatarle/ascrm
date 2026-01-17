@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Orders</h1>
                <p class="text-muted">View and manage orders</p>
            </div>
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create New Order
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">All Orders</h5>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Search orders...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Dealer</th>
                            <th class="text-number">Subtotal</th>
                            <th class="text-number">Discount</th>
                            <th class="text-number">GST</th>
                            <th class="text-number">Grand Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\App\Models\Order::with(['dealer', 'organization'])->where('organization_id', auth()->user()->organization_id)->latest()->get() as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->dealer->name }}</td>
                            <td class="text-number">₹{{ number_format($order->subtotal, 2) }}</td>
                            <td class="text-number">₹{{ number_format($order->discount_amount, 2) }}</td>
                            <td class="text-number">
                                ₹{{ number_format($order->cgst_amount + $order->sgst_amount + $order->igst_amount, 2) }}
                            </td>
                            <td class="text-number">₹{{ number_format($order->grand_total, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'confirmed' ? 'info' : ($order->status === 'dispatched' ? 'primary' : 'success')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $order)
                                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @role('admin')
                                @if($order->status === 'pending')
                                <button onclick="deleteOrder({{ $order->id }})" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                                @endrole
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No orders found. <a href="{{ route('orders.create') }}">Create one now</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function deleteOrder(orderId) {
    if (confirm('Are you sure you want to delete this order?')) {
        // Add delete functionality via API or form submission
        fetch(`/orders/${orderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting order');
        });
    }
}
</script>
@endsection

