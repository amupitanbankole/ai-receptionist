@extends('layouts.app')

@section('title', 'Conversation | AI Sales')
@section('page_title', 'AI Sales Conversation')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <a href="{{ route('sales-conversations.index') }}" class="text-decoration-none text-muted">← Back to Conversations</a>
        <h2 class="fw-bold mt-3 mb-1">{{ $conversation->subject ?: 'Sales Conversation' }}</h2>
        <p class="text-muted mb-0">{{ $conversation->company?->name ?? 'Unknown company' }} @if($conversation->contact) · {{ $conversation->contact->first_name }} {{ $conversation->contact->last_name }} @endif</p>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('sales-conversations.generate',$conversation) }}">@csrf<button class="btn btn-primary">Generate AI Reply</button></form>
        <form method="POST" action="{{ route('sales-conversations.status',$conversation) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="{{ $conversation->status === 'closed' ? 'open' : 'closed' }}">
            <button class="btn btn-outline-dark">{{ $conversation->status === 'closed' ? 'Reopen' : 'Close' }}</button>
        </form>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h5 class="fw-bold mb-0">Conversation History</h5></div>
            <div class="card-body">
                @forelse($conversation->messages as $message)
                    <div class="border rounded p-3 mb-3 {{ $message->direction === 'inbound' ? 'bg-light' : 'bg-white' }}">
                        <div class="d-flex justify-content-between mb-2"><strong>{{ ucfirst($message->direction) }}</strong><small class="text-muted">{{ $message->created_at->format('d M Y H:i') }}</small></div>
                        @if($message->subject)<div class="fw-semibold mb-2">{{ $message->subject }}</div>@endif
                        <div style="white-space:pre-wrap;">{{ $message->body }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No messages in this conversation.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3"><h5 class="fw-bold mb-0">AI Classification</h5></div>
            <div class="card-body">
                <p class="mb-2"><strong>Intent:</strong> {{ $conversation->intent ? ucwords(str_replace('_',' ',$conversation->intent)) : 'Unclassified' }}</p>
                <p class="mb-2"><strong>Priority:</strong> {{ ucfirst($conversation->priority) }}</p>
                <p class="mb-2"><strong>Status:</strong> {{ ucfirst($conversation->status) }}</p>
                <p class="mb-2"><strong>Next action:</strong> {{ $conversation->next_action ?: '—' }}</p>
                <p class="mb-0"><strong>Summary:</strong> {{ $conversation->summary ?: '—' }}</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h5 class="fw-bold mb-0">AI Reply Suggestions</h5></div>
            <div class="card-body">
                @forelse($conversation->aiReplySuggestions as $suggestion)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between"><span class="badge text-bg-secondary">{{ ucfirst($suggestion->status) }}</span><small class="text-muted">{{ $suggestion->generated_at?->format('d M H:i') }}</small></div>
                        <div class="mt-3"><strong>{{ $suggestion->subject ?: 'Reply' }}</strong></div>
                        <div class="mt-2" style="white-space:pre-wrap;">{{ $suggestion->body }}</div>
                        @if($suggestion->rationale)<hr><small class="text-muted">{{ $suggestion->rationale }}</small>@endif
                    </div>
                @empty
                    <p class="text-muted mb-0">No AI suggestion yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection