@if(isset($data['total_gst']))
<div class="summary">
    <h3>Summary</h3>
    <div class="summary-row">
        <span><strong>Total CGST:</strong></span>
        <span>₹{{ number_format($data['total_cgst'], 2) }}</span>
    </div>
    <div class="summary-row">
        <span><strong>Total SGST:</strong></span>
        <span>₹{{ number_format($data['total_sgst'], 2) }}</span>
    </div>
    <div class="summary-row">
        <span><strong>Total IGST:</strong></span>
        <span>₹{{ number_format($data['total_igst'], 2) }}</span>
    </div>
    <div class="summary-row">
        <span><strong>Total GST:</strong></span>
        <span>₹{{ number_format($data['total_gst'], 2) }}</span>
    </div>
</div>
@endif

<table>
    <thead>
        <tr>
            <th>State</th>
            <th class="text-right">CGST</th>
            <th class="text-right">SGST</th>
            <th class="text-right">IGST</th>
            <th class="text-right">Total GST</th>
            <th class="text-right">Orders</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['by_state'] ?? [] as $state => $stateData)
        <tr>
            <td><strong>{{ $state }}</strong></td>
            <td class="text-right">₹{{ number_format($stateData['cgst'], 2) }}</td>
            <td class="text-right">₹{{ number_format($stateData['sgst'], 2) }}</td>
            <td class="text-right">₹{{ number_format($stateData['igst'], 2) }}</td>
            <td class="text-right"><strong>₹{{ number_format($stateData['cgst'] + $stateData['sgst'] + $stateData['igst'], 2) }}</strong></td>
            <td class="text-right">{{ $stateData['count'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">No GST data found</td>
        </tr>
        @endforelse
    </tbody>
</table>

