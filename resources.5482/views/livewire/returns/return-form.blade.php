<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $returnId ? 'Edit' : 'Create' }} Return</h1>
            <p class="text-muted">{{ $returnId ? 'Update return information' : 'Create a return for an order' }}</p>
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
                                @if($returnId) disabled @endif>
                            <option value="">-- Select Order --</option>
                            @foreach($orders as $order)
                            <option value="{{ $order->id }}">
                                {{ $order->order_number }} - {{ $order->dealer->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('orderId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="processed">Processed</option>
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
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Status:</strong> {{ ucfirst($selectedOrder->status) }}</p>
                            <p class="mb-1"><strong>Items:</strong> {{ $selectedOrder->items->count() }} items</p>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Select Items to Return</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th class="text-end">Ordered Qty</th>
                                        <th class="text-end">Return Qty</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($returnItems as $index => $item)
                                    @php
                                        $orderItem = $selectedOrder->items->find($item['order_item_id']);
                                    @endphp
                                    <tr>
                                        <td>{{ $orderItem ? $orderItem->product->name : '' }}</td>
                                        <td>
                                            @if($orderItem && $orderItem->productSize)
                                                {{ $orderItem->productSize->size_label ?: ($orderItem->productSize->size_value . ($orderItem->productSize->unit ? $orderItem->productSize->unit->symbol : '')) }}
                                            @else
                                                Base
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $orderItem ? $orderItem->quantity : 0 }}</td>
                                        <td class="text-end">
                                            <input type="number" 
                                                   wire:model.live="returnItems.{{ $index }}.quantity"
                                                   wire:change="updateReturnItem({{ $index }}, 'quantity', $event.target.value)"
                                                   min="0" 
                                                   max="{{ $item['max_quantity'] ?? 0 }}"
                                                   class="form-control form-control-sm text-end" 
                                                   style="width: 100px;">
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   wire:model="returnItems.{{ $index }}.reason"
                                                   class="form-control form-control-sm" 
                                                   placeholder="Return reason">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @error('returnItems') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Return Reason</label>
                    <textarea wire:model="reason" 
                              class="form-control @error('reason') is-invalid @enderror" 
                              rows="3" 
                              placeholder="General reason for return"></textarea>
                    @error('reason') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Returned At</label>
                    <input type="datetime-local" 
                           wire:model="returnedAt" 
                           class="form-control @error('returnedAt') is-invalid @enderror">
                    @error('returnedAt') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $returnId ? 'Update' : 'Create' }} Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
