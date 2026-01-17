<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Dispatches</h1>
                <p class="text-muted">Track and manage order dispatches</p>
            </div>
            <a href="{{ route('dispatches.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create New Dispatch
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
                    <h5 class="mb-0">All Dispatches</h5>
                </div>
                <div class="col-md-4">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           class="form-control" 
                           placeholder="Search by dispatch number, LR number, vehicle...">
                </div>
                <div class="col-md-4">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="dispatched">Dispatched</option>
                        <option value="in_transit">In Transit</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Dispatch Number</th>
                            <th>Order Number</th>
                            <th>Dealer</th>
                            <th>LR Number</th>
                            <th>Transporter</th>
                            <th>Vehicle Number</th>
                            <th>Status</th>
                            <th>Dispatched At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dispatches as $dispatch)
                        <tr>
                            <td><strong>{{ $dispatch->dispatch_number }}</strong></td>
                            <td>
                                <a href="{{ route('orders.show', $dispatch->order_id) }}" class="text-decoration-none">
                                    {{ $dispatch->order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div>{{ $dispatch->order->dealer->name }}</div>
                                <small class="text-muted">{{ $dispatch->order->dealer->mobile }}</small>
                            </td>
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
                                <div class="btn-group" role="group">
                                    <a href="{{ route('dispatches.show', $dispatch->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('dispatches.edit', $dispatch->id) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($dispatch->status !== 'delivered')
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($dispatch->status === 'dispatched')
                                            <li>
                                                <a class="dropdown-item" wire:click="updateStatus({{ $dispatch->id }}, 'in_transit')" href="#">
                                                    <i class="fas fa-truck me-2"></i>Mark In Transit
                                                </a>
                                            </li>
                                            @endif
                                            @if($dispatch->status === 'in_transit')
                                            <li>
                                                <a class="dropdown-item" wire:click="updateStatus({{ $dispatch->id }}, 'delivered')" href="#">
                                                    <i class="fas fa-check me-2"></i>Mark Delivered
                                                </a>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                    @endif
                                    @role('admin')
                                    <button wire:click="delete({{ $dispatch->id }})" 
                                            wire:confirm="Are you sure you want to delete this dispatch?" 
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
                            <td colspan="9" class="text-center py-4">
                                <p class="text-muted mb-0">No dispatches found.</p>
                                <a href="{{ route('dispatches.create') }}" class="btn btn-sm btn-primary mt-2">Create one now</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $dispatches->links() }}
            </div>
        </div>
    </div>
</div>
