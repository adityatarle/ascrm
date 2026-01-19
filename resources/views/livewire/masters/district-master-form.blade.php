<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $districtId ? 'Edit' : 'Create' }} District</h1>
            <p class="text-muted">{{ $districtId ? 'Update district information' : 'Add a new district master' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter district name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">State *</label>
                        <select wire:model="stateId" class="form-select @error('stateId') is-invalid @enderror">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                            <option value="{{ $state->fld_state_id }}">{{ $state->fld_name }}</option>
                            @endforeach
                        </select>
                        @error('stateId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country ID *</label>
                        <input type="number" wire:model="countryId" class="form-control @error('countryId') is-invalid @enderror" placeholder="Enter country ID">
                        @error('countryId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ $districtId ? 'Update' : 'Create' }} District
                        </button>
                        <a href="{{ route('districts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
