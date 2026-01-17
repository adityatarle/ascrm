<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Returns</h1>
                <p class="text-muted">View and manage order returns</p>
            </div>
            <a href="{{ route('returns.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Return
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
                <div class="col-md-4">
                    <h5 class="mb-0">All Returns</h5>
                </div>
                <div class="col-md-4">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           class="form-control" 
                           placeholder="Search by return number, order number...">
                </div>
                <div class="col-md-4">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="processed">Processed</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Return Number</th>
                            <th>Order Number</th>
                            <th>Dealer</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Returned At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $return)
                        <tr>
                            <td><strong>{{ $return->return_number }}</strong></td>
                            <td>
                                <a href="{{ route('orders.show', $return->order_id) }}" class="text-decoration-none">
                                    {{ $return->order->order_number }}
                                </a>
                            </td>
                            <td>{{ $return->dealer->name }}</td>
                            <td>{{ $return->items->count() }} items</td>
                            <td>
                                <span class="badge bg-{{ $return->status === 'approved' ? 'success' : ($return->status === 'pending' ? 'warning' : ($return->status === 'rejected' ? 'danger' : 'info')) }}">
                                    {{ ucfirst($return->status) }}
                                </span>
                            </td>
                            <td>{{ $return->returned_at ? $return->returned_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('returns.show', $return->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('returns.edit', $return->id) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($return->status === 'pending')
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" wire:click="updateStatus({{ $return->id }}, 'approved')" href="#">
                                                    <i class="fas fa-check me-2"></i>Approve
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" wire:click="updateStatus({{ $return->id }}, 'rejected')" href="#">
                                                    <i class="fas fa-times me-2"></i>Reject
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                    @role('admin')
                                    <button wire:click="delete({{ $return->id }})" 
                                            wire:confirm="Are you sure you want to delete this return?" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <p class="text-muted mb-0">No returns found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $returns->links() }}
            </div>
        </div>
    </div>
</div>
