@extends('layouts.hrAdmin')

@section('content')
    <x-shared.profile
        :user="$user"
        :experiences="$experiences"
        :updateRoute="route('hrAdmin.profile.update')"
    />
@endsection
