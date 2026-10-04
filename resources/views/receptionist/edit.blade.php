@extends('layouts.app')
@section('title','Configure AI Receptionist')
@section('page_title','Configure AI Receptionist')
@section('content')
<div class="d-flex justify-content-between mb-4"><div><h2>{{ $company->name }}</h2><p class="text-muted">Receptionist configuration, knowledge, booking and availability.</p></div><a href="{{ route('receptionist.dashboard',['company_id'=>$company->id]) }}" class="btn btn-outline-secondary">Dashboard</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card mb-4"><div class="card-header fw-bold">Receptionist settings</div><div class="card-body"><form method="POST" action="{{ route('receptionist.update',$company) }}">@csrf @method('PUT')<textarea name="allowed_origins" class="d-none">{{ implode("\n",$serviceOrigins) }}</textarea>
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Display name</label><input name="display_name" class="form-control" value="{{ old('display_name',$config->display_name) }}"></div>
<div class="col-md-3"><label class="form-label">Tone</label><select name="tone" class="form-select">@foreach(['professional','friendly','concise','warm'] as $tone)<option value="{{ $tone }}" @selected($config->tone===$tone)>{{ ucfirst($tone) }}</option>@endforeach</select></div>
<div class="col-md-3 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled" value="1" @checked($config->enabled)><label class="form-check-label">Enabled</label></div></div>
<div class="col-12"><label class="form-label">Greeting</label><input name="greeting" class="form-control" value="{{ old('greeting',$config->greeting) }}"></div>
<div class="col-md-6"><label class="form-label">Services (one per line)</label><textarea name="services" class="form-control" rows="6">{{ implode("\n",$config->services ?? []) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Service areas (one per line)</label><textarea name="service_areas" class="form-control" rows="6">{{ implode("\n",$config->service_areas ?? []) }}</textarea></div>
<div class="col-12"><label class="form-label">Business hours / notes (one per line)</label><textarea name="business_hours" class="form-control" rows="4">{{ implode("\n",$config->business_hours ?? []) }}</textarea></div>
<div class="col-12"><label class="form-label">AI instructions</label><textarea name="instructions" class="form-control" rows="4">{{ $config->instructions }}</textarea></div>
<div class="col-md-6"><label class="form-label">After-hours message</label><textarea name="after_hours_message" class="form-control" rows="3">{{ $config->after_hours_message }}</textarea></div>
<div class="col-md-6"><label class="form-label">Timezone</label><input name="timezone" class="form-control" value="{{ $config->timezone }}"></div>
<div class="col-md-4"><label class="form-label">Notification email</label><input name="notification_email" type="email" class="form-control" value="{{ $config->notification_email }}"></div>
<div class="col-md-4"><label class="form-label">Escalation email</label><input name="escalation_email" type="email" class="form-control" value="{{ $config->escalation_email }}"></div>
<div class="col-md-4"><label class="form-label">Escalation phone</label><input name="escalation_phone" class="form-control" value="{{ $config->escalation_phone }}"></div>
<div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="booking_enabled" value="1" @checked($config->booking_enabled)><label class="form-check-label">Allow AI to book appointments</label></div></div>
</div><button class="btn btn-primary mt-4">Save configuration</button></form></div></div>
<div class="row g-4">
<div class="col-lg-6"><div class="card"><div class="card-header fw-bold">Knowledge base</div><div class="card-body"><form method="POST" action="{{ route('receptionist.knowledge.store',$company) }}">@csrf<div class="row g-2"><div class="col-md-4"><select name="type" class="form-select"><option value="faq">FAQ</option><option value="service">Service</option><option value="policy">Policy</option><option value="general">General</option></select></div><div class="col-md-8"><input name="title" class="form-control" placeholder="e.g. Do you offer end-of-tenancy cleaning?"></div><div class="col-12"><textarea name="content" class="form-control" rows="3" placeholder="Accurate answer the AI can use"></textarea></div><div class="col-md-4"><input name="priority" type="number" class="form-control" value="50" min="1" max="100"></div><div class="col-md-8"><button class="btn btn-outline-primary">Add knowledge</button></div></div></form><hr>@forelse($knowledge as $item)<div class="border rounded p-3 mb-2"><div class="d-flex justify-content-between"><strong>{{ $item->title }}</strong><form method="POST" action="{{ route('receptionist.knowledge.destroy',[$company,$item]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></div><small class="text-muted">{{ strtoupper($item->type) }} · Priority {{ $item->priority }}</small><p class="mb-0 mt-2">{{ $item->content }}</p></div>@empty<p class="text-muted">No knowledge items yet.</p>@endforelse</div></div></div>
<div class="col-lg-6"><div class="card mb-4"><div class="card-header fw-bold">Appointment types</div><div class="card-body"><form method="POST" action="{{ route('receptionist.types.store',$company) }}">@csrf<div class="row g-2"><div class="col-7"><input name="name" class="form-control" placeholder="Consultation"></div><div class="col-5"><input name="duration_minutes" type="number" class="form-control" value="30"></div><div class="col-12"><input name="description" class="form-control" placeholder="Description"></div><div class="col-5"><input name="buffer_minutes" type="number" class="form-control" value="0"></div><div class="col-7"><button class="btn btn-outline-primary">Add type</button></div></div></form><hr>@foreach($types as $type)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $type->name }}</span><span>{{ $type->duration_minutes }} min</span></div>@endforeach</div></div>
<div class="card"><div class="card-header fw-bold">Availability windows</div><div class="card-body"><form method="POST" action="{{ route('receptionist.availability.store',$company) }}">@csrf<div class="row g-2"><div class="col-4"><select name="day_of_week" class="form-select">@foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i=>$day)<option value="{{ $i }}">{{ $day }}</option>@endforeach</select></div><div class="col-3"><input name="start_time" type="time" class="form-control" value="09:00"></div><div class="col-3"><input name="end_time" type="time" class="form-control" value="17:00"></div><div class="col-2"><button class="btn btn-outline-primary">Add</button></div></div></form><hr>@foreach($availability as $window)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][$window->day_of_week] }}</span><span>{{ substr($window->start_time,0,5) }} - {{ substr($window->end_time,0,5) }}</span></div>@endforeach</div></div></div>
</div>

