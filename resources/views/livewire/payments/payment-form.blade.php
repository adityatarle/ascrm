<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $paymentId ? 'Edit' : 'Record' }} Payment</h1>
            <p class="text-muted">{{ $paymentId ? 'Update payment information' : 'Record a payment for an order' }}</p>
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
                                @if($paymentId) disabled @endif>
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
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if($selectedOrder)
                <div class="alert alert-info mb-3">
                    <h6 class="mb-2">Order Payment Summary:</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Order Total:</strong> ₹{{ number_format($selectedOrder->grand_total, 2) }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Paid Amount:</strong> ₹{{ number_format($selectedOrder->paid_amount, 2) }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Remaining:</strong> <span class="text-{{ $selectedOrder->remaining_amount > 0 ? 'danger' : 'success' }}">₹{{ number_format($selectedOrder->remaining_amount, 2) }}</span></p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <input type="number" 
                               wire:model="amount" 
                               step="0.01"
                               min="0.01"
                               class="form-control @error('amount') is-invalid @enderror" 
                               placeholder="Enter payment amount">
                        @error('amount') <span class="text-danger">{{ $message }}</span> @enderror
                        @if($selectedOrder)
                        <small class="text-muted">Maximum: ₹{{ number_format($selectedOrder->remaining_amount, 2) }}</small>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select wire:model.live="paymentMethod" class="form-select @error('paymentMethod') is-invalid @enderror">
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="neft">NEFT</option>
                            <option value="rtgs">RTGS</option>
                            <option value="upi">UPI</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                        </select>
                        @error('paymentMethod') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if(in_array($paymentMethod, ['cheque']))
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cheque Number</label>
                        <input type="text" 
                               wire:model="chequeNumber" 
                               class="form-control @error('chequeNumber') is-invalid @enderror" 
                               placeholder="Enter cheque number">
                        @error('chequeNumber') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" 
                               wire:model="bankName" 
                               class="form-control @error('bankName') is-invalid @enderror" 
                               placeholder="Enter bank name">
                        @error('bankName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif

                @if(in_array($paymentMethod, ['neft', 'rtgs', 'upi', 'bank_transfer']))
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Transaction/Reference Number</label>
                        <input type="text" 
                               wire:model="transactionId" 
                               class="form-control @error('transactionId') is-invalid @enderror" 
                               placeholder="Enter transaction/reference number">
                        @error('transactionId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" 
                               wire:model="bankName" 
                               class="form-control @error('bankName') is-invalid @enderror" 
                               placeholder="Enter bank name">
                        @error('bankName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" 
                               wire:model="referenceNumber" 
                               class="form-control @error('referenceNumber') is-invalid @enderror" 
                               placeholder="Optional reference number">
                        @error('referenceNumber') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="datetime-local" 
                               wire:model="paidAt" 
                               class="form-control @error('paidAt') is-invalid @enderror">
                        @error('paidAt') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea wire:model="notes" 
                              class="form-control @error('notes') is-invalid @enderror" 
                              rows="3" 
                              placeholder="Additional notes about this payment"></textarea>
                    @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ $paymentId ? route('orders.show', $orderId) : route('orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $paymentId ? 'Update' : 'Record' }} Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
