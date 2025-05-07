<!-- resources/views/errors/404.blade.php -->
@extends('layouts.applicantHome')

@section('content')
    <div class="text-center mt-5">
        <h1 class="display-4">404</h1>
        <p class="lead">Page not found.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
    </div>
@endsection
