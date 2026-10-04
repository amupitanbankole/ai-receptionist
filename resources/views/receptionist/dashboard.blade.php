@extends('layouts.app')
@section('title','AI Receptionist')
@section('page_title','AI Receptionist')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h2 class="mb-1">AI Receptionist</h2><p class="text-muted mb-0">{{ $company?->name ?: 'No company configured' }}</p></div>
    @if($company)<a class="btn btn-primary" href="{{ route('receptionist.edit',$company) }}">Configure receptionist</a>@endif
</div>
@if(!$company)<div class="alert alert-warning">Create a company first.</div>@else
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Conversations today</div><div class="fs-2 fw-bold">{{ $today }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">CRM leads</div><div class="fs-2 fw-bold">{{ $leads }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Upcoming appointments</div><div class="fs-2 fw-bold">{{ $appointments->count() }}</div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Status</div><div class="fs-4 fw-bold">{{ $company->receptionistConfig?->enabled ? 'Enabled' : 'Disabled' }}</div></div></div></div>
</div>
<div class="row g-4">
<div class="col-lg-7"><div class="card"><div class="card-header fw-bold">Upcoming appointments</div><div class="card-body p-0"><table class="table mb-0"><thead><tr><th>Customer</th><th>When</th><th>Status</th></tr></thead><tbody>@forelse($appointments as $a)<tr><td>{{ $a->customer_name }}</td><td>{{ $a->starts_at->format('D j M Y H:i') }}</td><td>{{ ucfirst($a->status) }}</td></tr>@empty<tr><td colspan="3" class="text-muted">No upcoming appointments.</td></tr>@endforelse</tbody></table></div></div></div>
<div class="col-lg-5"><div class="card"><div class="card-header fw-bold">Test receptionist</div><div class="card-body"><p class="text-muted">Run a simulated customer conversation before connecting real web chat or phone.</p><a href="{{ route('receptionist.simulate',['company_id'=>$company->id]) }}" class="btn btn-outline-primary">Simulate customer</a><a href="{{ route('appointments.create',['company_id'=>$company->id]) }}" class="btn btn-outline-secondary ms-2">Book appointment</a></div></div></div>
</div>
@endif
@endsection