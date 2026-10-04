@extends('layouts.app')

@section('title', 'Email Templates')
@section('page_title', 'Email Templates')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Email Templates</h2>
        <p class="text-muted mb-0">Reusable outreach copy with personalization variables.</p>
    </div>
    <a href="{{ route('email-templates.create') }}" class="btn btn-primary">New Template</a>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Name</th><th>Subject</th><th>Status</th><th>Updated</th><th></th></tr></thead>
            <tbody>
            @forelse($templates as $template)
                <tr>
                    <td class="fw-semibold">{{ $template->name }}</td>
                    <td>{{ $template->subject }}</td>
                    <td><span class="badge {{ $template->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $template->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>{{ $template->updated_at->format('d M Y H:i') }}</td>
                    <td class="text-end">
                        <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('email-templates.destroy', $template) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this template?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-5 text-muted">No templates yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $templates->links() }}</div>
@endsection
