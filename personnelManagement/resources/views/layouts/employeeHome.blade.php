@extends('layouts.base', ['title' => $title ?? 'Employee Dashboard'])

@php
    $menuItems = [
        ['img' => 'home.png', 'label' => 'Home', 'route' => 'employee.dashboard'],
        ['img' => 'user.png', 'label' => 'Profile', 'route' => 'employee.profile'],
        ['img' => 'application.png', 'label' => 'My Application', 'route' => 'employee.application'],
        ['img' => 'folder.png', 'label' => '201 Files', 'route' => 'employee.files'],
        ['img' => 'leaveForm.png', 'label' => 'Leave Form', 'route' => 'employee.leaveForm'],
        ['img' => 'settings.png', 'label' => 'Settings', 'route' => 'employee.settings'],
    ];
@endphp

@section('navbar')
    <x-shared.navbar :showRoleText="false" />
@endsection

@section('sidebar')
    <x-shared.sidebar :items="$menuItems" :currentRoute="Route::currentRouteName()" />
@endsection

@push('styles')
    <!-- Litepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css">
@endpush

@push('modals')
    <x-shared.loading-overlay />
@endpush

@push('scripts')
    <!-- Litepicker JS -->
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data('leaveForm', () => ({
                open: false,
                init() {
                    new Litepicker({
                        element: this.$refs.dateRange,
                        singleMode: false,
                        format: 'MM/DD/YYYY',
                        numberOfMonths: 2,
                        numberOfColumns: 2
                    });
                }
            }));
        });

        // Make overlay logic conditional
        window.allowSubmit = true; // default to true for non-confirm forms
    </script>
@endpush
