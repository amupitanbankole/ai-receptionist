@extends('layouts.app')

@section('title', $contact->first_name . ' | Contact')
@section('page_title', 'Contact Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <a href="{{ route('contacts.index') }}" class="text-decoration-none text-muted">← Back to Contacts</a>
            <h2 class="fw-bold mt-3 mb-1">{{ $contact->first_name }} {{ $contact->last_name }}</h2>
            <p class="text-muted mb-0">
                {{ $contact->job_title ?: 'Contact' }}
                @if($contact->company)
                    · <a href="{{ route('companies.show', $contact->company) }}" class="text-decoration-none">{{ $contact->company->name }}</a>
                @endif
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-dark">Edit</a>
            <form method="POST" action="{{ route('contacts.destroy', $contact) }}" onsubmit="return confirm('Delete this contact?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Email</small>
                            @if($contact->email)
                                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                            @else
                                —
                            @endif
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted d-block">Phone</small>
                            {{ $contact->phone ?: '—' }}
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted d-block">Mobile / WhatsApp</small>
                            {{ $contact->mobile ?: '—' }}
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted d-block">LinkedIn</small>
                            @if($contact->linkedin_url)
                                <a href="{{ $contact->linkedin_url }}" target="_blank" rel="noopener noreferrer">Open profile</a>
                            @else
                                —
                            @endif
                        </div>

                        <div class="col-12">
                            <small class="text-muted d-block">Notes</small>
                            <p class="mb-0">{{ $contact->notes ?: 'No notes yet.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">CRM Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Company</small>
                        @if($contact->company)
                            <a href="{{ route('companies.show', $contact->company) }}">{{ $contact->company->name }}</a>
                        @else
                            —
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge {{ $contact->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ ucfirst($contact->status) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Primary Contact</small>
                        {{ $contact->is_primary ? 'Yes' : 'No' }}
                    </div>

                    <div>
                        <small class="text-muted d-block">Added</small>
                        {{ $contact->created_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection