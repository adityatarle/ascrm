<div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Create Order</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Select Dealer</label>
                    <select wire:model.live="dealerId" class="form-select">
                        <option value="">-- Select Dealer --</option>
                        @foreach($dealers as $dealer)
                        <option value="{{ $dealer->id }}">{{ $dealer->name }} - {{ $dealer->mobile }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($selectedDealer)
            <div class="mt-3">
                <p><strong>Dealer:</strong> {{ $selectedDealer->name }}</p>
                <p><strong>State:</strong> {{ $selectedDealer->state->name ?? 'N/A' }}</p>
            </div>
            @endif

            <div class="row mt-4">
                <div class="col-md-8">
                    <h6>Products</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Unit</th>
                                    <th class="text-number">Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->unit }}</td>
                                    <td class="text-number">₹{{ number_format($product->base_price, 2) }}</td>
                                    <td>
                                        <button wire:click="addToCart({{ $product->id }})" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6>Cart</h6>
                    <div class="card">
                        <div class="card-body">
                            @forelse($cartItems as $index => $item)
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <small>{{ $item['product_name'] }}</small><br>
                                    <small>Qty: {{ $item['quantity'] }} × ₹{{ number_format($item['rate'], 2) }}</small>
                                </div>
                                <div>
                                    <button wire:click="removeFromCart({{ $index }})" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">Cart is empty</p>
                            @endforelse
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Subtotal:</strong>
                                <strong>₹{{ number_format($this->subtotal, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

