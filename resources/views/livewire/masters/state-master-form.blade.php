<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $stateId ? 'Edit' : 'Create' }} State</h1>
            <p class="text-muted">{{ $stateId ? 'Update state information' : 'Add a new state master' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter state name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country ID *</label>
                        <input type="number" wire:model="countryId" class="form-control @error('countryId') is-invalid @enderror" placeholder="Enter country ID">
                        @error('countryId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ $stateId ? 'Update' : 'Create' }} State
                        </button>
                        <a href="{{ route('states.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
