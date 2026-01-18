<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Crop Master</h1>
                <p class="text-muted">Manage crop master data</p>
            </div>
            <a href="{{ route('crops.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Crop
            </a>
        </div>
    </div>

    @if(session('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Crops</h5>
            <div class="d-flex gap-2">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" wire:model.live="showInactive" id="showInactive">
                    <label class="form-check-label" for="showInactive">Show Inactive</label>
                </div>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search crops..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Unique ID</th>
                            <th>Sort Order</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($crops as $crop)
                        <tr>
                            <td>
                                @if($crop->image)
                                    <img src="{{ asset('storage/' . $crop->image) }}" alt="{{ $crop->name }}" class="img-thumbnail" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                @else
                                    <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <td><code>{{ $crop->unique_id }}</code></td>
                            <td>{{ $crop->sort_order }}</td>
                            <td>
                                {{ $crop->name }}
                                @php
                                    $productCount = $crop->products_count ?? $crop->products()->count();
                                @endphp
                                @if($productCount > 0)
                                    <span class="badge bg-info ms-2">{{ $productCount }} product{{ $productCount > 1 ? 's' : '' }}</span>
                                @endif
                            </td>
                            <td>{{ $crop->description ? \Illuminate\Support\Str::limit($crop->description, 50) : '-' }}</td>
                            <td>
                                @if($crop->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('crops.edit', $crop->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="delete({{ $crop->id }})" 
                                        wire:confirm="Are you sure you want to delete this crop?"
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No crops found. <a href="{{ route('crops.create') }}">Create one now</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $crops->links() }}
            </div>
        </div>
    </div>
</div>
