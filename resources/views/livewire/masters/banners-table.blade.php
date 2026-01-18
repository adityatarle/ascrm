<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Banner Master</h1>
                <p class="text-muted">Manage dynamic banners for mobile app</p>
            </div>
            <a href="{{ route('banners.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Banner
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
            <h5 class="mb-0">All Banners</h5>
            <div class="d-flex gap-2">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" wire:model.live="showInactive" id="showInactive">
                    <label class="form-check-label" for="showInactive">Show Inactive</label>
                </div>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search banners..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Link</th>
                            <th>Sort Order</th>
                            <th>Date Range</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                        <tr>
                            <td>
                                @if($banner->image)
                                    <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title ?? 'Banner' }}" class="img-thumbnail" style="max-width: 150px; max-height: 80px; object-fit: cover;">
                                @else
                                    <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $banner->title ?? 'Untitled' }}</strong>
                                @if($banner->description)
                                    <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($banner->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" class="text-primary">
                                        <i class="fas fa-external-link-alt"></i> Link
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $banner->sort_order }}</td>
                            <td>
                                @if($banner->start_date || $banner->end_date)
                                    <small>
                                        @if($banner->start_date)
                                            From: {{ $banner->start_date->format('M d, Y') }}<br>
                                        @endif
                                        @if($banner->end_date)
                                            To: {{ $banner->end_date->format('M d, Y') }}
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">Always active</span>
                                @endif
                            </td>
                            <td>
                                @if($banner->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('banners.edit', $banner->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="delete({{ $banner->id }})" 
                                        wire:confirm="Are you sure you want to delete this banner?"
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No banners found. <a href="{{ route('banners.create') }}">Create one now</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $banners->links() }}
            </div>
        </div>
    </div>
</div>
