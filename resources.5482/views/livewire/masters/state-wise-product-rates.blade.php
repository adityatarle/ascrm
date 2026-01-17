<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">
                <i class="fas fa-map-marker-alt me-2"></i>
                State-wise Product Rates
            </h1>
            <p class="text-muted">Select a state and manage product prices for that state</p>
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

    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label for="stateSelect" class="form-label">
                        <strong>Select State <span class="text-danger">*</span></strong>
                    </label>
                    <select 
                        wire:model.live="selectedStateId" 
                        id="stateSelect" 
                        class="form-select form-select-lg"
                    >
                        <option value="">-- Select State --</option>
                        @foreach($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }} @if($state->code)({{ $state->code }})@endif</option>
                        @endforeach
                    </select>
                </div>
                @if($selectedState)
                <div class="col-md-8">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Selected State:</strong> {{ $selectedState->name }}
                        <br>
                        <small>Enter state-specific prices below. Leave blank to use base price.</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($selectedStateId && $products->count() > 0)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-boxes me-2"></i>
                Products for {{ $selectedState->name }}
            </h5>
            <button wire:click="saveRates" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save All Rates
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 20%;">Product Name</th>
                            <th style="width: 10%;">Code</th>
                            <th style="width: 10%;">Unit</th>
                            <th style="width: 15%;">Base Price (₹)</th>
                            <th style="width: 15%;">State Price (₹)</th>
                            <th style="width: 10%;">Difference</th>
                            <th style="width: 15%;">Product Sizes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $index => $product)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->description)
                                <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $product->code ?? '-' }}</td>
                            <td>
                                @php
                                    $unitDisplay = '-';
                                    if ($product->unit_id && $product->relationLoaded('unit') && $product->unit && is_object($product->unit)) {
                                        $unitDisplay = $product->unit->symbol ?? $product->unit->name;
                                    } elseif (isset($product->unit) && is_string($product->unit)) {
                                        $unitDisplay = $product->unit;
                                    }
                                @endphp
                                @if($unitDisplay !== '-')
                                <span class="badge bg-secondary">{{ $unitDisplay }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <strong>₹{{ number_format($product->base_price, 2) }}</strong>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₹</span>
                                    <input 
                                        type="number" 
                                        wire:model="rates.{{ $product->id }}" 
                                        step="0.01" 
                                        min="0" 
                                        class="form-control text-end"
                                        placeholder="{{ number_format($product->base_price, 2) }}"
                                    >
                                </div>
                            </td>
                            <td class="text-end">
                                @php
                                    $stateRate = $rates[$product->id] ?? null;
                                    $basePrice = $product->base_price;
                                    $diff = $stateRate ? ($stateRate - $basePrice) : 0;
                                @endphp
                                @if($stateRate)
                                <span class="badge {{ $diff >= 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($product->sizes->count() > 0)
                                <div class="accordion accordion-flush" id="sizesAccordion{{ $product->id }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button 
                                                class="accordion-button collapsed" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#sizesCollapse{{ $product->id }}"
                                                style="font-size: 0.85rem; padding: 0.5rem;"
                                            >
                                                <small>{{ $product->sizes->count() }} Size(s)</small>
                                            </button>
                                        </h2>
                                        <div 
                                            id="sizesCollapse{{ $product->id }}" 
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#sizesAccordion{{ $product->id }}"
                                        >
                                            <div class="accordion-body p-2">
                                                @foreach($product->sizes as $size)
                                                <div class="mb-2">
                                                    <label class="form-label small mb-1">
                                                        {{ $size->display_label }}
                                                        <small class="text-muted">(Base: ₹{{ number_format($size->base_price, 2) }})</small>
                                                    </label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">₹</span>
                                                        <input 
                                                            type="number" 
                                                            wire:model="productSizes.{{ $product->id }}.{{ $size->id }}" 
                                                            step="0.01" 
                                                            min="0" 
                                                            class="form-control text-end"
                                                            placeholder="{{ number_format($size->base_price, 2) }}"
                                                        >
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span class="text-muted small">No sizes</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($products->count() === 0)
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No active products found for your organization.</p>
            </div>
            @endif
        </div>
    </div>
    @elseif($selectedStateId && $products->count() === 0)
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <p class="text-muted">No active products found for your organization.</p>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
            <p class="text-muted">Please select a state to view and manage product rates.</p>
        </div>
    </div>
    @endif
</div>

