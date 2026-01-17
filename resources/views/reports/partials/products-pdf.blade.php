<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th class="text-right">Quantity</th>
            <th class="text-right">Revenue</th>
            <th>Orders</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['top_products'] ?? [] as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item['product']->name ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($item['total_quantity']) }}</td>
            <td class="text-right">₹{{ number_format($item['total_revenue'], 2) }}</td>
            <td>{{ $item['order_count'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No products found</td>
        </tr>
        @endforelse
    </tbody>
</table>

