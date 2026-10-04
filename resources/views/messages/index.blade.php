@extends('layouts.app')

@section('title', 'Messages')
@section('page_title', 'Messages')

@section('content')
<div class="mb-4"><h2 class="mb-1">Messages</h2><p class="text-muted mb-0">Outbound campaign email activity and delivery state.</p></div>
<div class="card border-0 shadow-sm"><div class="table-responsive">
<table class="table table-hover mb-0"><thead><tr><th>Date</th><th>Recipient</th><th>Company</th><th>Subject</th><th>Channel</th><th>Status</th></tr></thead><tbody>
@forelse($messages as $message)
<tr><td>{{ $message->created_at->format('d M Y H:i') }}</td><td>{{ $message->to_address }}</td><td>{{ $message->recipient?->company?->name ?? '—' }}</td><td>{{ $message->subject }}</td><td>{{ ucfirst($message->channel) }}</td><td><span class="badge text-bg-{{ $message->status==='sent'?'success':($message->status==='failed'?'danger':'secondary') }}">{{ ucfirst($message->status) }}</span></td></tr>
@empty<tr><td colspan="6" class="text-center py-5 text-muted">No messages yet.</td></tr>@endforelse
</tbody></table></div></div>
<div class="mt-3">{{ $messages->links() }}</div>
@endsection
