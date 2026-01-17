<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Product State Rates: {{ $product->name }}</h1>
            <p class="text-muted">Manage state-specific pricing for this product</p>
        </div>
    </div>

    @if(session('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th>Product:</th>
                            <td>{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <th>Code:</th>
                            <td>{{ $product->code ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Base Price:</th>
                            <td>₹{{ number_format($product->base_price, 2) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Select Product Size/Variant</label>
                    <select wire:model.live="selectedSizeId" class="form-select">
                        <option value="">Base Product (Default)</option>
                        @foreach($productSizes as $size)
                        <option value="{{ $size->id }}">
                            @if($size->size_label)
                                {{ $size->size_label }}
                            @elseif($size->unit && $size->size_value)
                                {{ $size->size_value }}{{ $size->unit->symbol }}
                            @else
                                Size #{{ $size->id }}
                            @endif
                            - ₹{{ number_format($size->base_price, 2) }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Select a size to manage rates for that variant, or leave as "Base Product" for default rates</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-map-marker-alt me-2"></i>
                State-wise Rates {{ $selectedSizeId ? '(for selected size)' : '(Base Product)' }}
            </h5>
            <button wire:click="saveRates" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-2"></i>Save Rates
            </button>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Set different rates for different states. Leave blank to use base price.</p>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>State</th>
                            <th>Rate (₹)</th>
                            <th>Difference from Base</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($states as $state)
                        <tr>
                            <td>
                                <strong>{{ $state->name }}</strong>
                                @if($state->code)
                                <small class="text-muted">({{ $state->code }})</small>
                                @endif
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input 
                                        type="number" 
                                        wire:model="rates.{{ $state->id }}" 
                                        step="0.01" 
                                        min="0" 
                                        class="form-control"
                                        placeholder="{{ number_format($product->base_price, 2) }}"
                                    >
                                </div>
                            </td>
                            <td>
                                @if(isset($rates[$state->id]) && $rates[$state->id] > 0)
                                    @php
                                        $diff = $rates[$state->id] - $product->base_price;
                                        $diffPercent = $product->base_price > 0 ? ($diff / $product->base_price) * 100 : 0;
                                    @endphp
                                    @if($diff > 0)
                                        <span class="text-success">+₹{{ number_format($diff, 2) }} (+{{ number_format($diffPercent, 2) }}%)</span>
                                    @elseif($diff < 0)
                                        <span class="text-danger">₹{{ number_format($diff, 2) }} ({{ number_format($diffPercent, 2) }}%)</span>
                                    @else
                                        <span class="text-muted">No difference</span>
                                    @endif
                                @else
                                    <span class="text-muted">Using base price</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-2"></i>Edit Product
        </a>
    </div>
</div>
