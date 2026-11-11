@extends('layouts.app')

@section('title', 'Unclaimed Funds Prospects')
@section('page_title', 'Unclaimed Funds Prospects')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info">
        These are research records, not permission to call. The outbound dialer is separately gated by verified consent and state/buyer eligibility.
    </div>
    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <input name="state" value="{{ request('state') }}" maxlength="2" class="form-control" placeholder="State code (e.g. TX)">
        </div>
        <div class="col-auto">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                @foreach(['uncontacted','consent_requested','consented','suppressed','qualified','closed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-dark">Filter</button></div>
        <div class="col-auto"><a href="{{ route('unclaimed-funds.prospects.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Name</th><th>State</th><th>Phone</th><th>Status</th><th>Record last verified</th><th>Contact restriction</th></tr></thead>
                <tbody>
                @forelse($prospects as $prospect)
                    <tr>
                        <td>{{ trim(($prospect->first_name ?? '').' '.($prospect->last_name ?? '')) ?: 'Unnamed prospect' }}</td>
                        <td>{{ $prospect->state?->code ?? '—' }}</td>
                        <td>{{ $prospect->phone_e164 ?: '—' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $prospect->status)) }}</td>
                        <td>{{ $prospect->public_record_last_verified_at?->format('M j, Y') ?? 'Not verified' }}</td>
                        <td>
                            @if($prospect->do_not_contact_at)
                                <span class="badge text-bg-danger">Suppressed</span>
                            @else
                                <span class="badge text-bg-secondary">Review required</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No prospects imported yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $prospects->links() }}</div>
    </div>
</div>
@endsection
