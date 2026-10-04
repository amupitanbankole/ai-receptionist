@extends('layouts.app')
@section('title','Schedule Follow-up | AI Receptionist')
@section('page_title','Schedule Follow-up')
@section('content')
<div class="container-fluid"><div class="mb-4"><a href="{{ route('followups.index') }}" class="text-muted text-decoration-none">← Back to Follow-ups</a><h2 class="fw-bold mt-3">Schedule Follow-up</h2></div>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<form method="POST" action="{{ route('followups.store') }}"><div class="card border-0 shadow-sm"><div class="card-body"><div class="row g-3">@csrf
<div class="col-12"><label class="form-label">Lead *</label><select name="lead_id" class="form-select" required><option value="">Select lead</option>@foreach($leads as $lead)<option value="{{ $lead->id }}">{{ $lead->title }} — {{ $lead->company->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Type</label><select name="type" class="form-select">@foreach(['manual','email','call','whatsapp','task'] as $type)<option value="{{ $type }}">{{ ucfirst($type) }}</option>@endforeach</select></div>
<div class="col-md-8"><label class="form-label">Subject *</label><input name="subject" class="form-control" required placeholder="Call prospect about AI receptionist demo"></div>
<div class="col-md-6"><label class="form-label">Due At *</label><input type="datetime-local" name="due_at" class="form-control" required></div>
<div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="5"></textarea></div>
<div class="col-12"><button class="btn btn-dark">Schedule Follow-up</button></div>
</div></div></div></form></div>
@endsection
