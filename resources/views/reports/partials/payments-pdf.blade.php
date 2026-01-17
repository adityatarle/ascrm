@if(isset($data['total_paid']))
<div class="summary">
    <h3>Summary</h3>
    <div class="summary-row">
        <span><strong>Total Paid:</strong></span>
        <span>₹{{ number_format($data['total_paid'], 2) }}</span>
    </div>
    <div class="summary-row">
        <span><strong>Pending Payments:</strong></span>
        <span>₹{{ number_format($data['total_pending'], 2) }}</span>
    </div>
</div>
@endif

<table>
    <thead>
        <tr>
            <th>Payment #</th>
            <th>Order #</th>
            <th>Dealer</th>
            <th class="text-right">Amount</th>
            <th>Mode</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['payments'] ?? [] as $payment)
        <tr>
            <td>#{{ $payment->id }}</td>
            <td>{{ $payment->order->order_number ?? 'N/A' }}</td>
            <td>{{ $payment->order->dealer->name ?? 'N/A' }}</td>
            <td class="text-right">₹{{ number_format($payment->amount, 2) }}</td>
            <td>{{ ucfirst($payment->payment_mode) }}</td>
            <td>{{ ucfirst($payment->status) }}</td>
            <td>{{ $payment->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">No payments found</td>
        </tr>
        @endforelse
    </tbody>
</table>

