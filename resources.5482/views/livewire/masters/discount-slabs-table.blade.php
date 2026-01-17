<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Discount Slabs</h1>
                <p class="text-muted">Manage slabwise discounts based on order value</p>
            </div>
            <a href="{{ route('discount-slabs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Discount Slab
            </a>
        </div>
    </div>

    @if(session('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Discount Slabs</h5>
            <div class="d-flex gap-2">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" wire:model.live="showInactive" id="showInactive">
                    <label class="form-check-label" for="showInactive">Show Inactive</label>
                </div>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Organization</th>
                            <th>Name</th>
                            <th>Min Amount (₹)</th>
                            <th>Max Amount (₹)</th>
                            <th>Discount %</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($slabs as $slab)
                        <tr>
                            <td>{{ $slab->organization ? $slab->organization->name : 'Global' }}</td>
                            <td>{{ $slab->name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($slab->min_amount, 2) }}</td>
                            <td class="text-end">{{ $slab->max_amount ? number_format($slab->max_amount, 2) : 'No Limit' }}</td>
                            <td class="text-end">{{ number_format($slab->discount_percent, 2) }}%</td>
                            <td>
                                @if($slab->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('discount-slabs.edit', $slab->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="delete({{ $slab->id }})" 
                                        wire:confirm="Are you sure you want to delete this discount slab?"
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No discount slabs found. <a href="{{ route('discount-slabs.create') }}">Create one now</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $slabs->links() }}
            </div>
        </div>
    </div>
</div>
