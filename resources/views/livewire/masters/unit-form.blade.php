<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $unitId ? 'Edit' : 'Create' }} Unit</h1>
            <p class="text-muted">{{ $unitId ? 'Update unit information' : 'Add a new unit of measurement' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g., Kilogram, Liter, Piece">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Symbol</label>
                        <input type="text" wire:model="symbol" class="form-control @error('symbol') is-invalid @enderror" placeholder="e.g., kg, L, pcs" maxlength="10">
                        @error('symbol') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" wire:model="code" class="form-control @error('code') is-invalid @enderror" placeholder="e.g., KG, LTR, PCS" maxlength="20">
                        @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">Unique code for the unit</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" wire:model="sortOrder" min="0" class="form-control @error('sortOrder') is-invalid @enderror">
                        @error('sortOrder') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-check form-switch mt-4">
                            <input type="checkbox" wire:model="isActive" class="form-check-input" id="isActive">
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="3"></textarea>
                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('units.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $unitId ? 'Update' : 'Create' }} Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
