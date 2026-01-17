<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Dealers</h1>
                <p class="text-muted">Manage your dealers and customers</p>
            </div>
            <a href="{{ route('dealers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Dealer
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Dealers</h5>
            <div class="d-flex gap-2">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" wire:model.live="showInactive" id="showInactive">
                    <label class="form-check-label" for="showInactive">Show Inactive</label>
                </div>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search dealers..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Zone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dealers as $dealer)
                        <tr>
                            <td>{{ $dealer->name }}</td>
                            <td>{{ $dealer->mobile }}</td>
                            <td>{{ $dealer->email ?? 'N/A' }}</td>
                            <td>{{ $dealer->city->name ?? 'N/A' }}</td>
                            <td>{{ $dealer->state->name ?? 'N/A' }}</td>
                            <td>{{ $dealer->zone->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $dealer->is_active ? 'success' : 'secondary' }}">
                                    {{ $dealer->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('dealers.edit', $dealer->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @role('admin')
                                <button wire:click="delete({{ $dealer->id }})" 
                                        wire:confirm="Are you sure you want to delete this dealer?"
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endrole
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No dealers found. <a href="{{ route('dealers.create') }}">Create one now</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $dealers->links() }}
            </div>
        </div>
    </div>
</div>

