@extends(auth()->user()->role === 'hrAdmin' ? 'layouts.hrAdmin' : 'layouts.hrStaff')

@section('content')
    <x-shared.settings />
@endsection
