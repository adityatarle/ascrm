<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $slabId ? 'Edit' : 'Create' }} Discount Slab</h1>
            <p class="text-muted">{{ $slabId ? 'Update discount slab information' : 'Add a new discount slab based on order value' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Organization</label>
                        <select wire:model="organizationId" class="form-select @error('organizationId') is-invalid @enderror">
                            <option value="">-- Global (All Organizations) --</option>
                            @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                        @error('organizationId') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">Leave empty for global discount slab</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g., Standard Slab, Premium Slab">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="2"></textarea>
                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Minimum Amount (₹) *</label>
                        <input type="number" wire:model="minAmount" step="0.01" min="0" class="form-control @error('minAmount') is-invalid @enderror">
                        @error('minAmount') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Maximum Amount (₹)</label>
                        <input type="number" wire:model="maxAmount" step="0.01" min="0" class="form-control @error('maxAmount') is-invalid @enderror">
                        @error('maxAmount') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">Leave empty for no upper limit</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Discount Percentage (%) *</label>
                        <input type="number" wire:model="discountPercent" step="0.01" min="0" max="100" class="form-control @error('discountPercent') is-invalid @enderror">
                        @error('discountPercent') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" wire:model="isActive" class="form-check-input" id="isActive">
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('discount-slabs.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $slabId ? 'Update' : 'Create' }} Discount Slab
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
