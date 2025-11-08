@extends(auth()->user()->role === 'HR Admin' ? 'layouts.hrAdmin' : 'layouts.hrStaff')

@section('content')
    <x-shared.settings />
@endsection
