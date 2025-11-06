@props(['status' => 'pending', 'label' => null])

@php
    $statusMap = [
        // Applicant statuses
        'pending' => ['label' => 'Pending', 'class' => 'bg-gray-200 text-gray-800'],
        'for_interview' => ['label' => 'For Interview', 'class' => 'bg-yellow-200 text-yellow-800'],
        'interviewed' => ['label' => 'Passed', 'class' => 'bg-green-200 text-green-800'],
        'declined' => ['label' => 'Failed', 'class' => 'bg-red-200 text-red-800'],
        'for_training' => ['label' => 'For Training', 'class' => 'bg-blue-200 text-blue-800'],
        'trained' => ['label' => 'Trained', 'class' => 'bg-indigo-200 text-indigo-800'],
        'hired' => ['label' => 'Hired', 'class' => 'bg-green-300 text-green-900'],

        // Leave form statuses
        'approved' => ['label' => 'Approved', 'class' => 'bg-green-200 text-green-800'],
        'rejected' => ['label' => 'Rejected', 'class' => 'bg-red-200 text-red-800'],

        // General statuses
        'active' => ['label' => 'Active', 'class' => 'bg-green-200 text-green-800'],
        'inactive' => ['label' => 'Inactive', 'class' => 'bg-gray-200 text-gray-800'],
    ];

    $statusData = $statusMap[$status] ?? $statusMap['pending'];
    $displayLabel = $label ?? $statusData['label'];
    $classes = $statusData['class'];
@endphp

<span {{ $attributes->merge(['class' => "px-2 py-1 text-xs font-semibold rounded-full $classes"]) }}>
    {{ $displayLabel }}
</span>
