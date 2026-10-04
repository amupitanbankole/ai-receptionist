@extends('layouts.app')

@section('title', 'Add Contact | AI Receptionist')
@section('page_title', 'Add Contact')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Add Contact</h2>
            <p class="text-muted mb-0">Add a decision-maker or sales contact to a company.</p>
        </div>
        <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">← Back to Contacts</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('contacts.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Company <span class="text-danger">*</span></label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">Select company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Job Title</label>
                                <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}" placeholder="Owner, Director, Manager">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mobile / WhatsApp</label>
                                <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">LinkedIn URL</label>
                                <input type="url" name="linkedin_url" class="form-control" value="{{ old('linkedin_url') }}" placeholder="https://www.linkedin.com/in/...">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="4" placeholder="Useful sales context, preferences, or research notes...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">CRM Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', 'active') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-check mb-4">
                            <input type="hidden" name="is_primary" value="0">
                            <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="is_primary" @checked(old('is_primary'))>
                            <label class="form-check-label" for="is_primary">
                                Primary contact
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Save Contact</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection