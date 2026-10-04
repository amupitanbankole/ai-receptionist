@extends('layouts.app')

@section('title', 'Contacts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Contacts</h1>
        <p class="text-muted mb-0">Manage people associated with your prospect companies.</p>
    </div>

    <a href="{{ route('contacts.create') }}" class="btn btn-primary">
        + Add Contact
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        @if($contacts->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Name</th>
                            <th>Company</th>
                            <th>Job Title</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($contacts as $contact)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-semibold">
                                        {{ $contact->first_name }}
                                        {{ $contact->last_name }}
                                    </div>

                                    @if($contact->is_primary)
                                        <span class="badge text-bg-primary">
                                            Primary
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($contact->company)
                                        <a href="{{ route('companies.show', $contact->company) }}"
                                           class="text-decoration-none">
                                            {{ $contact->company->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">No company</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $contact->job_title ?: '—' }}
                                </td>

                                <td>
                                    @if($contact->email)
                                        <a href="mailto:{{ $contact->email }}">
                                            {{ $contact->email }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    {{ $contact->phone ?: $contact->mobile ?: '—' }}
                                </td>

                                <td>
                                    @if($contact->status === 'active')
                                        <span class="badge text-bg-success">Active</span>
                                    @elseif($contact->status === 'inactive')
                                        <span class="badge text-bg-secondary">Inactive</span>
                                    @else
                                        <span class="badge text-bg-light text-dark">
                                            {{ ucfirst($contact->status) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end px-4">
                                    <a href="{{ route('contacts.show', $contact) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>

                                    <a href="{{ route('contacts.edit', $contact) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $contacts->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5>No contacts yet</h5>
                <p class="text-muted">
                    Add your first contact to start building your sales database.
                </p>

                <a href="{{ route('contacts.create') }}" class="btn btn-primary">
                    Add First Contact
                </a>
            </div>
        @endif
    </div>
</div>
@endsection