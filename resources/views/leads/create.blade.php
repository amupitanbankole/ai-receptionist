@extends('layouts.app')
@section('title','Add Lead | AI Receptionist')
@section('page_title','Add Lead')
@section('content')
<div class="container-fluid"><div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="fw-bold mb-1">Add Lead</h2><p class="text-muted mb-0">Create a sales opportunity for a prospect company.</p></div><a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">← Back to Leads</a></div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('leads.store') }}">@csrf<div class="row g-4"><div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-header bg-white border-0 py-3"><h5 class="fw-bold mb-0">Opportunity</h5></div><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Company *</label><select name="company_id" class="form-select" required><option value="">Select company</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected(old('company_id')==$company->id)>{{ $company->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Contact</label><select name="contact_id" class="form-select"><option value="">No contact selected</option>@foreach($contacts as $contact)<option value="{{ $contact->id }}" @selected(old('contact_id')==$contact->id)>{{ $contact->first_name }} {{ $contact->last_name }} — {{ $contact->company?->name }}</option>@endforeach</select></div>
<div class="col-12"><label class="form-label">Lead Title *</label><input name="title" class="form-control" value="{{ old('title') }}" placeholder="AI receptionist demo opportunity" required></div>
<div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['new','qualified','demo','customer','lost'] as $v)<option value="{{ $v }}" @selected(old('status','new')===$v)>{{ ucfirst($v) }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Score</label><input type="number" name="score" class="form-control" min="0" max="100" value="{{ old('score',0) }}"></div>
<div class="col-md-4"><label class="form-label">Temperature</label><select name="temperature" class="form-select">@foreach(['low','medium','high','hot'] as $v)<option value="{{ $v }}" @selected(old('temperature','low')===$v)>{{ ucfirst($v) }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Source</label><input name="source" class="form-control" value="{{ old('source') }}" placeholder="Website, referral, LinkedIn..."></div>
<div class="col-md-6"><label class="form-label">Next Follow-up</label><input type="datetime-local" name="next_follow_up_at" class="form-control" value="{{ old('next_follow_up_at') }}"></div>
<div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="5">{{ old('notes') }}</textarea></div>
</div></div></div></div><div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-body"><p class="small text-muted">Automated scoring will replace manual score management as company intelligence signals are connected.</p><button class="btn btn-primary w-100">Save Lead</button></div></div></div></div></form></div>
@endsection