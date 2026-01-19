<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $talukaId ? 'Edit' : 'Create' }} Taluka</h1>
            <p class="text-muted">{{ $talukaId ? 'Update taluka information' : 'Add a new taluka master' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter taluka name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" wire:model="code" class="form-control @error('code') is-invalid @enderror" placeholder="Enter taluka code" maxlength="5">
                        @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">State *</label>
                        <select wire:model.live="stateId" class="form-select @error('stateId') is-invalid @enderror">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                            <option value="{{ $state->fld_state_id }}">{{ $state->fld_name }}</option>
                            @endforeach
                        </select>
                        @error('stateId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">District *</label>
                        <select wire:model="districtId" class="form-select @error('districtId') is-invalid @enderror" {{ !$stateId ? 'disabled' : '' }}>
                            <option value="">Select District</option>
                            @foreach($districts as $district)
                            <option value="{{ $district->fld_dist_id }}">{{ $district->fld_dist_name }}</option>
                            @endforeach
                        </select>
                        @error('districtId') <span class="text-danger">{{ $message }}</span> @enderror
                        @if(!$stateId)
                        <small class="text-muted">Please select a state first</small>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country ID *</label>
                        <input type="number" wire:model="countryId" class="form-control @error('countryId') is-invalid @enderror" placeholder="Enter country ID">
                        @error('countryId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sequence</label>
                        <input type="number" wire:model="sequence" class="form-control @error('sequence') is-invalid @enderror" placeholder="Enter sequence">
                        @error('sequence') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ $talukaId ? 'Update' : 'Create' }} Taluka
                        </button>
                        <a href="{{ route('talukas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
