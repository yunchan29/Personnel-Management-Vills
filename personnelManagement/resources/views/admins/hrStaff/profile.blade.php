@extends('layouts.hrStaff')

@section('content')
    <x-shared.profile
        :user="$user"
        :experiences="$experiences"
    />
@endsection
