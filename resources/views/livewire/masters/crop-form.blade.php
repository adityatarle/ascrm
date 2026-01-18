<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $cropId ? 'Edit' : 'Create' }} Crop</h1>
            <p class="text-muted">{{ $cropId ? 'Update crop information' : 'Add a new crop master' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                @if($cropId && $uniqueId)
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Unique ID</label>
                        <input type="text" value="{{ $uniqueId }}" class="form-control" readonly>
                        <small class="text-muted">Auto-generated unique identifier</small>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter crop name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Image {{ $cropId ? '' : '*' }}</label>
                        @if($existingImage)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $existingImage) }}" alt="Current image" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                <p class="text-muted small">Current image</p>
                            </div>
                        @endif
                        <input type="file" wire:model="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                        @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                        @if($image && !$image->getError())
                            <div class="mt-2">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                <p class="text-muted small">Preview</p>
                            </div>
                        @endif
                        <small class="text-muted">Maximum file size: 2MB. Supported formats: JPG, PNG, GIF</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter crop description"></textarea>
                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Assign Products</label>
                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                        @if(count($availableProducts) > 0)
                            @foreach($availableProducts as $product)
                                <div class="form-check mb-2">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        wire:model="selectedProducts" 
                                        value="{{ $product->id }}" 
                                        id="product_{{ $product->id }}">
                                    <label class="form-check-label" for="product_{{ $product->id }}">
                                        {{ $product->name }} 
                                        @if($product->code)
                                            <small class="text-muted">({{ $product->code }})</small>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted mb-0">No active products available. Please create products first.</p>
                        @endif
                    </div>
                    <small class="text-muted">Select products to assign to this crop. These will be visible in the mobile app under this crop.</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('crops.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $cropId ? 'Update' : 'Create' }} Crop
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
