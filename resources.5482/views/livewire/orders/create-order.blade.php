<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Create Order</h1>
            <p class="text-muted">Create a new order for a dealer</p>
        </div>
    </div>

    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('message'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Select Dealer</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Dealer <span class="text-danger">*</span></label>
                    <select wire:model.live="dealerId" class="form-select @error('dealerId') is-invalid @enderror">
                        <option value="">-- Select Dealer --</option>
                        @foreach($dealers as $dealer)
                        <option value="{{ $dealer->id }}">{{ $dealer->name }} - {{ $dealer->mobile }}</option>
                        @endforeach
                    </select>
                    @error('dealerId') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            @if($selectedDealer)
            <div class="mt-3 p-3 bg-light rounded">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Dealer:</strong> {{ $selectedDealer->name }}</p>
                        <p class="mb-1"><strong>Mobile:</strong> {{ $selectedDealer->mobile }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $selectedDealer->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>State:</strong> {{ $selectedDealer->state->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>City:</strong> {{ $selectedDealer->city->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Address:</strong> {{ $selectedDealer->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($selectedDealer)
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Add Products</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Product</label>
                            <select wire:model.live="selectedProductId" class="form-select">
                                <option value="">-- Select Product --</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->code ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                        </div>
                        @if($selectedProduct)
                        <div class="col-md-4">
                            <label class="form-label">Size/Variant</label>
                            <select wire:model="selectedSizeId" class="form-select">
                                <option value="">Base Product</option>
                                @foreach($selectedProduct->sizes->where('is_active', true) as $size)
                                <option value="{{ $size->id }}">
                                    {{ $size->size_label ?: ($size->size_value . ($size->unit ? $size->unit->symbol : '')) }}
                                    @if($size->base_price > 0)
                                    - ₹{{ number_format($size->base_price, 2) }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantity</label>
                            <input type="number" wire:model="quantity" min="1" class="form-control" value="1">
                        </div>
                        @endif
                    </div>

                    @if($selectedProduct)
                    <div class="mb-3">
                        <button wire:click="addToCart" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add to Cart
                        </button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cartItems as $index => $item)
                                <tr>
                                    <td>{{ $item['product_name'] }}</td>
                                    <td>{{ $item['size_label'] ?: 'Base' }}</td>
                                    <td class="text-end">₹{{ number_format($item['rate'], 2) }}</td>
                                    <td class="text-end">
                                        <input type="number" 
                                               wire:change="updateQuantity({{ $index }}, $event.target.value)" 
                                               value="{{ $item['quantity'] }}" 
                                               min="1" 
                                               class="form-control form-control-sm text-end" 
                                               style="width: 80px;">
                                    </td>
                                    <td class="text-end">₹{{ number_format($item['subtotal'], 2) }}</td>
                                    <td>
                                        <button wire:click="removeFromCart({{ $index }})" class="btn btn-sm btn-danger" title="Remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No items in cart. Select a product and add it to cart.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    @if(count($cartItems) > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>₹{{ number_format($this->subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount:</span>
                        <strong class="text-success">-₹{{ number_format($this->discountAmount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Taxable Amount:</span>
                        <strong>₹{{ number_format($this->taxableAmount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>GST (18%):</span>
                        <strong>₹{{ number_format($this->gstAmount, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Grand Total:</strong>
                        <strong class="text-primary fs-5">₹{{ number_format($this->grandTotal, 2) }}</strong>
                    </div>
                    <hr>
                    <button wire:click="saveOrder" class="btn btn-primary w-100" @if(empty($cartItems)) disabled @endif>
                        <i class="fas fa-check me-2"></i>Create Order
                    </button>
                    @else
                    <p class="text-muted text-center">Add products to see order summary</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>Please select a dealer to continue.
    </div>
    @endif
</div>
