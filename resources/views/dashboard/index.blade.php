@extends('layouts.app')
@section('title','Dashboard | AI Receptionist')
@section('page_title','Dashboard')
@section('content')
<div class="container-fluid">
<div class="mb-4"><h2 class="fw-bold">Sales Engine Dashboard</h2><p class="text-muted mb-0">A live view of your CRM pipeline and next actions.</p></div>
<div class="row g-4 mb-4">
@foreach([['Total Leads',$totalLeads,'All opportunities','leads.index'],['Hot Leads',$hotLeads,'Priority prospects','lead-scoring.index'],['Demos',$demos,'Demo-stage leads','leads.index'],['Customers',$customers,'Customer-stage leads','leads.index']] as [$label,$value,$hint,$route])
<div class="col-md-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-muted mb-1">{{ $label }}</p><h2 class="fw-bold mb-0">{{ $value }}</h2><small class="text-muted">{{ $hint }}</small><br><a class="small text-decoration-none" href="{{ route($route) }}">View →</a></div></div></div>
@endforeach
</div>
<div class="row g-4">
<div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-header bg-white py-3 d-flex justify-content-between"><h5 class="fw-bold mb-0">Recent Activity</h5><a href="{{ route('activities.index') }}" class="small">View all</a></div><div class="card-body p-0">
@if($recentActivities->count())<div class="list-group list-group-flush">@foreach($recentActivities as $activity)<div class="list-group-item px-4 py-3"><div class="d-flex justify-content-between"><strong>{{ $activity->subject }}</strong><small class="text-muted">{{ $activity->occurred_at?->format('d M Y H:i') }}</small></div><small class="text-muted">{{ ucfirst($activity->type) }} · {{ $activity->company?->name ?? 'No company' }} @if($activity->lead) · {{ $activity->lead->title }} @endif</small></div>@endforeach</div>@else<div class="text-center py-5 text-muted">No activity recorded yet.</div>@endif
</div></div></div>
<div class="col-lg-4"><div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="fw-bold mb-0">Pipeline</h5></div><div class="card-body">
@foreach([['New',$newLeads],['Qualified',$qualified],['Demos',$demos],['Customers',$customers]] as [$label,$count])<div class="mb-3"><div class="d-flex justify-content-between"><span>{{ $label }}</span><strong>{{ $count }}</strong></div><div class="progress mt-2" style="height:6px"><div class="progress-bar" style="width:{{ $totalLeads ? min(100,($count/$totalLeads)*100) : 0 }}%"></div></div></div>@endforeach
</div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white py-3 d-flex justify-content-between"><h5 class="fw-bold mb-0">Next Follow-ups</h5><a href="{{ route('followups.index') }}" class="small">View all</a></div><div class="card-body">@forelse($upcomingFollowups as $followup)<div class="mb-3"><strong>{{ $followup->subject }}</strong><br><small class="text-muted">{{ $followup->lead->company->name }} · {{ $followup->due_at->format('d M Y H:i') }}</small></div>@empty<p class="text-muted mb-0">No pending follow-ups.</p>@endforelse</div></div>
</div></div></div>
@endsection
