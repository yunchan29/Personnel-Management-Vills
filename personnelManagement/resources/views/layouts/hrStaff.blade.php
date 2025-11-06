@extends('layouts.base', ['title' => $title ?? 'HR Staff Dashboard'])

@php
    $menuItems = [
        ['img' => 'home.png', 'label' => 'Home', 'route' => 'hrStaff.dashboard'],
        ['img' => 'employees.png', 'label' => 'Employees', 'route' => 'hrStaff.employees'],
        ['img' => 'briefcase.png', 'label' => 'Performance Evaluation', 'route' => 'hrStaff.perfEval'],
        ['img' => 'leaveForm.png', 'label' => 'Leave Forms', 'route' => 'hrStaff.leaveForm'],
        ['img' => 'archive.png', 'label' => 'Archive', 'route' => 'hrStaff.archive.index'],
        ['img' => 'settings.png', 'label' => 'Settings', 'route' => 'hrStaff.settings'],
    ];
@endphp

@section('navbar')
    <x-shared.navbar :showRoleText="true" />
@endsection

@section('sidebar')
    <x-shared.sidebar :items="$menuItems" :currentRoute="Route::currentRouteName()" />
@endsection
