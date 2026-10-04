@extends('layouts.app')

@section('title', 'Campaigns')
@section('page_title', 'Outreach Campaigns')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Outreach Campaigns</h2>
        <p class="text-muted mb-0">Email sequences, daily sending limits and campaign performance.</p>
    </div>
    <div>
        <a href="{{ route('email-templates.index') }}" class="btn btn-outline-secondary me-2">Templates</a>
        <a href="{{ route('campaigns.create') }}" class="btn btn-primary">New Campaign</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="row g-3 mb-4">
    @foreach([
        ['Draft', 'draft'], ['Active', 'active'], ['Paused', 'paused'], ['Completed', 'completed']
    ] as [$label,$status])
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">{{ $label }}</div><div class="fs-3 fw-bold">{{ \App\Models\Campaign::where('status',$status)->count() }}</div></div></div></div>
    @endforeach
</div>

<div class="card border-0 shadow-sm">
<div class="table-responsive">
<table class="table table-hover mb-0">
<thead><tr><th>Campaign</th><th>Status</th><th>Recipients</th><th>Sent</th><th>Replies</th><th>Daily limit</th><th></th></tr></thead>
<tbody>
@forelse($campaigns as $campaign)
<tr>
<td><a class="fw-semibold text-decoration-none" href="{{ route('campaigns.show',$campaign) }}">{{ $campaign->name }}</a><div class="small text-muted">{{ $campaign->scheduled_at?->format('d M Y H:i') ?? 'Not scheduled' }}</div></td>
<td><span class="badge text-bg-{{ $campaign->status === 'active' ? 'success' : ($campaign->status === 'completed' ? 'primary' : 'secondary') }}">{{ ucfirst($campaign->status) }}</span></td>
<td>{{ $campaign->recipients_count }}</td>
<td>{{ $campaign->sent_count }}</td>
<td>{{ $campaign->replied_count }}</td>
<td>{{ $campaign->daily_limit }}</td>
<td class="text-end"><a href="{{ route('campaigns.show',$campaign) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
</tr>
@empty
<tr><td colspan="7" class="text-center py-5 text-muted">No campaigns yet.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
<div class="mt-3">{{ $campaigns->links() }}</div>
@endsection
