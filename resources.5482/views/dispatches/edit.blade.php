@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Edit Dispatch</h1>
            <p class="text-muted">Dispatch #{{ $dispatch->dispatch_number }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-muted">Dispatch editing functionality will be implemented here.</p>
            <p class="text-muted">For now, dispatches can only be viewed. Edit functionality can be added based on business requirements.</p>
            <a href="{{ route('dispatches.show', $dispatch->id) }}" class="btn btn-primary">View Dispatch</a>
            <a href="{{ route('dispatches.index') }}" class="btn btn-secondary">Back to Dispatches</a>
        </div>
    </div>
</div>
@endsection

