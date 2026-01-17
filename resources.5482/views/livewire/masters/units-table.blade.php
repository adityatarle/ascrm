<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Unit Master</h1>
                <p class="text-muted">Manage units of measurement (Kg, Liter, Piece, etc.)</p>
            </div>
            <a href="{{ route('units.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Unit
            </a>
        </div>
    </div>

    @if(session('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Units</h5>
            <div class="d-flex gap-2">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" wire:model.live="showInactive" id="showInactive">
                    <label class="form-check-label" for="showInactive">Show Inactive</label>
                </div>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search units..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sort Order</th>
                            <th>Name</th>
                            <th>Symbol</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                        <tr>
                            <td>{{ $unit->sort_order }}</td>
                            <td>{{ $unit->name }}</td>
                            <td>{{ $unit->symbol ?? '-' }}</td>
                            <td>{{ $unit->code ?? '-' }}</td>
                            <td>{{ $unit->description ? \Illuminate\Support\Str::limit($unit->description, 50) : '-' }}</td>
                            <td>
                                @if($unit->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('units.edit', $unit->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="delete({{ $unit->id }})" 
                                        wire:confirm="Are you sure you want to delete this unit?"
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No units found. <a href="{{ route('units.create') }}">Create one now</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $units->links() }}
            </div>
        </div>
    </div>
</div>
