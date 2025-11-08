@extends(auth()->user()->role === 'applicant' ? 'layouts.applicantHome' : 'layouts.employeeHome')

@section('content')

@if(auth()->user()->role === 'applicant')
    @php
        $selectedIndustry = request('industry') ?? auth()->user()->job_industry;
    @endphp

    <x-applicant.job-search-section
        :jobs="$jobs"
        :industry="$industry"
        :resume="$resume"
        :appliedJobIds="$appliedJobIds"
    />
@else
    <x-shared.calendar-widget />
@endif

@endsection
