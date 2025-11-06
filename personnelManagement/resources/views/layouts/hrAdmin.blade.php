@extends('layouts.base', ['title' => $title ?? 'HR Admin Dashboard'])

@php
    $menuItems = [
        ['img' => 'home.png', 'label' => 'Home', 'route' => 'hrAdmin.dashboard'],
        ['img' => 'search.png', 'label' => 'Job Posting', 'route' => 'hrAdmin.jobPosting'],
        ['img' => 'application.png', 'label' => 'Applications', 'route' => 'hrAdmin.application'],
        ['img' => 'leaveForm.png', 'label' => 'Leave Forms', 'route' => 'hrAdmin.leaveForm'],
        ['img' => 'employees.png', 'label' => 'Employees', 'route' => 'hrAdmin.employees'],
        ['img' => 'archive.png', 'label' => 'Archive', 'route' => 'hrAdmin.archive.index'],
        ['img' => 'settings.png', 'label' => 'Settings', 'route' => 'hrAdmin.settings'],
    ];
@endphp

@section('navbar')
    <x-shared.navbar :showRoleText="true" />
@endsection

@section('sidebar')
    <x-shared.sidebar :items="$menuItems" :currentRoute="Route::currentRouteName()" />
@endsection
