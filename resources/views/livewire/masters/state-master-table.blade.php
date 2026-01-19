<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">State Master</h1>
                <p class="text-muted">Manage states</p>
            </div>
            <a href="{{ route('states.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New State
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
            <h5 class="mb-0">All States</h5>
            <input type="text" wire:model.live="search" class="form-control" placeholder="Search states..." style="width: 250px;">
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Country ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($states as $state)
                        <tr>
                            <td>{{ $state->fld_state_id }}</td>
                            <td>{{ $state->fld_name }}</td>
                            <td>{{ $state->fld_country_id }}</td>
                            <td>
                                <a href="{{ route('states.edit', $state->fld_state_id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button wire:click="delete({{ $state->fld_state_id }})" 
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this state?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No states found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $states->links() }}
            </div>
        </div>
    </div>
</div>
