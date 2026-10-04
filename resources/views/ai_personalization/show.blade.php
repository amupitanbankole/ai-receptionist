@extends('layouts.app')

@section('title', 'AI Personalization | '.$lead->title)
@section('page_title', 'AI Personalization')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <a href="{{ route('leads.show', $lead) }}" class="text-decoration-none text-muted">← Back to Lead</a>
        <h2 class="fw-bold mt-3 mb-1">AI Outreach Personalization</h2>
        <p class="text-muted mb-0">{{ $lead->company->name }} · {{ $lead->title }}</p>
    </div>
    <form method="POST" action="{{ route('ai-personalization.generate', $lead) }}">
        @csrf
        <button class="btn btn-primary">Generate New Draft</button>
    </form>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3"><h5 class="fw-bold mb-0">Prospect Context</h5></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Company</dt><dd class="col-sm-7">{{ $lead->company->name }}</dd>
                    <dt class="col-sm-5">Industry</dt><dd class="col-sm-7">{{ $lead->company->industry ?: '—' }}</dd>
                    <dt class="col-sm-5">Location</dt><dd class="col-sm-7">{{ $lead->company->city ?: '—' }}</dd>
                    <dt class="col-sm-5">Lead score</dt><dd class="col-sm-7">{{ $lead->score }}/100</dd>
                    <dt class="col-sm-5">Temperature</dt><dd class="col-sm-7">{{ ucfirst($lead->temperature) }}</dd>
                    <dt class="col-sm-5">Research</dt><dd class="col-sm-7">{{ $lead->company->intelligence?->research_summary ?: 'No research summary yet.' }}</dd>
                </dl>
            </div>
        </div>
        <div class="alert alert-light border">
            <strong>Guardrail:</strong> drafts are generated from stored CRM and website research only. Review before sending.
        </div>
    </div>

    <div class="col-lg-7">
        @forelse($personalizations as $item)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between">
                    <strong>{{ $item->subject ?: 'AI Draft' }}</strong>
                    <span class="badge text-bg-{{ $item->status === 'generated' ? 'success' : ($item->status === 'failed' ? 'danger' : 'secondary') }}">{{ ucfirst($item->status) }}</span>
                </div>
                <div class="card-body">
                    @if($item->content)
                        <div class="border rounded p-3" style="white-space: pre-wrap;">{{ $item->content }}</div>
                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('campaigns.create') }}" class="btn btn-outline-primary">Create Campaign</a>
                            <form method="POST" action="{{ route('ai-personalization.use-campaign', $item) }}">
                                @csrf
                                <button class="btn btn-primary">Use as Campaign Draft</button>
                            </form>
                        </div>
                    @elseif($item->metadata['error'] ?? false)
                        <div class="text-danger small">{{ $item->metadata['error'] }}</div>
                    @endif
                </div>
                <div class="card-footer bg-white text-muted small">
                    {{ $item->generated_at?->format('d M Y H:i') ?? $item->created_at->format('d M Y H:i') }}
                    · {{ $item->model ?: 'AI provider' }}
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm"><div class="card-body text-center py-5 text-muted">No AI drafts yet. Generate the first one.</div></div>
        @endforelse
        {{ $personalizations->links() }}
    </div>
</div>
@endsection
