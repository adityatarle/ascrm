@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Dispatches</h1>
            <p class="text-muted">Track and manage order dispatches</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">All Dispatches</h5>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Search dispatches...">
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
                        @forelse(\App\Models\Dispatch::with(['order.dealer'])->whereHas('order', function($q) { $q->where('organization_id', auth()->user()->organization_id); })->latest()->get() as $dispatch)
                        <tr>
                            <td>{{ $dispatch->dispatch_number }}</td>
                            <td>{{ $dispatch->order->order_number }}</td>
                            <td>{{ $dispatch->order->dealer->name }}</td>
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
                                <a href="{{ route('dispatches.show', $dispatch->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $dispatch)
                                <a href="{{ route('dispatches.edit', $dispatch->id) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @role('admin')
                                <button onclick="deleteDispatch({{ $dispatch->id }})" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endrole
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No dispatches found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function deleteDispatch(dispatchId) {
    if (confirm('Are you sure you want to delete this dispatch?')) {
        // Add delete functionality via API or form submission
        fetch(`/dispatches/${dispatchId}`, {
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
                alert(data.message || 'Error deleting dispatch');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting dispatch');
        });
    }
}
</script>
@endsection

