@extends('layouts.app')
@section('title','Lead Scoring | AI Receptionist')
@section('page_title','Lead Scoring')
@section('content')
<div class="container-fluid">
<div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="fw-bold mb-1">Lead Scoring</h2><p class="text-muted mb-0">Recalculate prospect scores from company intelligence signals.</p></div><form method="POST" action="{{ route('lead-scoring.run') }}">@csrf<button class="btn btn-dark">Recalculate All</button></form></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="px-4">Lead</th><th>Company</th><th>Score</th><th>Temperature</th><th>Status</th></tr></thead><tbody>
@forelse($leads as $lead)<tr><td class="px-4"><a href="{{ route('leads.show',$lead) }}" class="fw-semibold text-decoration-none">{{ $lead->title }}</a></td><td>{{ $lead->company->name }}</td><td><span class="badge {{ $lead->score >= 80 ? 'text-bg-danger' : ($lead->score >= 60 ? 'text-bg-warning' : ($lead->score >= 40 ? 'text-bg-info' : 'text-bg-secondary')) }}">{{ $lead->score }}/100</span></td><td>{{ ucfirst($lead->temperature) }}</td><td>{{ ucfirst($lead->status) }}</td></tr>@empty<tr><td colspan="5" class="text-center py-5 text-muted">No leads to score.</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $leads->links() }}</div></div>
<div class="row g-3 mt-4"><div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><h5 class="fw-bold">Scoring bands</h5><p class="mb-1"><strong>80–100:</strong> Hot</p><p class="mb-1"><strong>60–79:</strong> High</p><p class="mb-1"><strong>40–59:</strong> Medium</p><p class="mb-0"><strong>0–39:</strong> Low</p></div></div></div><div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><h5 class="fw-bold">Signals</h5><p class="mb-0 text-muted">Emergency service, appointment-based, prominent phone, small team, outside-hours service, high-value service, multiple locations, online booking and live chat.</p></div></div></div></div>
</div>
@endsection
