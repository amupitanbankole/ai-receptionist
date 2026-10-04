@extends('layouts.app')
@section('title','Record Activity | AI Receptionist')
@section('page_title','Record Activity')
@section('content')
<div class="container-fluid"><div class="mb-4"><a href="{{ route('activities.index') }}" class="text-muted text-decoration-none">← Back to Activities</a><h2 class="fw-bold mt-3">Record Activity</h2></div>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<form method="POST" action="{{ route('activities.store') }}"><div class="card border-0 shadow-sm"><div class="card-body"><div class="row g-3">
@csrf
<div class="col-md-6"><label class="form-label">Lead</label><select name="lead_id" class="form-select"><option value="">None</option>@foreach($leads as $lead)<option value="{{ $lead->id }}">{{ $lead->title }} — {{ $lead->company->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Company</label><select name="company_id" class="form-select"><option value="">None</option>@foreach($companies as $company)<option value="{{ $company->id }}">{{ $company->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Type</label><select name="type" class="form-select">@foreach(['note','call','email','meeting','research','status_change'] as $type)<option value="{{ $type }}">{{ ucfirst(str_replace('_',' ',$type)) }}</option>@endforeach</select></div>
<div class="col-md-8"><label class="form-label">Subject *</label><input name="subject" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Occurred At</label><input type="datetime-local" name="occurred_at" class="form-control"></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5"></textarea></div>
<div class="col-12"><button class="btn btn-dark">Save Activity</button></div>
</div></div></div></form></div>
@endsection
