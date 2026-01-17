<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $productId ? 'Edit' : 'Create' }} Product</h1>
            <p class="text-muted">{{ $productId ? 'Update product information' : 'Add a new product to your catalog' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" wire:model="code" class="form-control @error('code') is-invalid @enderror" placeholder="e.g., PROD-001">
                        @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="3"></textarea>
                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Unit</label>
                        <input type="text" wire:model="unit" class="form-control @error('unit') is-invalid @enderror" placeholder="e.g., Kg, Litre">
                        @error('unit') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Base Price *</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" wire:model="basePrice" step="0.01" min="0" class="form-control @error('basePrice') is-invalid @enderror">
                        </div>
                        @error('basePrice') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check form-switch mt-4">
                            <input type="checkbox" wire:model="isActive" class="form-check-input" id="isActive">
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $productId ? 'Update' : 'Create' }} Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

