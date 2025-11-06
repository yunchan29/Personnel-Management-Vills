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
@endpush
