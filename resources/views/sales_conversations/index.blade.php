@extends('layouts.app')

@section('title', 'AI Sales Conversations')
@section('page_title', 'AI Sales Conversations')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-1">AI Sales Conversations</h2>
        <p class="text-muted mb-0">Review inbound replies, AI intent classification and suggested next actions.</p>
    </div>
    <a href="{{ route('sales-conversations.simulate') }}" class="btn btn-primary">Simulate Inbound Reply</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach(['open','waiting','closed'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="intent" class="form-select">
                    <option value="">All intents</option>
                    @foreach(['interested','question','objection','meeting_request','not_interested','unsubscribe','out_of_office','unclear'] as $intent)
                        <option value="{{ $intent }}" @selected(request('intent') === $intent)>{{ ucwords(str_replace('_',' ',$intent)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-outline-dark w-100">Filter</button></div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Last Activity</th><th>Company</th><th>Contact</th><th>Intent</th><th>Priority</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($conversations as $conversation)
                <tr>
                    <td>{{ $conversation->last_message_at?->format('d M Y H:i') ?? $conversation->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $conversation->company?->name ?? 'Unknown company' }}</td>
                    <td>{{ trim(($conversation->contact?->first_name ?? '').' '.($conversation->contact?->last_name ?? '')) ?: '—' }}</td>
                    <td><span class="badge text-bg-{{ in_array($conversation->intent, ['interested','meeting_request']) ? 'success' : ($conversation->intent === 'objection' ? 'warning' : 'secondary') }}">{{ $conversation->intent ? ucwords(str_replace('_',' ',$conversation->intent)) : 'Unclassified' }}</span></td>
                    <td>{{ ucfirst($conversation->priority) }}</td>
                    <td>{{ ucfirst($conversation->status) }}</td>
                    <td><a href="{{ route('sales-conversations.show',$conversation) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-5 text-muted">No sales conversations yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $conversations->links() }}</div>
@endsection