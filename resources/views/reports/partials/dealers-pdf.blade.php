<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Dealer</th>
            <th class="text-right">Orders</th>
            <th class="text-right">Revenue</th>
            <th class="text-right">Avg Order Value</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['top_dealers'] ?? [] as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item['dealer']->name ?? 'N/A' }}</td>
            <td class="text-right">{{ $item['order_count'] }}</td>
            <td class="text-right">₹{{ number_format($item['total_revenue'], 2) }}</td>
            <td class="text-right">₹{{ number_format($item['average_order_value'], 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No dealers found</td>
        </tr>
        @endforelse
    </tbody>
</table>

