<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">District Master</h1>
                <p class="text-muted">Manage districts</p>
            </div>
            <a href="{{ route('districts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New District
            </a>
        </div>
    </div>

    @if(session('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Districts</h5>
            <div class="d-flex gap-2">
                <select wire:model.live="stateFilter" class="form-select" style="width: 200px;">
                    <option value="">All States</option>
                    @foreach($states as $state)
                    <option value="{{ $state->fld_state_id }}">{{ $state->fld_name }}</option>
                    @endforeach
                </select>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search districts..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>State</th>
                            <th>Country ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($districts as $district)
                        <tr>
                            <td>{{ $district->fld_dist_id }}</td>
                            <td>{{ $district->fld_dist_name }}</td>
                            <td>{{ $district->state->fld_name ?? 'N/A' }}</td>
                            <td>{{ $district->fld_country_id }}</td>
                            <td>
                                <a href="{{ route('districts.edit', $district->fld_dist_id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button wire:click="delete({{ $district->fld_dist_id }})" 
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this district?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No districts found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $districts->links() }}
            </div>
        </div>
    </div>
</div>
