@extends('layouts.app')
@section('title','Prospect Discovery | AI Receptionist')
@section('page_title','Prospect Discovery')
@section('content')
<div class="container-fluid">
<div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="fw-bold mb-1">Prospect Discovery</h2><p class="text-muted mb-0">Build a queue of UK businesses before converting them into CRM companies.</p></div><a href="{{ route('discovery.create') }}" class="btn btn-dark">+ Add Prospect</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="px-4">Prospect</th><th>Industry</th><th>Location</th><th>Source</th><th>Status</th><th class="text-end px-4">Action</th></tr></thead><tbody>
@forelse($results as $result)<tr><td class="px-4"><strong>{{ $result->company_name }}</strong>@if($result->website)<br><small class="text-muted">{{ $result->website }}</small>@endif</td><td>{{ $result->industry ?: '—' }}</td><td>{{ $result->city ?: '—' }}</td><td>{{ $result->source }}</td><td><span class="badge {{ $result->status === 'converted' ? 'text-bg-success' : ($result->status === 'discarded' ? 'text-bg-secondary' : 'text-bg-warning') }}">{{ ucfirst($result->status) }}</span></td><td class="text-end px-4">@if(!$result->company_id)<form method="POST" action="{{ route('discovery.promote',$result) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-dark">Convert</button></form><form method="POST" action="{{ route('discovery.discard',$result) }}" class="d-inline">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-secondary">Discard</button></form>@else<a href="{{ route('companies.show',$result->company_id) }}" class="btn btn-sm btn-outline-success">View Company</a>@endif</td></tr>
@empty<tr><td colspan="6" class="text-center py-5 text-muted">No prospects discovered yet.</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $results->links() }}</div></div>
</div>
@endsection
