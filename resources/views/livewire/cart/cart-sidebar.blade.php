<div>
    <button wire:click="toggle" class="btn btn-primary position-fixed bottom-0 end-0 m-3" style="z-index: 1000;">
        <i class="fas fa-shopping-cart"></i>
        <span class="badge bg-danger">{{ count($items) }}</span>
    </button>

    @if($isOpen)
    <div class="position-fixed top-0 end-0 h-100 bg-white shadow-lg p-3" style="width: 350px; z-index: 1050; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Cart</h5>
            <button wire:click="toggle" class="btn btn-sm btn-link">
                <i class="fas fa-times"></i>
            </button>
        </div>

        @forelse($items as $item)
        <div class="card mb-2">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>{{ $item['product']['name'] ?? 'Product' }}</strong><br>
                        <small>₹{{ number_format($item['product']['base_price'] ?? 0, 2) }} × {{ $item['quantity'] }}</small>
                    </div>
                    <button wire:click="removeItem({{ $item['id'] }})" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <p class="text-muted">Cart is empty</p>
        @endforelse

        @if(count($items) > 0)
        <div class="mt-3">
            <div class="d-flex justify-content-between">
                <strong>Total:</strong>
                <strong>₹{{ number_format($this->total, 2) }}</strong>
            </div>
            <button class="btn btn-primary w-100 mt-2">Checkout</button>
        </div>
        @endif
    </div>
    @endif
</div>

