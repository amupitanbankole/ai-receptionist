@extends('layouts.app')

@section('title', 'Edit Email Template')
@section('page_title', 'Edit Email Template')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('email-templates.update', $template) }}">
            @csrf @method('PUT')
            @include('email_templates.form')
            <button class="btn btn-primary">Update Template</button>
            <a href="{{ route('email-templates.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection
