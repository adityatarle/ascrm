<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Taluka Master</h1>
                <p class="text-muted">Manage talukas</p>
            </div>
            <a href="{{ route('talukas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Taluka
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
            <h5 class="mb-0">All Talukas</h5>
            <div class="d-flex gap-2">
                <select wire:model.live="stateFilter" class="form-select" style="width: 200px;">
                    <option value="">All States</option>
                    @foreach($states as $state)
                    <option value="{{ $state->fld_state_id }}">{{ $state->fld_name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="districtFilter" class="form-select" style="width: 200px;" {{ !$stateFilter ? 'disabled' : '' }}>
                    <option value="">All Districts</option>
                    @foreach($districts as $district)
                    <option value="{{ $district->fld_dist_id }}">{{ $district->fld_dist_name }}</option>
                    @endforeach
                </select>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search talukas..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>State</th>
                            <th>District</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($talukas as $taluka)
                        <tr>
                            <td>{{ $taluka->fld_taluka_id }}</td>
                            <td>{{ $taluka->fld_name }}</td>
                            <td>{{ $taluka->fld_code ?? 'N/A' }}</td>
                            <td>{{ $taluka->state->fld_name ?? 'N/A' }}</td>
                            <td>{{ $taluka->district->fld_dist_name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('talukas.edit', $taluka->fld_taluka_id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button wire:click="delete({{ $taluka->fld_taluka_id }})" 
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this taluka?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No talukas found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $talukas->links() }}
            </div>
        </div>
    </div>
</div>
