@extends('layouts.app')

@section('title', $campaign->name)
@section('page_title', 'Campaign: '.$campaign->name)

@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="d-flex justify-content-between align-items-center mb-4">
<div><h2 class="mb-1">{{ $campaign->name }}</h2><span class="badge text-bg-secondary">{{ ucfirst($campaign->status) }}</span></div>
<div>
@if($campaign->status !== 'active')
<form method="POST" action="{{ route('campaigns.launch',$campaign) }}" class="d-inline">@csrf<button class="btn btn-success">Launch / Resume</button></form>
@else
<form method="POST" action="{{ route('campaigns.pause',$campaign) }}" class="d-inline">@csrf<button class="btn btn-warning">Pause</button></form>
@endif
<a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary ms-2">Back</a>
</div>
</div>

<div class="row g-3 mb-4">
@foreach([['Recipients',$stats['total']],['Queued',$stats['queued']],['Sent',$stats['sent']],['Replies',$stats['replied']],['Failed',$stats['failed']]] as [$label,$value])
<div class="col"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">{{ $label }}</div><div class="fs-4 fw-bold">{{ $value }}</div></div></div></div>
@endforeach
</div>

<div class="row g-4">
<div class="col-lg-8">
<div class="card border-0 shadow-sm"><div class="card-body">
<h5>Sequence</h5>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Step</th><th>Day</th><th>Subject</th><th></th></tr></thead><tbody>
@foreach($campaign->steps as $step)
<tr><td>{{ $step->step_number }}</td><td>Day {{ $step->day_offset }}</td><td>{{ $step->subject }}</td><td class="text-end">@if($step->step_number>1)<form method="POST" action="{{ route('campaigns.steps.destroy',[$campaign,$step]) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form>@endif</td></tr>
@endforeach
</tbody></table></div>
</div></div>

<div class="card border-0 shadow-sm mt-4"><div class="card-body">
<h5>Recipients</h5>
<div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Email</th><th>Company</th><th>Step</th><th>Status</th><th>Next</th></tr></thead><tbody>
@forelse($recipients as $recipient)
<tr><td>{{ $recipient->email }}</td><td>{{ $recipient->company?->name ?? '—' }}</td><td>{{ $recipient->current_step }}</td><td><span class="badge text-bg-secondary">{{ ucfirst($recipient->status) }}</span></td><td>{{ $recipient->next_step_at?->format('d M Y H:i') ?? '—' }}</td></tr>
@empty<tr><td colspan="5" class="text-center py-4 text-muted">No eligible recipients.</td></tr>@endforelse
</tbody></table></div>
{{ $recipients->links() }}
</div></div>
</div>

<div class="col-lg-4">
<div class="card border-0 shadow-sm"><div class="card-body">
<h5>Add follow-up step</h5>
<p class="small text-muted">Day offset is measured from the campaign start/previous step schedule.</p>
<form method="POST" action="{{ route('campaigns.steps.store',$campaign) }}">
@csrf
<div class="mb-3"><label class="form-label">Day offset</label><input type="number" name="day_offset" class="form-control" min="0" max="365" required></div>
<div class="mb-3"><label class="form-label">Subject</label><input name="subject" class="form-control" placeholder="Following up..." required></div>
<div class="mb-3"><label class="form-label">Body</label><textarea name="body" rows="8" class="form-control" required>Hi {{ '{{first_name}}' }},

Just following up on my previous note about {{ '{{company_name}}' }}.</textarea></div>
<button class="btn btn-primary w-100">Add Step</button>
</form>
</div></div>

<div class="card border-0 shadow-sm mt-4"><div class="card-body">
<h6>Sending settings</h6>
<div class="small text-muted">Daily limit: <strong>{{ $campaign->daily_limit }}</strong><br>Schedule: <strong>{{ $campaign->scheduled_at?->format('d M Y H:i') ?? 'Immediate' }}</strong><br>Reply-to: <strong>{{ $campaign->reply_to ?: config('mail.from.address') }}</strong></div>
</div></div>
</div>
</div>
@endsection
