@extends(auth()->user()->role === 'applicant' ? 'layouts.applicantHome' : 'layouts.employeeHome')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">My Applications</h1>

    <div class="border border-gray-300 rounded-md shadow-sm p-6 mb-6 bg-white">
        <div class="flex items-start gap-3 mb-4">
            <div class="text-xl text-[#BD6F22]">⚠️</div>
            <div class="text-sm text-[#BD6F22]">
                <ul class="list-disc pl-4 space-y-1">
                    <li>Make sure your resume is in PDF format only (Max: 25 MB).</li>
                    <li>Update your 201 files to boost your chances of getting hired. (e.g., Certifications)</li>
                </ul>
            </div>
        </div>

        <form action="{{ auth()->user()->role === 'applicant' ? route('applicant.application.store') : route('employee.application.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-start md:items-end gap-4">
            @csrf
            <label class="w-full md:flex-1">
                <span class="block mb-1 text-sm font-medium text-gray-700">
                    {{ isset($resume) && $resume->resume ? 'Update your resume' : 'Upload your resume (PDF only)' }}
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

            <button type="submit" class="px-6 py-2 bg-[#BD6F22] text-white rounded font-medium hover:bg-[#a75e1c] transition">
                {{ isset($resume) && $resume->resume ? 'Update Resume' : 'Upload' }}
            </button>
        </form>

        @if(isset($resume) && $resume->resume)
            @php
                $fileName = $resume->original_name ?? basename($resume->resume);
            @endphp

            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="text-sm">
                        <p class="font-medium text-gray-700">Current Uploaded Resume:</p>
                        <p class="text-[#BD6F22]">{{ $fileName }}</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            type="button"
                            onclick="openResumeModal('{{ asset('storage/' . $resume->resume) }}')"
                            class="px-4 py-2 bg-[#BD6F22] text-white rounded hover:bg-[#a75e1c] transition text-sm"
                        >
                            View Resume
                        </button>

                        <form id="deleteForm" action="{{ auth()->user()->role === 'applicant' ? route('applicant.application.destroy') : route('employee.application.destroy') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button
                                type="button"
                                id="deleteResumeBtn"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm confirm-delete"
                                data-title="Are you sure?"
                                data-text="Delete your existing resume?"
                            >
                                Delete Resume
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse($applications ?? [] as $application)
        @php
            $statusLabels = [
                'For_interview'           => 'For Interview',
                'scheduled_for_training'  => 'Scheduled for Training',
                'passed'                  => 'Training Passed',
                'declined'                => 'Declined',
                'fail_interview'          => 'Failed Interview',
            ];

            $displayStatus = $statusLabels[$application->status]
                ?? ucfirst(str_replace('_', ' ', $application->status ?? 'To Review'));

            $isInactive = in_array($application->status, ['declined', 'fail_interview']);
        @endphp

        <div class="border border-gray-300 rounded-lg shadow-md p-4 flex flex-col justify-between
            {{ $isInactive ? 'bg-gray-100 opacity-70' : 'bg-white' }}">

            <div>
                <h3 class="text-md font-semibold {{ $isInactive ? 'text-gray-500' : 'text-[#BD6F22]' }}">
                    {{ $application->job->job_title ?? 'No job title' }}
                </h3>
                <p class="text-sm font-semibold {{ $isInactive ? 'text-gray-500' : 'text-black' }}">
                    {{ $application->job->company_name ?? 'No company' }}
                </p>

                @if($application->resume_snapshot)
                    <div class="mt-4">
                        <a href="{{ $application->resume_snapshot_url }}"
                           target="_blank"
                           class="inline-block bg-[#BD6F22] text-white text-sm px-4 py-2 rounded hover:bg-[#a75e1c] transition
                           {{ $isInactive ? 'pointer-events-none opacity-50' : '' }}">
                            View Resume
                        </a>
                    </div>
                @endif

                <p class="text-xs {{ $isInactive ? 'text-gray-400' : 'text-gray-500' }} mt-2">
                    Applied on: {{ optional($application->created_at)->format('F d, Y') ?? 'N/A' }}
                </p>
            </div>

            <div class="flex flex-col items-end gap-2 mt-4">
                <span class="inline-block text-white text-sm px-4 py-2 rounded"
                      style="background-color: #DD6161">
                    {{ $displayStatus }}
                </span>

                @if(auth()->user()->role === 'applicant')
                {{-- Only allow delete if not interview, training, or passed --}}
                @if(!in_array($application->status, ['For_interview', 'scheduled_for_training', 'passed','declined', 'fail_interview']))
                    <form action="{{ route('applicant.application.delete', $application->id) }}" method="POST" class="delete-application-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="deleteApplicationBtn confirm-delete text-red-500 hover:text-red-700 transition mt-2"
                                data-title="Are you sure?"
                                data-text="You are about to delete this job application.">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                      00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                            </svg>
                        </button>
                    </form>
                @endif
                @endif
            </div>
        </div>
    @empty
        <p class="text-gray-500">{{ auth()->user()->role === 'applicant' ? 'No applications found.' : 'You haven\'t applied to any jobs yet.' }}</p>
    @endforelse
</div>


<x-shared.resume-modal />

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/shared/confirmations.js') }}"></script>

@if(session('success'))
<script>
    showSuccess('Success', '{{ session('success') }}');
</script>
@endif

@endsection
