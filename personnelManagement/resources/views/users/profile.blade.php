@extends(auth()->user()->role === 'applicant' ? 'layouts.applicantHome' : 'layouts.employeeHome')

@section('content')
    <x-shared.profile
        :user="$user"
        :experiences="$experiences"
    />
@endsection
