@extends('layouts.app')
@section('title','Company Intelligence | AI Receptionist')
@section('page_title','Company Intelligence')
@section('content')
<div class="container-fluid">
<div class="d-flex justify-content-between align-items-center mb-4"><div><a href="{{ route('companies.show',$company) }}" class="text-muted text-decoration-none">← Back to Company</a><h2 class="fw-bold mt-3 mb-1">{{ $company->name }}</h2><p class="text-muted mb-0">Research profile and sales intelligence.</p></div>
<form method="POST" action="{{ route('research.run',$company) }}">@csrf<button class="btn btn-dark" {{ $company->website ? '' : 'disabled' }}>Research Website</button></form></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="row g-4"><div class="col-lg-5"><div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><strong>Research Status</strong></div><div class="card-body"><p class="mb-1">Status: <span class="badge text-bg-secondary">{{ ucfirst($company->intelligence?->research_status ?? 'pending') }}</span></p><p class="mb-0">Last researched: {{ $company->intelligence?->researched_at?->format('d M Y H:i') ?? 'Never' }}</p></div></div><div class="card border-0 shadow-sm"><div class="card-body"><h5 class="fw-bold">Website</h5>@if($company->website)<a href="{{ $company->website }}" target="_blank" rel="noopener">{{ $company->website }}</a>@else<p class="text-muted mb-0">No website recorded.</p>@endif</div></div></div>
<div class="col-lg-7"><form method="POST" action="{{ route('research.update',$company) }}">@csrf @method('PATCH')<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Intelligence Profile</strong></div><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Research Status</label><select name="research_status" class="form-select">@foreach(['pending','completed','failed'] as $status)<option value="{{ $status }}" @selected(($company->intelligence?->research_status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
@foreach([['research_summary','Research Summary'],['services_summary','Services'],['service_areas_summary','Service Areas'],['booking_process','Booking Process'],['sales_opportunities','Sales Opportunities'],['pain_points','Pain Points'],['strengths','Strengths'],['technology_notes','Technology Notes']] as [$field,$label])<div class="col-12"><label class="form-label">{{ $label }}</label><textarea name="{{ $field }}" class="form-control" rows="3">{{ old($field,$company->intelligence?->{$field}) }}</textarea></div>@endforeach
<div class="col-12"><button class="btn btn-dark">Save Intelligence</button></div></div></div></div></form></div></div>
</div>
@endsection
