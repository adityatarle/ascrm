@if(isset($data['total_revenue']))
<div class="summary">
    <h3>Summary</h3>
    <div class="summary-row">
        <span><strong>Total Revenue:</strong></span>
        <span>₹{{ number_format($data['total_revenue'], 2) }}</span>
    </div>
    <div class="summary-row">
        <span><strong>Total Orders:</strong></span>
        <span>{{ number_format($data['total_orders']) }}</span>
    </div>
    <div class="summary-row">
        <span><strong>Average Order Value:</strong></span>
        <span>₹{{ number_format($data['average_order_value'], 2) }}</span>
    </div>
    <div class="summary-row">
        <span><strong>Total Discount:</strong></span>
        <span>₹{{ number_format($data['total_discount'], 2) }}</span>
    </div>
</div>
@endif

<table>
    <thead>
        <tr>
            <th>Order Number</th>
            <th>Dealer</th>
            <th class="text-right">Subtotal</th>
            <th class="text-right">Discount</th>
            <th class="text-right">GST</th>
            <th class="text-right">Total</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['orders'] ?? [] as $order)
        <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->dealer->name ?? 'N/A' }}</td>
            <td class="text-right">₹{{ number_format($order->subtotal, 2) }}</td>
            <td class="text-right">₹{{ number_format($order->discount_amount, 2) }}</td>
            <td class="text-right">₹{{ number_format($order->cgst_amount + $order->sgst_amount + $order->igst_amount, 2) }}</td>
            <td class="text-right"><strong>₹{{ number_format($order->grand_total, 2) }}</strong></td>
            <td>{{ ucfirst($order->status) }}</td>
            <td>{{ $order->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">No orders found</td>
        </tr>
        @endforelse
    </tbody>
</table>

