@extends('layouts.app')

@section('title', $company->name . ' | AI Receptionist')
@section('page_title', 'Company Details')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>

            <a href="{{ route('companies.index') }}"
               class="text-decoration-none text-muted">
                ← Back to Companies
            </a>

            <div class="d-flex align-items-center gap-3 mt-3">

                <div>
                    <h2 class="fw-bold mb-1">
                        {{ $company->name }}
                    </h2>

                    <p class="text-muted mb-0">
                        {{ $company->industry ?: 'Business' }}
                        @if($company->city)
                            · {{ $company->city }}
                        @endif
                    </p>
                </div>

                @php
                    $statusClasses = [
                        'prospect' => 'bg-secondary',
                        'qualified' => 'bg-primary',
                        'customer' => 'bg-success',
                        'inactive' => 'bg-dark',
                    ];
                @endphp

                <span class="badge {{ $statusClasses[$company->status] ?? 'bg-secondary' }}">
                    {{ ucfirst($company->status) }}
                </span>

            </div>

        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('research.show', $company) }}"
                class="btn btn-dark">
                Research
            </a>

            <a
                href="{{ route('companies.edit', $company) }}"
                class="btn btn-outline-dark">
                Edit Company
            </a>

            <form
                action="{{ route('companies.destroy', $company) }}"
                method="POST"
                onsubmit="return confirm('Are you sure you want to delete this company?');">

                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-outline-danger">
                    Delete
                </button>

            </form>

        </div>

    </div>

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-1">
                        Lead Score
                    </p>

                    <h2 class="fw-bold mb-0">
                        {{ $company->lead_score }}
                        <small class="fs-6 text-muted">/ 100</small>
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-1">
                        Contacts
                    </p>

                    <h2 class="fw-bold mb-0">
                        {{ $company->contacts->count() }}
                    </h2>

                    <small class="text-muted">
                        CRM contacts
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-1">
                        Messages
                    </p>

                    <h2 class="fw-bold mb-0">
                        {{ $company->leads->count() }}
                    </h2>

                    <small class="text-muted">
                        Leads in pipeline
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-1">
                        Appointments
                    </p>

                    <h2 class="fw-bold mb-0">
                        {{ $company->activities->count() }}
                    </h2>

                    <small class="text-muted">
                        Recorded activities
                    </small>

                </div>

            </div>

        </div>

    </div>

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

                    <div class="row g-4">

                        <div class="col-md-6">

                            <small class="text-muted d-block">
                                Website
                            </small>

                            @if($company->website)

                                <a
                                    href="{{ $company->website }}"
                                    target="_blank"
                                    rel="noopener noreferrer">

                                    {{ $company->website }}

                                </a>

                            @else
                                —
                            @endif

                        </div>

                        <div class="col-md-6">

                            <small class="text-muted d-block">
                                Phone
                            </small>

                            {{ $company->phone ?: '—' }}

                        </div>

                        <div class="col-md-6">

                            <small class="text-muted d-block">
                                Email
                            </small>

                            {{ $company->email ?: '—' }}

                        </div>

                        <div class="col-md-6">

                            <small class="text-muted d-block">
                                Business Type
                            </small>

                            {{ $company->business_type ?: '—' }}

                        </div>

                        <div class="col-12">

                            <small class="text-muted d-block">
                                Description
                            </small>

                            <p class="mb-0">
                                {{ $company->description ?: 'No description available.' }}
                            </p>

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

                    <p class="mb-1">
                        {{ $company->address_line_1 ?: '' }}
                    </p>

                    @if($company->address_line_2)
                        <p class="mb-1">
                            {{ $company->address_line_2 }}
                        </p>
                    @endif

                    <p class="mb-1">

                        {{ $company->city ?: '' }}

                        @if($company->county)
                            , {{ $company->county }}
                        @endif

                    </p>

                    <p class="mb-1">
                        {{ $company->postcode ?: '' }}
                    </p>

                    <p class="mb-0">
                        {{ $company->country }}
                    </p>

                </div>

            </div>

            {{-- Future AI Intelligence --}}
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0 py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <h5 class="fw-bold mb-0">AI Sales Intelligence</h5>
                        <a href="{{ route('research.show', $company) }}" class="btn btn-sm btn-outline-dark">Open Research</a>

                    </div>

                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="border rounded p-3">

                                <h6 class="fw-bold">
                                    Lead Scoring
                                </h6>

                                <p class="text-muted small mb-0">
                                    Current score: {{ $company->lead_score }}/100. Scoring signals are managed in Lead Scoring.
                                </p>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="border rounded p-3">

                                <h6 class="fw-bold">
                                    Website Research
                                </h6>

                                <p class="text-muted small mb-0">
                                    Research status: {{ ucfirst($company->intelligence?->research_status ?? 'pending') }}.
                                    @if($company->intelligence?->researched_at) Last run {{ $company->intelligence->researched_at->format('d M Y H:i') }}. @endif
                                </p>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="border rounded p-3">

                                <h6 class="fw-bold">
                                    Personalised Outreach
                                </h6>

                                <p class="text-muted small mb-0">
                                    Generate personalised email and WhatsApp
                                    messages based on the company's profile.
                                </p>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="border rounded p-3">

                                <h6 class="fw-bold">
                                    AI Receptionist Demo
                                </h6>

                                <p class="text-muted small mb-0">
                                    Generate a receptionist configured around
                                    this company's services and booking flow.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- CRM Sidebar --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-0 py-3">

                    <h5 class="fw-bold mb-0">
                        CRM Details
                    </h5>

                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Status
                        </small>

                        <strong>
                            {{ ucfirst($company->status) }}
                        </strong>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Lead Score
                        </small>

                        <strong>
                            {{ $company->lead_score }} / 100
                        </strong>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Source
                        </small>

                        {{ $company->source ?: '—' }}

                    </div>

                    <div>

                        <small class="text-muted d-block">
                            Added
                        </small>

                        {{ $company->created_at->format('d M Y H:i') }}

                    </div>

                </div>

            </div>

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0 py-3">

                    <h5 class="fw-bold mb-0">
                        Sales Activity
                    </h5>

                </div>

                <div class="card-body">

                    <div class="text-center py-4">

                        <div class="display-6 mb-3">
                            📊
                        </div>

                        <p class="text-muted mb-0">
                            No sales activity yet.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection