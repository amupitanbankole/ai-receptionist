@extends('layouts.app')

@section('title', 'Edit ' . $company->name . ' | AI Receptionist')
@section('page_title', 'Edit Company')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <a href="{{ route('companies.show', $company) }}"
           class="text-decoration-none text-muted">
            ← Back to Company
        </a>

        <h2 class="fw-bold mt-3 mb-1">Edit Company</h2>

        <p class="text-muted mb-0">
            Update information for {{ $company->name }}.
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

    <form
        action="{{ route('companies.update', $company) }}"
        method="POST">

        @csrf
        @method('PUT')

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
                                    value="{{ old('name', $company->name) }}"
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
                                    value="{{ old('industry', $company->industry) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Business Type
                                </label>

                                <input
                                    type="text"
                                    name="business_type"
                                    class="form-control"
                                    value="{{ old('business_type', $company->business_type) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Website
                                </label>

                                <input
                                    type="url"
                                    name="website"
                                    class="form-control"
                                    value="{{ old('website', $company->website) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Phone
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    value="{{ old('phone', $company->phone) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email', $company->email) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Description
                                </label>

                                <textarea
                                    name="description"
                                    class="form-control"
                                    rows="4">{{ old('description', $company->description) }}</textarea>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- Location --}}
                <div class="card border-0 shadow-sm">

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
                                    value="{{ old('address_line_1', $company->address_line_1) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Address Line 2
                                </label>

                                <input
                                    type="text"
                                    name="address_line_2"
                                    class="form-control"
                                    value="{{ old('address_line_2', $company->address_line_2) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    City
                                </label>

                                <input
                                    type="text"
                                    name="city"
                                    class="form-control"
                                    value="{{ old('city', $company->city) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    County
                                </label>

                                <input
                                    type="text"
                                    name="county"
                                    class="form-control"
                                    value="{{ old('county', $company->county) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Postcode
                                </label>

                                <input
                                    type="text"
                                    name="postcode"
                                    class="form-control"
                                    value="{{ old('postcode', $company->postcode) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Country
                                </label>

                                <input
                                    type="text"
                                    name="country"
                                    class="form-control"
                                    value="{{ old('country', $company->country) }}">
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
                                    {{ old('status', $company->status) === 'prospect' ? 'selected' : '' }}>
                                    Prospect
                                </option>

                                <option value="qualified"
                                    {{ old('status', $company->status) === 'qualified' ? 'selected' : '' }}>
                                    Qualified
                                </option>

                                <option value="customer"
                                    {{ old('status', $company->status) === 'customer' ? 'selected' : '' }}>
                                    Customer
                                </option>

                                <option value="inactive"
                                    {{ old('status', $company->status) === 'inactive' ? 'selected' : '' }}>
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
                                value="{{ old('lead_score', $company->lead_score) }}"
                                min="0"
                                max="100">

                            <small class="text-muted">
                                0–100. This will eventually be calculated automatically.
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
                                value="{{ old('source', $company->source) }}">

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Source URL
                            </label>

                            <input
                                type="url"
                                name="source_url"
                                class="form-control"
                                value="{{ old('source_url', $company->source_url) }}">

                        </div>

                    </div>

                </div>

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <button
                            type="submit"
                            class="btn btn-dark w-100 mb-2">
                            Save Changes
                        </button>

                        <a
                            href="{{ route('companies.show', $company) }}"
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