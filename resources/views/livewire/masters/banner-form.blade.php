<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $bannerId ? 'Edit' : 'Create' }} Banner</h1>
            <p class="text-muted">{{ $bannerId ? 'Update banner information' : 'Add a new banner' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" wire:model="title" class="form-control @error('title') is-invalid @enderror" placeholder="Enter banner title">
                        @error('title') <span class="text-danger">{{ $message }}</span> @enderror
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
                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter banner description"></textarea>
                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Image {{ $bannerId ? '' : '*' }}</label>
                        @if($existingImage)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $existingImage) }}" alt="Current banner" class="img-thumbnail" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                                <p class="text-muted small">Current banner image</p>
                            </div>
                        @endif
                        <input type="file" wire:model="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                        @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                        @if($image && !$image->getError())
                            <div class="mt-2">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                                <p class="text-muted small">Preview</p>
                            </div>
                        @endif
                        <small class="text-muted">Maximum file size: 5MB. Supported formats: JPG, PNG, GIF, WebP</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Link/URL (Optional)</label>
                        <input type="url" wire:model="link" class="form-control @error('link') is-invalid @enderror" placeholder="https://example.com">
                        @error('link') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">If provided, banner will be clickable and redirect to this URL</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date (Optional)</label>
                        <input type="date" wire:model="startDate" class="form-control @error('startDate') is-invalid @enderror">
                        @error('startDate') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">Banner will be active from this date</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date (Optional)</label>
                        <input type="date" wire:model="endDate" class="form-control @error('endDate') is-invalid @enderror">
                        @error('endDate') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">Banner will be active until this date</small>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('banners.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $bannerId ? 'Update' : 'Create' }} Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
