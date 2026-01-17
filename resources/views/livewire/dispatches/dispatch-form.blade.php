<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $dispatchId ? 'Edit' : 'Create' }} Dispatch</h1>
            <p class="text-muted">{{ $dispatchId ? 'Update dispatch information' : 'Create a new dispatch for an order (partial dispatch supported)' }}</p>
        </div>
    </div>

    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order <span class="text-danger">*</span></label>
                        <select wire:model.live="orderId" 
                                class="form-select @error('orderId') is-invalid @enderror" 
                                @if($dispatchId) disabled @endif>
                            <option value="">-- Select Order --</option>
                            @foreach($orders as $order)
                            <option value="{{ $order->id }}">
                                {{ $order->order_number }} - {{ $order->dealer->name }} 
                                (₹{{ number_format($order->grand_total, 2) }})
                            </option>
                            @endforeach
                        </select>
                        @error('orderId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="pending">Pending</option>
                            <option value="dispatched">Dispatched</option>
                            <option value="in_transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                        </select>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if($selectedOrder)
                <div class="alert alert-info mb-3">
                    <h6 class="mb-2">Order Details:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Order Number:</strong> {{ $selectedOrder->order_number }}</p>
                            <p class="mb-1"><strong>Dealer:</strong> {{ $selectedOrder->dealer->name }}</p>
                            <p class="mb-1"><strong>Grand Total:</strong> ₹{{ number_format($selectedOrder->grand_total, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Status:</strong> {{ ucfirst($selectedOrder->status) }}</p>
                            <p class="mb-1"><strong>Items:</strong> {{ $selectedOrder->items->count() }} items</p>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Select Items to Dispatch (Partial Dispatch Supported)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th class="text-end">Ordered Qty</th>
                                        <th class="text-end">Dispatched</th>
                                        <th class="text-end">Remaining</th>
                                        <th class="text-end">Dispatch Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dispatchItems as $index => $item)
                                    @php
                                        $orderItem = $selectedOrder->items->find($item['order_item_id']);
                                        $dispatchedQty = $orderItem ? ($orderItem->dispatched_quantity ?? 0) : 0;
                                        $remainingQty = $orderItem ? ($orderItem->quantity - $dispatchedQty) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $item['product_name'] ?? ($orderItem ? $orderItem->product->name : '') }}</td>
                                        <td>{{ $item['size_label'] ?? 'Base' }}</td>
                                        <td class="text-end">{{ $orderItem ? $orderItem->quantity : 0 }}</td>
                                        <td class="text-end text-warning">{{ $dispatchedQty }}</td>
                                        <td class="text-end text-success"><strong>{{ $remainingQty }}</strong></td>
                                        <td class="text-end">
                                            @if($remainingQty > 0)
                                            <input type="number" 
                                                   wire:model.live="dispatchItems.{{ $index }}.quantity"
                                                   wire:change="updateDispatchItem({{ $index }}, 'quantity', $event.target.value)"
                                                   min="0" 
                                                   max="{{ $remainingQty }}"
                                                   class="form-control form-control-sm text-end" 
                                                   style="width: 100px;">
                                            @else
                                            <span class="text-muted">Fully Dispatched</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @error('dispatchItems') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">LR Number</label>
                        <input type="text" 
                               wire:model="lrNumber" 
                               class="form-control @error('lrNumber') is-invalid @enderror" 
                               placeholder="Enter LR Number">
                        @error('lrNumber') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Transporter Name</label>
                        <input type="text" 
                               wire:model="transporterName" 
                               class="form-control @error('transporterName') is-invalid @enderror" 
                               placeholder="Enter transporter name">
                        @error('transporterName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Number</label>
                        <input type="text" 
                               wire:model="vehicleNumber" 
                               class="form-control @error('vehicleNumber') is-invalid @enderror" 
                               placeholder="Enter vehicle number">
                        @error('vehicleNumber') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dispatched At</label>
                        <input type="datetime-local" 
                               wire:model="dispatchedAt" 
                               class="form-control @error('dispatchedAt') is-invalid @enderror">
                        @error('dispatchedAt') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('dispatches.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $dispatchId ? 'Update' : 'Create' }} Dispatch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
