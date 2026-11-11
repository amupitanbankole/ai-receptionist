@extends('layouts.app')

@section('title', 'Unclaimed Funds | Lead Generation')
@section('page_title', 'Unclaimed Funds Lead Generation')

@section('content')
<div class="container-fluid">
    <div class="alert alert-warning">
        <strong>Outbound calling is disabled.</strong>
        Public-record data is not calling consent. Calls must remain disabled until buyer-specific consent, suppression checks, state review and buyer authorization have all passed.
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            'Prospects' => $metrics['prospects'],
            'Verified property records' => $metrics['verified_records'],
            'Prospects with verified consent' => $metrics['consented_prospects'],
            'Currently eligible numbers' => $metrics['eligible_numbers'],
            'Confirmed appointments' => $metrics['appointments'],
            'Verified active buyers' => $metrics['active_buyers'],
        ] as $label => $value)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="fs-2 fw-bold">{{ number_format($value) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Recent recovery appointments</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Prospect</th><th>State</th><th>Buyer</th><th>Appointment</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($recentAppointments as $appointment)
                    <tr>
                        <td>{{ trim(($appointment->prospect->first_name ?? '').' '.($appointment->prospect->last_name ?? '')) ?: 'Prospect #'.$appointment->prospect_id }}</td>
                        <td>{{ $appointment->prospect->state?->code ?? '—' }}</td>
                        <td>{{ $appointment->buyer->display_name ?: $appointment->buyer->legal_name }}</td>
                        <td>{{ $appointment->starts_at->timezone($appointment->timezone)->format('M j, Y g:i A') }} {{ $appointment->timezone }}</td>
                        <td><span class="badge text-bg-secondary">{{ str_replace('_', ' ', ucfirst($appointment->status)) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No appointments have been created yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
