@extends('layouts.applicantHome')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <!-- Section Title -->
    <h2 class="text-xl font-semibold mb-4" style="color: #BD6F22;">My Applications</h2>

    <!-- Resume Upload Notice (only if no resume) -->
    @if(empty($resume) || !$resume->resume)
    <div class="border border-gray-300 rounded-md shadow-sm p-4 mb-6">
        <div class="flex items-start gap-2 mb-4">
            <span class="text-xl">⚠️</span>
            <div class="text-sm text-gray-800" style="color: #BD6F22;">
                <ul class="list-disc pl-4 space-y-1">
                    <li>Make sure your resume is in PDF format only.</li>
                    <li>Update your 201 files to boost your chances of getting hired. (ex. Certifications, etc.)</li>
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
                <span class="block mb-1 text-sm text-gray-700">
                    Please upload your resume (PDF only)
                </span>
                <input
                    type="file"
                    name="resume_file"
                    accept=".pdf"
                    required
                    class="w-full border border-gray-300 rounded px-3 py-2"
                >
                @error('resume_file')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </label>

            <button
                type="submit"
                class="px-6 py-2 text-white rounded"
                style="background-color: #BD6F22;"
            >
                Upload
            </button>
        </form>
    </div>
    @endif

    <!-- Resume Actions (if resume exists) -->
    @if(isset($resume) && $resume->resume)
    <div class="mb-6 flex items-center gap-4">
        <a
            href="{{ asset('storage/' . $resume->resume) }}"
            target="_blank"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
        >
            Show Resume
        </a>

        <form
            id="deleteForm"
            action="{{ route('applicant.application.destroy') }}"
            method="POST"
        >
            @csrf
            @method('DELETE')
            <button
                type="button"
                id="deleteResumeBtn"
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition"
            >
                Delete Resume
            </button>
        </form>
    </div>
    @endif

    <!-- Application Cards -->
    @forelse($applications ?? [] as $application)
        <div class="border border-gray-300 rounded-md shadow-md p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
            <div>
                <h3 class="text-md font-semibold" style="color: #BD6F22;">
                    {{ $application->job->job_title ?? 'No job title' }}
                </h3>
                <p class="text-sm text-gray-700 mb-2">
                    {{ $application->job->company_name ?? 'No company' }}
                </p>

                <p class="text-xs text-gray-500 mt-2">
                    Applied on: {{ optional($application->created_at)->format('F d, Y') ?? 'N/A' }}
                </p>

                @if($application->resume_snapshot)
                    <div class="flex items-center gap-2 mt-2">
                        <!-- View Resume Button -->
                        <a href="{{ $application->resume_snapshot_url }}" target="_blank"
                        class="inline-block bg-[#BD6F22] text-white text-sm px-4 py-2 rounded hover:bg-[#a75e1c] transition">
                            View Resume
                        </a>

                        <!-- Delete Application Button with Trash Icon -->
                        <form action="{{ route('applicant.application.delete', $application->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this application?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Delete Application">
                                <!-- Trash Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 
                                        00-1-1h-4a1 1 0 00-1 1v3m5 0H6"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endif

            </div>

            <div class="flex flex-col items-end gap-2">
                <p class="text-sm text-gray-700">
                    Status: {{ ucfirst($application->status ?? 'Pending') }}
                </p>
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-600 mt-6">You haven’t applied to any jobs yet.</p>
    @endforelse
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

<!-- Show SweetAlert after redirect -->
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        timer: 2500,
        showConfirmButton: false
    });
</script>
@endif
@endsection
