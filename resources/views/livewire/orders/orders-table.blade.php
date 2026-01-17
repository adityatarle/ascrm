<div>
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

    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0">All Orders</h5>
                </div>
                <div class="col-md-4">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           class="form-control" 
                           placeholder="Search by order number, dealer name or mobile...">
                </div>
                <div class="col-md-4">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="dispatched">Dispatched</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
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
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">GST</th>
                            <th class="text-end">Grand Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>
                                <div>{{ $order->dealer->name }}</div>
                                <small class="text-muted">{{ $order->dealer->mobile }}</small>
                            </td>
                            <td class="text-end">₹{{ number_format($order->subtotal, 2) }}</td>
                            <td class="text-end text-success">-₹{{ number_format($order->discount_amount, 2) }}</td>
                            <td class="text-end">
                                ₹{{ number_format($order->cgst_amount + $order->sgst_amount + $order->igst_amount, 2) }}
                            </td>
                            <td class="text-end"><strong>₹{{ number_format($order->grand_total, 2) }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'confirmed' ? 'info' : ($order->status === 'dispatched' ? 'primary' : ($order->status === 'delivered' ? 'success' : 'danger'))) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($order->status === 'pending')
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" wire:click="updateStatus({{ $order->id }}, 'confirmed')" href="#">
                                                    <i class="fas fa-check me-2"></i>Confirm
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" wire:click="updateStatus({{ $order->id }}, 'cancelled')" href="#">
                                                    <i class="fas fa-times me-2"></i>Cancel
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                    @role('admin')
                                    @if($order->status === 'pending')
                                    <button wire:click="delete({{ $order->id }})" 
                                            wire:confirm="Are you sure you want to delete this order?" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                    @endrole
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <p class="text-muted mb-0">No orders found.</p>
                                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary mt-2">Create one now</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
