@extends('layouts.app')
@section('title','Appointments')
@section('page_title','Appointments')
@section('content')
<div class="d-flex justify-content-between mb-4"><h2>Appointments</h2><a class="btn btn-primary" href="{{ route('receptionist.dashboard') }}">AI Receptionist</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Customer</th><th>Company</th><th>Type</th><th>When</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($appointments as $a)<tr><td>{{ $a->customer_name }}<br><small>{{ $a->customer_email }}</small></td><td>{{ $a->company->name }}</td><td>{{ $a->appointmentType?->name }}</td><td>{{ $a->starts_at->format('D j M Y H:i') }}</td><td>{{ ucfirst($a->status) }}</td><td><form method="POST" action="{{ route('appointments.status',$a) }}" class="d-flex gap-1">@csrf @method('PATCH')<select name="status" class="form-select form-select-sm"><option value="confirmed">Confirmed</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option><option value="no_show">No show</option></select><button class="btn btn-sm btn-outline-secondary">Save</button></form></td></tr>@empty<tr><td colspan="6" class="text-muted">No appointments.</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $appointments->links() }}</div></div>
@endsection