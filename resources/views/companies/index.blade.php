@extends('layouts.app')

@section('title', 'Companies | AI Receptionist')
@section('page_title', 'Companies')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">Companies</h2>
            <p class="text-muted mb-0">
                Manage businesses discovered by your sales engine.
            </p>
        </div>

        <a href="{{ route('companies.create') }}" class="btn btn-dark">
            + Add Company
        </a>

    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Companies Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            @if($companies->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th class="px-4">Company</th>
                                <th>Industry</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Lead Score</th>
                                <th>Added</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($companies as $company)

                                <tr>

                                    <td class="px-4">

                                        <div class="fw-semibold">
                                            {{ $company->name }}
                                        </div>

                                        @if($company->website)
                                            <small class="text-muted">
                                                {{ $company->website }}
                                            </small>
                                        @endif

                                    </td>

                                    <td>
                                        {{ $company->industry ?: '—' }}
                                    </td>

                                    <td>
                                        {{ $company->city ?: '—' }}
                                    </td>

                                    <td>

                                        @php
                                            $statusClasses = [
                                                'prospect' => 'bg-secondary',
                                                'qualified' => 'bg-primary',
                                                'customer' => 'bg-success',
                                                'inactive' => 'bg-dark',
                                            ];

                                            $statusClass =
                                                $statusClasses[$company->status]
                                                ?? 'bg-secondary';
                                        @endphp

                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst($company->status) }}
                                        </span>

                                    </td>

                                    <td>

                                        @php
                                            $score = $company->lead_score;

                                            if ($score >= 80) {
                                                $scoreClass = 'bg-danger';
                                                $scoreLabel = 'Hot';
                                            } elseif ($score >= 60) {
                                                $scoreClass = 'bg-warning text-dark';
                                                $scoreLabel = 'High';
                                            } elseif ($score >= 40) {
                                                $scoreClass = 'bg-info text-dark';
                                                $scoreLabel = 'Medium';
                                            } else {
                                                $scoreClass = 'bg-secondary';
                                                $scoreLabel = 'Low';
                                            }
                                        @endphp

                                        <span class="badge {{ $scoreClass }}">
                                            {{ $score }} — {{ $scoreLabel }}
                                        </span>

                                    </td>

                                    <td>
                                        {{ $company->created_at->format('d M Y') }}
                                    </td>

                                    <td class="text-end px-4">

                                        <a
                                            href="{{ route('companies.show', $company) }}"
                                            class="btn btn-sm btn-outline-dark">
                                            View
                                        </a>

                                        <a
                                            href="{{ route('companies.edit', $company) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            Edit
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                @if($companies->hasPages())

                    <div class="p-3 border-top">
                        {{ $companies->links() }}
                    </div>

                @endif

            @else

                <div class="text-center py-5 px-4">

                    <div class="display-6 mb-3">
                        🏢
                    </div>

                    <h4 class="fw-bold">
                        No companies yet
                    </h4>

                    <p class="text-muted mb-4">
                        Start building your prospect database by adding your first company.
                    </p>

                    <a
                        href="{{ route('companies.create') }}"
                        class="btn btn-dark">
                        + Add Your First Company
                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection