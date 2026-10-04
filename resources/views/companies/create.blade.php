@extends('layouts.app')

@section('title', 'Add Company | AI Receptionist')
@section('page_title', 'Add Company')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <a href="{{ route('companies.index') }}"
           class="text-decoration-none text-muted">
            ← Back to Companies
        </a>

        <h2 class="fw-bold mt-3 mb-1">Add Company</h2>

        <p class="text-muted mb-0">
            Add a business to your sales prospect database.
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('companies.store') }}" method="POST">
        @csrf

        <div class="row g-4">

            {{-- Company Information --}}
            <div class="col-lg-8">

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">
                            Company Information
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">
                                    Company Name *
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    value="{{ old('name') }}"
                                    placeholder="e.g. ABC Cleaning Services"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Industry
                                </label>

                                <input
                                    type="text"
                                    name="industry"
                                    class="form-control"
                                    value="{{ old('industry') }}"
                                    placeholder="e.g. Cleaning">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Business Type
                                </label>

                                <input
                                    type="text"
                                    name="business_type"
                                    class="form-control"
                                    value="{{ old('business_type') }}"
                                    placeholder="e.g. Local Service Business">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Website
                                </label>

                                <input
                                    type="url"
                                    name="website"
                                    class="form-control"
                                    value="{{ old('website') }}"
                                    placeholder="https://example.co.uk">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Phone
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    value="{{ old('phone') }}"
                                    placeholder="+44...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email') }}"
                                    placeholder="info@example.co.uk">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Description
                                </label>

                                <textarea
                                    name="description"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Brief description of the business...">{{ old('description') }}</textarea>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- Location --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">
                            Location
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Address Line 1
                                </label>

                                <input
                                    type="text"
                                    name="address_line_1"
                                    class="form-control"
                                    value="{{ old('address_line_1') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Address Line 2
                                </label>

                                <input
                                    type="text"
                                    name="address_line_2"
                                    class="form-control"
                                    value="{{ old('address_line_2') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    City
                                </label>

                                <input
                                    type="text"
                                    name="city"
                                    class="form-control"
                                    value="{{ old('city') }}"
                                    placeholder="Birmingham">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    County
                                </label>

                                <input
                                    type="text"
                                    name="county"
                                    class="form-control"
                                    value="{{ old('county') }}"
                                    placeholder="West Midlands">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Postcode
                                </label>

                                <input
                                    type="text"
                                    name="postcode"
                                    class="form-control"
                                    value="{{ old('postcode') }}"
                                    placeholder="B1 1AA">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Country
                                </label>

                                <input
                                    type="text"
                                    name="country"
                                    class="form-control"
                                    value="{{ old('country', 'United Kingdom') }}">
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- CRM Information --}}
            <div class="col-lg-4">

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">
                            CRM Information
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Status
                            </label>

                            <select name="status" class="form-select">

                                <option value="prospect"
                                    {{ old('status', 'prospect') === 'prospect' ? 'selected' : '' }}>
                                    Prospect
                                </option>

                                <option value="qualified"
                                    {{ old('status') === 'qualified' ? 'selected' : '' }}>
                                    Qualified
                                </option>

                                <option value="customer"
                                    {{ old('status') === 'customer' ? 'selected' : '' }}>
                                    Customer
                                </option>

                                <option value="inactive"
                                    {{ old('status') === 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Lead Score
                            </label>

                            <input
                                type="number"
                                name="lead_score"
                                class="form-control"
                                value="{{ old('lead_score', 0) }}"
                                min="0"
                                max="100">

                            <small class="text-muted">
                                0–100. This will eventually be calculated automatically by the AI lead-scoring engine.
                            </small>

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Source
                            </label>

                            <input
                                type="text"
                                name="source"
                                class="form-control"
                                value="{{ old('source') }}"
                                placeholder="Google Maps, Website, LinkedIn...">

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Source URL
                            </label>

                            <input
                                type="url"
                                name="source_url"
                                class="form-control"
                                value="{{ old('source_url') }}"
                                placeholder="https://...">

                        </div>

                    </div>

                </div>

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <button
                            type="submit"
                            class="btn btn-dark w-100 mb-2">
                            Save Company
                        </button>

                        <a
                            href="{{ route('companies.index') }}"
                            class="btn btn-outline-secondary w-100">
                            Cancel
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection