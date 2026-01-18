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

                <div class="mb-3">
                    <label class="form-label">Contains Description</label>
                    <textarea wire:model="containsDescription" class="form-control @error('containsDescription') is-invalid @enderror" rows="3" placeholder="Enter what the product contains (e.g., ingredients, components, etc.)"></textarea>
                    @error('containsDescription') <span class="text-danger">{{ $message }}</span> @enderror
                    <small class="text-muted">Describe what the product contains (ingredients, components, etc.)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select wire:model="categoryId" class="form-select @error('categoryId') is-invalid @enderror">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <span class="text-danger">{{ $message }}</span> @enderror
                    <small class="text-muted">Select a category for this product</small>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Unit</label>
                        <select wire:model="unitId" class="form-select @error('unitId') is-invalid @enderror">
                            <option value="">-- Select Unit --</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                            @endforeach
                        </select>
                        @error('unitId') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">Or enter custom unit below</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Custom Unit (if not in list)</label>
                        <input type="text" wire:model="unit" class="form-control @error('unit') is-invalid @enderror" placeholder="e.g., Kg, Litre">
                        @error('unit') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Base Price *</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" wire:model="basePrice" step="0.01" min="0" class="form-control @error('basePrice') is-invalid @enderror">
                        </div>
                        @error('basePrice') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Unit Per Case</label>
                        <input type="number" wire:model="unitPerCase" step="0.01" min="0" class="form-control @error('unitPerCase') is-invalid @enderror" placeholder="e.g., 12, 24">
                        @error('unitPerCase') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">Number of units in one case/pack</small>
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

                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Product Sizes/Variants</h5>
                    <button type="button" wire:click="addSize" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>Add Size
                    </button>
                </div>
                <p class="text-muted small mb-3">Add different sizes/quantities for this product (e.g., 2kg, 3liter, 5kg)</p>

                @if(count($sizes) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Unit</th>
                                <th>Size Value</th>
                                <th>Size Label</th>
                                <th>Base Price</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sizes as $index => $size)
                            <tr>
                                <td>
                                    <select wire:model="sizes.{{ $index }}.unit_id" class="form-select form-select-sm">
                                        <option value="">-- Select --</option>
                                        @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" wire:model="sizes.{{ $index }}.size_value" step="0.001" min="0" class="form-control form-control-sm" placeholder="e.g., 2, 3, 5">
                                </td>
                                <td>
                                    <input type="text" wire:model="sizes.{{ $index }}.size_label" class="form-control form-control-sm" placeholder="e.g., 2kg, 3liter">
                                </td>
                                <td>
                                    <input type="number" wire:model="sizes.{{ $index }}.base_price" step="0.01" min="0" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" wire:model="sizes.{{ $index }}.is_active" class="form-check-input">
                                    </div>
                                </td>
                                <td>
                                    <button type="button" wire:click="removeSize({{ $index }})" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No product sizes added. Click "Add Size" to add variants.
                </div>
                @endif

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

