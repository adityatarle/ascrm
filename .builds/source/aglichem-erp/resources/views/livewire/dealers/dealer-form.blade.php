<div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $dealerId ? 'Edit' : 'Create' }} Dealer</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model="name" class="form-control">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile *</label>
                        <input type="text" wire:model="mobile" class="form-control">
                        @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" wire:model="email" class="form-control">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">GSTIN</label>
                        <input type="text" wire:model="gstin" class="form-control">
                        @error('gstin') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea wire:model="address" class="form-control" rows="2"></textarea>
                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State *</label>
                        <select wire:model.live="stateId" class="form-select">
                            <option value="">-- Select State --</option>
                            @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @error('stateId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City *</label>
                        <select wire:model.live="cityId" class="form-select">
                            <option value="">-- Select City --</option>
                            @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('cityId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Zone</label>
                        <select wire:model="zoneId" class="form-select">
                            <option value="">-- Select Zone --</option>
                            @foreach($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                        @error('zoneId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pincode</label>
                        <input type="text" wire:model="pincode" class="form-control">
                        @error('pincode') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input type="checkbox" wire:model="isActive" class="form-check-input" id="isActive">
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('dealers.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

