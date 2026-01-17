@if(isset($data['total_orders']))
<div class="summary">
    <h3>Summary</h3>
    <div class="summary-row">
        <span><strong>Total Orders:</strong></span>
        <span>{{ number_format($data['total_orders']) }}</span>
    </div>
</div>
@endif

<table>
    <thead>
        <tr>
            <th>Order Number</th>
            <th>Dealer</th>
            <th class="text-right">Amount</th>
            <th>Status</th>
            <th>Items</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['orders'] ?? [] as $order)
        <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->dealer->name ?? 'N/A' }}</td>
            <td class="text-right">₹{{ number_format($order->grand_total, 2) }}</td>
            <td>{{ ucfirst($order->status) }}</td>
            <td>{{ $order->items->count() }}</td>
            <td>{{ $order->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">No orders found</td>
        </tr>
        @endforelse
    </tbody>
</table>

