@extends('layouts.app')

@section('title', 'Dashboard | AI Receptionist')

@section('page_title', 'Dashboard')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h2 class="fw-bold">Good morning, Admin</h2>
        <p class="text-muted mb-0">
            Here's what's happening with your sales engine today.
        </p>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Leads</p>
                    <h2 class="fw-bold mb-0">0</h2>
                    <small class="text-muted">
                        No leads yet
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Hot Leads</p>
                    <h2 class="fw-bold mb-0">0</h2>
                    <small class="text-muted">
                        Ready for outreach
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Demos Booked</p>
                    <h2 class="fw-bold mb-0">0</h2>
                    <small class="text-muted">
                        This month
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Customers</p>
                    <h2 class="fw-bold mb-0">0</h2>
                    <small class="text-muted">
                        Active customers
                    </small>
                </div>
            </div>
        </div>

    </div>

    {{-- Main Dashboard --}}
    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        Recent Activity
                    </h5>
                </div>

                <div class="card-body">

                    <div class="text-center py-5">

                        <div class="display-6 mb-3">
                            No activity yet
                        </div>

                        <p class="text-muted">
                            Your sales activity will appear here.
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        Sales Pipeline
                    </h5>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>New Leads</span>
                            <strong>0</strong>
                        </div>

                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Qualified</span>
                            <strong>0</strong>
                        </div>

                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Demos</span>
                            <strong>0</strong>
                        </div>

                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between">
                            <span>Customers</span>
                            <strong>0</strong>
                        </div>

                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection