@extends('layouts.app')
@section('title','Activities | AI Receptionist')
@section('page_title','Activities')
@section('content')
<div class="container-fluid"><div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="fw-bold mb-1">Activities</h2><p class="text-muted mb-0">Keep a history of CRM interactions.</p></div><a href="{{ route('activities.create') }}" class="btn btn-dark">+ Record Activity</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th class="px-4">Subject</th><th>Type</th><th>Lead</th><th>Company</th><th>Occurred</th></tr></thead><tbody>@forelse($activities as $activity)<tr><td class="px-4"><strong>{{ $activity->subject }}</strong><br><small class="text-muted">{{ $activity->description }}</small></td><td>{{ ucfirst($activity->type) }}</td><td>{{ $activity->lead?->title ?? '—' }}</td><td>{{ $activity->company?->name ?? '—' }}</td><td>{{ $activity->occurred_at?->format('d M Y H:i') }}</td></tr>@empty<tr><td colspan="5" class="text-center py-5 text-muted">No activities recorded.</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $activities->links() }}</div></div></div>
@endsection
