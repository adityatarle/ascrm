<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $categoryId ? 'Edit' : 'Create' }} Category</h1>
            <p class="text-muted">{{ $categoryId ? 'Update category information' : 'Add a new category master' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter category name">
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
                        <label class="form-label">Image {{ $categoryId ? '' : '*' }}</label>
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
                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter category description"></textarea>
                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $categoryId ? 'Update' : 'Create' }} Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
