@extends(auth()->user()->role === 'applicant' ? 'layouts.applicantHome' : 'layouts.employeeHome')

@section('content')
    <x-shared.profile
        :user="$user"
        :experiences="$experiences"
        :updateRoute="auth()->user()->role === 'applicant' ? route('applicant.profile.update') : route('employee.profile.update')"
    />
@endsection
