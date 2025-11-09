@extends('layouts.base', ['title' => $title ?? 'Applicant Dashboard'])

@php
    $menuItems = [
        ['img' => 'home.png', 'label' => 'Home', 'route' => 'applicant.dashboard'],
        ['img' => 'user.png', 'label' => 'Profile', 'route' => 'applicant.profile'],
        ['img' => 'application.png', 'label' => 'My Application', 'route' => 'applicant.application'],
        ['img' => 'folder.png', 'label' => '201 Files', 'route' => 'applicant.files'],
        ['img' => 'settings.png', 'label' => 'Settings', 'route' => 'applicant.settings'],
    ];
@endphp

@section('navbar')
    <x-shared.navbar :showRoleText="false" />
@endsection

@section('sidebar')
    <x-shared.sidebar :items="$menuItems" :currentRoute="Route::currentRouteName()" :showTooltips="true" />
@endsection

@push('modals')
    <x-shared.loading-overlay />
    {{-- Debug: Profile Status --}}
    {{-- @php
        $profileComplete = auth()->user()->is_profile_complete;
        $isIncomplete = !$profileComplete;
        \Log::info('Profile Debug', [
            'is_profile_complete' => $profileComplete,
            'isIncomplete' => $isIncomplete,
            'user_id' => auth()->id()
        ]);
    @endphp --}}

    {{-- Complete Profile Modal - Shows on all pages if profile is incomplete --}}
    <x-shared.complete-profile-modal
        :isIncomplete="!auth()->user()->is_profile_complete"
        :profileRoute="route('applicant.profile')"
        :settingsRoute="route('applicant.settings')"
        title="Complete Your Profile"
        message="Please complete your profile to apply for jobs and access all features."
        buttonText="Go to Profile"
    />
@endpush
