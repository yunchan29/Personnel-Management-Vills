@extends('layouts.applicantHome')

@section('content')
<div class="max-w-7xl mx-auto p-6">

    <!-- Section Title -->
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">My Applications</h1>

    <!-- Resume Upload Notice (only if no resume) -->
@if(empty($resume) || !$resume->resume)
<div class="border border-gray-300 rounded-md shadow-sm p-4 mb-6 bg-white">
    <div class="flex items-start gap-2 mb-4">
        <span class="text-xl">⚠️</span>
        <div class="text-sm" style="color: #BD6F22;">
            <ul class="list-disc pl-4 space-y-1">
                <li>Make sure your resume is in PDF format only.</li>
                <li>Update your 201 files to boost your chances of getting hired. (e.g., Certifications)</li>
            </ul>
        </div>
    </div>

    <form
        action="{{ route('applicant.application.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="flex flex-col md:flex-row items-center gap-4"
    >
        @csrf

        <label class="w-full md:flex-1">
            <span class="block mb-1 text-sm font-medium text-gray-700">
                Upload your resume (PDF only)
            </span>
            <input
                type="file"
                name="resume_file"
                id="resumeInput"
                accept=".pdf"
                required
                class="w-full border border-gray-300 rounded px-3 py-2 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#BD6F22] file:text-white hover:file:bg-[#a75e1c] transition"
            >
            <p id="fileName" class="text-sm text-gray-600 mt-1"></p>

            @error('resume_file')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </label>

        <button
            type="submit"
            class="px-6 py-2 text-white rounded font-medium transition"
            style="background-color: #BD6F22;"
        >
            Upload
        </button>
    </form>
</div>
@endif

<!-- Resume Actions -->
@if(isset($resume) && $resume->resume)
@php
    $fileName = $resume->original_name ?? basename($resume->resume);
@endphp

<div class="mb-6 flex flex-col gap-2 bg-white p-4 rounded-md shadow-sm border border-gray-300">
    
    <!-- File Name -->
 <p class="text-sm text-gray-700 font-medium">
    Uploaded File: <span class="text-[#BD6F22]">{{ $fileName }}</span>
</p>


    <div class="flex flex-wrap items-center gap-4">
        <a
            href="{{ asset('storage/' . $resume->resume) }}"
            target="_blank"
            class="px-5 py-2 bg-[#BD6F22] text-white rounded hover:bg-[#a75e1c] transition font-medium"
        >
            Show Resume
        </a>

        <form id="deleteForm" action="{{ route('applicant.application.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <button
                type="button"
                id="deleteResumeBtn"
                class="px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition font-medium"
            >
                Delete Resume
            </button>
        </form>
    </div>
</div>
@endif



    

    <!-- Application Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($applications ?? [] as $application)
        <div class="border border-gray-300 rounded-lg shadow-md p-4 flex flex-col justify-between bg-white">
            <div>
                <h3 class="text-md font-semibold text-[#BD6F22]">
                    {{ $application->job->job_title ?? 'No job title' }}
                </h3>
                <p class="text-sm text-black font-semibold">
                    {{ $application->job->company_name ?? 'No company' }}
                </p>

                @if($application->resume_snapshot)
                <div class="mt-4">
                    <a
                        href="{{ $application->resume_snapshot_url }}"
                        target="_blank"
                        class="inline-block bg-[#BD6F22] text-white text-sm px-4 py-2 rounded hover:bg-[#a75e1c] transition">
                        View Resume
                    </a>
                </div>
                @endif

                <p class="text-xs text-gray-500 mt-2">
                    Applied on: {{ optional($application->created_at)->format('F d, Y') ?? 'N/A' }}
                </p>
            </div>

            <div class="flex flex-col items-end gap-2 mt-4">
                <span class="inline-block text-white text-sm px-4 py-2 rounded" style="background-color: #DD6161">
                    {{ ucfirst($application->status ?? 'To Review') }}
                </span>

                <form action="{{ route('applicant.application.delete', $application->id) }}"
                    method="POST" onsubmit="return confirm('Are you sure you want to delete this application?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 transition mt-2" title="Delete Application">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 
                                00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-600 mt-6 col-span-2">
            You haven’t applied to any jobs yet.
        </p>
        @endforelse
    </div>

</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Delete Resume with SweetAlert -->
<script>
document.getElementById('deleteResumeBtn')?.addEventListener('click', function (e) {
    e.preventDefault();

    Swal.fire({ 
        title: 'Are you sure?', 
        text: "Delete your existing resume?", 
        icon: 'warning', 
        showCancelButton: true, 
        confirmButtonColor: '#d33', 
        cancelButtonColor: '#3085d6', 
        confirmButtonText: 'Yes, delete it!', 
        reverseButtons: true 
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
});
</script>
<script>
    const resumeInput = document.getElementById('resumeInput');
    const fileName = document.getElementById('fileName');

    resumeInput?.addEventListener('change', function () {
        if (this.files && this.files.length > 0) {
            fileName.textContent = `Selected File: ${this.files[0].name}`;
        } else {
            fileName.textContent = '';
        }
    });
</script>

<!-- Success SweetAlert -->
@if(session('success'))
<script>
    Swal.fire({ icon: 'success', title: 'Success', text: '{{ session('success') }}', timer: 2500, showConfirmButton: false });
</script>
@endif
@endsection
