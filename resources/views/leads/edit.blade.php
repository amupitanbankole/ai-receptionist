@extends('layouts.app')
@section('title','Edit Lead | AI Receptionist')
@section('page_title','Edit Lead')
@section('content')
<div class="container-fluid"><div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="fw-bold mb-1">Edit Lead</h2><p class="text-muted mb-0">Update sales opportunity details.</p></div><a href="{{ route('leads.show',$lead) }}" class="btn btn-outline-secondary">← Back to Lead</a></div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('leads.update',$lead) }}">@csrf @method('PUT')<div class="row g-4"><div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Company</label><select name="company_id" class="form-select">@foreach($companies as $company)<option value="{{ $company->id }}" @selected(old('company_id',$lead->company_id)==$company->id)>{{ $company->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Contact</label><select name="contact_id" class="form-select"><option value="">No contact</option>@foreach($contacts as $contact)<option value="{{ $contact->id }}" @selected(old('contact_id',$lead->contact_id)==$contact->id)>{{ $contact->first_name }} {{ $contact->last_name }} — {{ $contact->company?->name }}</option>@endforeach</select></div>
<div class="col-12"><label class="form-label">Lead Title</label><input name="title" class="form-control" value="{{ old('title',$lead->title) }}" required></div>
<div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['new','qualified','demo','customer','lost'] as $v)<option value="{{ $v }}" @selected(old('status',$lead->status)===$v)>{{ ucfirst($v) }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Score</label><input type="number" name="score" class="form-control" min="0" max="100" value="{{ old('score',$lead->score) }}" required></div>
<div class="col-md-4"><label class="form-label">Temperature</label><select name="temperature" class="form-select">@foreach(['low','medium','high','hot'] as $v)<option value="{{ $v }}" @selected(old('temperature',$lead->temperature)===$v)>{{ ucfirst($v) }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Lead Source</label><select name="lead_source_id" class="form-select"><option value="">Select source</option>@foreach($leadSources as $leadSource)<option value="{{ $leadSource->id }}" @selected(old('lead_source_id',$lead->lead_source_id)==$leadSource->id)>{{ $leadSource->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Source Detail</label><input name="source" class="form-control" value="{{ old('source',$lead->source) }}"></div>
<div class="col-md-6"><label class="form-label">Next Follow-up</label><input type="datetime-local" name="next_follow_up_at" class="form-control" value="{{ old('next_follow_up_at',$lead->next_follow_up_at?->format('Y-m-d\TH:i')) }}"></div>
<div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="5">{{ old('notes',$lead->notes) }}</textarea></div>
</div></div></div></div><div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-body"><p class="small text-muted">Lead Source is the structured acquisition channel. Source Detail is optional context.</p><button class="btn btn-primary w-100">Update Lead</button></div></div></div></div></form></div>
@endsection
