@extends('layouts.app')

@section('title', 'Simulate Inbound Reply')
@section('page_title', 'Simulate Inbound Reply')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <a href="{{ route('sales-conversations.index') }}" class="text-decoration-none text-muted">← Back to Conversations</a>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body p-4">
                <h3 class="fw-bold">Test the Phase 6 AI reply workflow</h3>
                <p class="text-muted">This stores an inbound email, classifies it and generates an AI reply suggestion.</p>
                @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
                <form method="POST" action="{{ route('sales-conversations.simulate.store') }}">
                    @csrf
                    <div class="mb-3"><label class="form-label">From email</label><input name="from_address" type="email" class="form-control" value="{{ old('from_address') }}" required></div>
                    <div class="mb-3"><label class="form-label">To email</label><input name="to_address" type="email" class="form-control" value="{{ old('to_address', config('mail.from.address')) }}"></div>
                    <div class="mb-3"><label class="form-label">Subject</label><input name="subject" class="form-control" value="{{ old('subject', 'Re: AI receptionist') }}"></div>
                    <div class="mb-3"><label class="form-label">Reply</label><textarea name="body" rows="8" class="form-control" required>{{ old('body', "Hi,

Thanks for reaching out. This sounds interesting. How does the AI receptionist handle calls when we are already busy?

Best") }}</textarea></div>
                    <button class="btn btn-primary">Capture & Analyze Reply</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection