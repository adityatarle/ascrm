<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Products</h1>
                <p class="text-muted">Manage your product catalog</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Products</h5>
            <div class="d-flex gap-2">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" wire:model.live="showInactive" id="showInactive">
                    <label class="form-check-label" for="showInactive">Show Inactive</label>
                </div>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search products..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Unit</th>
                            <th class="text-number">Base Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->unit }}</td>
                            <td class="text-number">₹{{ number_format($product->base_price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @role('admin')
                                <button wire:click="delete({{ $product->id }})" 
                                        wire:confirm="Are you sure you want to delete this product?"
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endrole
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No products found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

