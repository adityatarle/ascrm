@props(['organization', 'title', 'dateFrom', 'dateTo'])

<div class="report-header print-only">
    <div class="row mb-3">
        <div class="col-12 text-center">
            <h2 class="mb-1">{{ $organization->name ?? 'Organization' }}</h2>
            @if($organization->gstin)
            <p class="mb-1"><strong>GSTIN:</strong> {{ $organization->gstin }}</p>
            @endif
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-6">
            @if($organization->address)
            <p class="mb-1"><strong>Address:</strong></p>
            <p class="mb-1">{{ $organization->address }}</p>
            @endif
            @if($organization->city)
            <p class="mb-1">{{ $organization->city->name ?? '' }}{{ $organization->state ? ', ' . $organization->state->name : '' }}</p>
            @endif
            @if($organization->pincode)
            <p class="mb-1">Pincode: {{ $organization->pincode }}</p>
            @endif
        </div>
        <div class="col-6 text-end">
            @if($organization->phone)
            <p class="mb-1"><strong>Phone:</strong> {{ $organization->phone }}</p>
            @endif
            @if($organization->email)
            <p class="mb-1"><strong>Email:</strong> {{ $organization->email }}</p>
            @endif
        </div>
    </div>
    
    <hr class="my-3">
    
    <div class="row mb-3">
        <div class="col-12 text-center">
            <h3 class="mb-2">{{ $title }}</h3>
            @if($dateFrom && $dateTo)
            <p class="mb-0">
                <strong>Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} 
                <strong>to</strong> {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            </p>
            @endif
            <p class="mb-0"><strong>Generated On:</strong> {{ now()->format('d/m/Y h:i A') }}</p>
        </div>
    </div>
    
    <hr class="my-3">
</div>