<div class="card mt-4">
    <div class="card-header fw-bold">Widget security & embed</div>
    <div class="card-body">
        <p class="small text-muted">For production, list the exact website origins that are allowed to use this widget. Example: <code>https://www.example.co.uk</code>. Use one origin per line. Do not include a path such as <code>/contact</code>.</p>
        <form method="POST" action="{{ route('receptionist.update',$company) }}" class="mb-4">
            @csrf @method('PUT')
            <input type="hidden" name="tone" value="{{ $config->tone }}">
            <input type="hidden" name="display_name" value="{{ $config->display_name }}">
            <input type="hidden" name="greeting" value="{{ $config->greeting }}">
            <input type="hidden" name="instructions" value="{{ $config->instructions }}">
            <input type="hidden" name="timezone" value="{{ $config->timezone }}">
            <input type="hidden" name="enabled" value="{{ $config->enabled ? 1 : 0 }}">
            <input type="hidden" name="booking_enabled" value="{{ $config->booking_enabled ? 1 : 0 }}">
            <textarea name="services" class="d-none">{{ implode("\n",$config->services ?? []) }}</textarea>
            <textarea name="service_areas" class="d-none">{{ implode("\n",$config->service_areas ?? []) }}</textarea>
            <textarea name="business_hours" class="d-none">{{ implode("\n",$config->business_hours ?? []) }}</textarea>
            <textarea name="after_hours_message" class="d-none">{{ $config->after_hours_message }}</textarea>
            <input type="hidden" name="notification_email" value="{{ $config->notification_email }}">
            <input type="hidden" name="escalation_email" value="{{ $config->escalation_email }}">
            <input type="hidden" name="escalation_phone" value="{{ $config->escalation_phone }}">
            <label class="form-label">Allowed origins</label>
            <textarea name="allowed_origins" class="form-control font-monospace" rows="4" placeholder="https://www.example.co.uk&#10;https://example.co.uk">{{ implode("\n",$serviceOrigins) }}</textarea>
            <button class="btn btn-outline-primary mt-2">Save allowed origins</button>
        </form>
        <div class="mb-4">
            <label class="form-label">Public widget key</label>
            <div class="input-group">
                <input class="form-control font-monospace" value="{{ $widgetKey }}" readonly>
                <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(this.previousElementSibling.value).then(()=>{this.textContent='Copied';setTimeout(()=>this.textContent='Copy',1500)})">Copy</button>
            </div>
            <form method="POST" action="{{ route('receptionist.widget-key.regenerate',$company) }}" class="mt-2" onsubmit="return confirm('Regenerate the widget key? Existing embeds will stop working until replaced.');">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Regenerate key</button>
            </form>
        </div>
        <p class="text-muted mb-2">Embed this snippet just before <code>&lt;/body&gt;</code> on an authorized website:</p>
        <div class="input-group">
            <textarea id="receptionist-widget-code" class="form-control font-monospace" rows="3" readonly>&lt;script src="{{ url('/receptionist-widget.js') }}?company_id={{ $company->id }}&amp;site_key={{ $widgetKey }}" defer&gt;&lt;/script&gt;</textarea>
            <button type="button" class="btn btn-outline-primary" onclick="navigator.clipboard.writeText(document.getElementById('receptionist-widget-code').value).then(() => { this.textContent='Copied'; setTimeout(() => this.textContent='Copy', 1500); })">Copy</button>
        </div>
        <div class="mt-3 small text-muted">The widget now validates its public key, calling website origin, rate limit and API availability before processing chat.</div>
    </div>
</div>

@endsection