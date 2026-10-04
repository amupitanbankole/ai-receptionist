@extends('layouts.app')

@section('title', 'New Campaign')
@section('page_title', 'Create Outreach Campaign')

@section('content')
<form method="POST" action="{{ route('campaigns.store') }}">
@csrf
<div class="row g-4">
<div class="col-lg-8">
@if($aiDraft)
<div class="alert alert-primary"><strong>AI draft loaded.</strong> Review the copy, select the intended lead(s), then create the campaign.</div>
@endif
<div class="card border-0 shadow-sm mb-4"><div class="card-body">
<h5 class="mb-3">Campaign</h5>
<div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name', $aiDraft ? 'AI Personalized Outreach' : '') }}" placeholder="Birmingham Cleaners — AI Receptionist" required></div>
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">Template</label><select name="template_id" class="form-select"><option value="">Custom campaign copy</option>@foreach($templates as $template)<option value="{{ $template->id }}" @selected(old('template_id') == $template->id)>{{ $template->name }}</option>@endforeach</select></div>
<div class="col-md-6 mb-3"><label class="form-label">Daily sending limit</label><input type="number" name="daily_limit" class="form-control" value="{{ old('daily_limit', 25) }}" min="1" max="1000" required></div>
</div>
<div class="mb-3"><label class="form-label">Subject</label><input name="subject" class="form-control" value="{{ old('subject', $aiDraft['subject'] ?? '') }}" placeholder="Quick question for &#123;&#123;company_name&#125;&#125;"></div>
<div class="mb-3"><label class="form-label">Initial email body</label><textarea name="body" rows="12" class="form-control font-monospace" placeholder="Hi &#123;&#123;first_name&#125;&#125;,&#10;&#10;I noticed &#123;&#123;company_name&#125;&#125; ...">{{ old('body', $aiDraft['body'] ?? '') }}</textarea></div>
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">Schedule</label><input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}"></div>
<div class="col-md-6 mb-3"><label class="form-label">Reply-to email</label><input type="email" name="reply_to" class="form-control" value="{{ old('reply_to') }}"></div>
</div>
<div class="mb-0"><label class="form-label">Unsubscribe text</label><textarea name="unsubscribe_text" rows="2" class="form-control">{{ old('unsubscribe_text', 'If you no longer want to receive these emails, use the unsubscribe link below.') }}</textarea></div>
</div></div>

<div class="card border-0 shadow-sm"><div class="card-body">
<h5 class="mb-3">Select leads</h5>
<div class="small text-muted mb-3">Only leads with a contact email or company email can be added. Suppressed addresses are skipped automatically.</div>
<div class="table-responsive" style="max-height:520px;overflow:auto">
<table class="table table-sm align-middle"><thead><tr><th></th><th>Lead</th><th>Company</th><th>Contact</th><th>Email</th><th>Score</th></tr></thead>
<tbody>
@foreach($leads as $lead)
@php($email = $lead->contact?->email ?: $lead->company?->email)
@if($email)
<tr>
<td><input class="form-check-input" type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" @checked(in_array($lead->id, old('lead_ids', $aiDraft ? [$aiDraft['lead_id']] : [])))></td>
<td>{{ $lead->title }}</td>
<td>{{ $lead->company->name }}</td>
<td>{{ trim(($lead->contact?->first_name ?? '').' '.($lead->contact?->last_name ?? '')) ?: '—' }}</td>
<td>{{ $email }}</td>
<td><span class="badge text-bg-{{ $lead->temperature === 'hot' ? 'danger' : ($lead->temperature === 'high' ? 'warning' : 'secondary') }}">{{ $lead->score }}</span></td>
</tr>
@endif
@endforeach
</tbody></table>
</div>
</div></div>
</div>

<div class="col-lg-4">
<div class="card border-0 shadow-sm mb-4"><div class="card-body">
<h5>AI Personalization</h5>
<p class="small text-muted">Generate a prospect-specific draft from CRM and website research, then review it before sending.</p>
<a href="{{ $aiDraft ? route('leads.show', $aiDraft['lead_id']) : route('leads.index') }}" class="btn btn-outline-primary w-100">{{ $aiDraft ? 'Review Source Lead' : 'Open Lead CRM' }}</a>
</div></div>
<div class="card border-0 shadow-sm mb-4"><div class="card-body">
<h5>Placeholders</h5>
<p class="small text-muted">Use these placeholders in subject/body:</p>
<div class="d-flex flex-wrap gap-2"><code>&#123;&#123;first_name&#125;&#125;</code><code>&#123;&#123;company_name&#125;&#125;</code><code>&#123;&#123;industry&#125;&#125;</code><code>&#123;&#123;website&#125;&#125;</code><code>&#123;&#123;city&#125;&#125;</code><code>&#123;&#123;research_summary&#125;&#125;</code><code>&#123;&#123;lead_title&#125;&#125;</code></div>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-body">
<h5>Before launch</h5>
<ul class="small text-muted">
<li>Connect a real SMTP/Resend/Postmark mailer in <code>.env</code>.</li>
<li>Keep daily limits conservative while domain reputation is established.</li>
<li>Only contact appropriate prospects and honor opt-outs.</li>
<li>Review AI-generated copy before sending.</li>
</ul>
<button class="btn btn-primary w-100">Create Campaign</button>
<a href="{{ route('campaigns.index') }}" class="btn btn-link w-100 mt-2">Cancel</a>
</div></div>
</div>
</div>
</form>
@endsection