@extends('layouts.app')

@section('title', 'New Email Template')
@section('page_title', 'New Email Template')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('email-templates.store') }}">
            @csrf
            @include('email_templates.form')
            <button class="btn btn-primary">Save Template</button>
            <a href="{{ route('email-templates.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection
