<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Payments</h1>
                <p class="text-muted">View and manage all payments</p>
            </div>
            <a href="{{ route('payments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Record Payment
            </a>
        </div>
    </div>

    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h5 class="mb-0">All Payments</h5>
                </div>
                <div class="col-md-3">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           class="form-control" 
                           placeholder="Search...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="methodFilter" class="form-select">
                        <option value="">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="neft">NEFT</option>
                        <option value="rtgs">RTGS</option>
                        <option value="upi">UPI</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Payment Date</th>
                            <th>Order Number</th>
                            <th>Dealer</th>
                            <th class="text-end">Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $payment->order_id) }}" class="text-decoration-none">
                                    {{ $payment->order->order_number }}
                                </a>
                            </td>
                            <td>{{ $payment->order->dealer->name }}</td>
                            <td class="text-end"><strong>₹{{ number_format($payment->amount, 2) }}</strong></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td>
                                {{ $payment->transaction_id ?: $payment->cheque_number ?: $payment->reference_number ?: 'N/A' }}
                            </td>
                            <td>
                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : ($payment->status === 'failed' ? 'danger' : 'info')) }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @role('admin')
                                <button wire:click="delete({{ $payment->id }})" 
                                        wire:confirm="Are you sure you want to delete this payment?" 
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endrole
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <p class="text-muted mb-0">No payments found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
