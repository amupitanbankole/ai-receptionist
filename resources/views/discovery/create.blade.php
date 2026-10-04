@extends('layouts.app')
@section('title','Add Prospect | AI Receptionist')
@section('page_title','Add Prospect')
@section('content')
<div class="container-fluid"><div class="mb-4"><a href="{{ route('discovery.index') }}" class="text-muted text-decoration-none">← Back to Discovery</a><h2 class="fw-bold mt-3">Add Prospect</h2><p class="text-muted">Capture a prospect discovered through search, directories, LinkedIn or another source.</p></div>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<form method="POST" action="{{ route('discovery.store') }}">@csrf<div class="row g-4"><div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-body"><div class="row g-3">
<div class="col-md-8"><label class="form-label">Company Name *</label><input name="company_name" class="form-control" required value="{{ old('company_name') }}"></div>
<div class="col-md-4"><label class="form-label">Industry</label><input name="industry" class="form-control" value="{{ old('industry') }}"></div>
<div class="col-md-6"><label class="form-label">Website</label><input type="url" name="website" class="form-control" value="{{ old('website') }}"></div>
<div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone') }}"></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
<div class="col-md-6"><label class="form-label">City</label><input name="city" class="form-control" value="{{ old('city') }}"></div>
<div class="col-md-6"><label class="form-label">County</label><input name="county" class="form-control" value="{{ old('county') }}"></div>
<div class="col-md-6"><label class="form-label">Postcode</label><input name="postcode" class="form-control" value="{{ old('postcode') }}"></div>
<div class="col-12"><label class="form-label">Discovery Notes</label><textarea name="discovery_notes" class="form-control" rows="4">{{ old('discovery_notes') }}</textarea></div>
</div></div></div></div><div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-body">
<div class="mb-3"><label class="form-label">Source *</label><select name="source" class="form-select">@foreach(['Google Search','Google Maps','LinkedIn','Business Directory','Website Research','Referral','Manual Research','Import','Other'] as $source)<option value="{{ $source }}">{{ $source }}</option>@endforeach</select></div>
<div class="mb-3"><label class="form-label">Source URL</label><input type="url" name="source_url" class="form-control" value="{{ old('source_url') }}"></div>
<div class="mb-3"><label class="form-label">Country</label><input name="country" class="form-control" value="{{ old('country','United Kingdom') }}"></div>
<button class="btn btn-dark w-100">Add to Discovery Queue</button>
</div></div></div></div></form></div>
@endsection
