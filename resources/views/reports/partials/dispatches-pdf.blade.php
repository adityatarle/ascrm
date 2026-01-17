@if(isset($data['total_dispatches']))
<div class="summary">
    <h3>Summary</h3>
    <div class="summary-row">
        <span><strong>Total Dispatches:</strong></span>
        <span>{{ number_format($data['total_dispatches']) }}</span>
    </div>
</div>
@endif

<table>
    <thead>
        <tr>
            <th>Dispatch #</th>
            <th>Order #</th>
            <th>Dealer</th>
            <th>LR Number</th>
            <th>Items</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['dispatches'] ?? [] as $dispatch)
        <tr>
            <td>#{{ $dispatch->id }}</td>
            <td>{{ $dispatch->order->order_number ?? 'N/A' }}</td>
            <td>{{ $dispatch->order->dealer->name ?? 'N/A' }}</td>
            <td>{{ $dispatch->lr_number ?? 'N/A' }}</td>
            <td>{{ $dispatch->items->sum('quantity') }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $dispatch->status)) }}</td>
            <td>{{ $dispatch->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">No dispatches found</td>
        </tr>
        @endforelse
    </tbody>
</table>

