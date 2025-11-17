@extends(auth()->user()->role === 'applicant' ? 'layouts.applicantHome' : 'layouts.employeeHome')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    @php
        $hasResume = isset($resume) && $resume->resume;
        $fileName = $hasResume ? ($resume->original_name ?? basename($resume->resume)) : null;
    @endphp

    <div class="border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-6 bg-white">
        <!-- Card Header -->
        <div class="bg-gradient-to-br from-orange-50 to-white px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="bg-[#BD6F22] text-white rounded-lg p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Resume Management</h2>
                    <p class="text-sm text-gray-600">{{ $hasResume ? 'Update or manage your resume' : 'Upload your resume to start applying' }}</p>
                </div>
            </div>
        </div>

        @if($hasResume)
            <!-- Current Resume Display -->
            <div class="px-6 py-5 bg-green-50 border-b border-green-100">
                <div class="flex items-start gap-4">
                    <div class="bg-green-100 text-green-600 rounded-lg p-3 mt-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-sm font-semibold text-green-800">Resume Uploaded Successfully</h3>
                            <span class="px-2 py-0.5 bg-green-200 text-green-800 rounded-full text-xs font-medium">Active</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-green-700 mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-medium">{{ $fileName }}</span>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button
                                type="button"
                                onclick="openResumeModal('{{ asset('storage/' . $resume->resume) }}')"
                                class="inline-flex items-center gap-2 bg-white border-2 border-green-600 text-green-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-green-600 hover:text-white transition-all duration-200 shadow-sm"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Resume
                            </button>

                            <button
                                type="button"
                                onclick="toggleUpdateForm()"
                                class="inline-flex items-center gap-2 bg-[#BD6F22] text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-[#a75e1c] transition-all duration-200 shadow-sm"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Update Resume
                            </button>

                            <form id="deleteForm" action="{{ auth()->user()->role === 'applicant' ? route('applicant.application.destroy') : route('employee.application.destroy') }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="button"
                                    id="deleteResumeBtn"
                                    class="inline-flex items-center gap-2 bg-white border-2 border-red-600 text-red-600 text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm confirm-delete"
                                    data-title="Are you sure?"
                                    data-text="Delete your existing resume?"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1 1v3M9 7h6"></path>
                                    </svg>
                                    Delete Resume
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Form (Hidden by default) -->
            <div id="updateResumeForm" class="hidden px-6 py-5 bg-blue-50 border-b border-blue-100">
                <div class="flex items-start gap-3 mb-4">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Updating Your Resume</p>
                        <p>Upload a new PDF file to replace your current resume. This will update your resume for all future applications.</p>
                    </div>
                </div>

                <form action="{{ auth()->user()->role === 'applicant' ? route('applicant.application.store') : route('employee.application.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex flex-col md:flex-row items-start md:items-end gap-4">
                        <label class="w-full md:flex-1">
                            <span class="block mb-2 text-sm font-semibold text-gray-700">Select New Resume (PDF only)</span>
                            <input
                                type="file"
                                name="resume_file"
                                id="resumeInputUpdate"
                                accept=".pdf"
                                required
                                class="w-full border-2 border-blue-300 rounded-lg px-3 py-2.5 bg-white file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#BD6F22] file:text-white hover:file:bg-[#a75e1c] file:rounded-md transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            >
                            <p id="fileNameUpdate" class="text-xs text-gray-600 mt-2"></p>

                            @error('resume_file')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </label>

                        <div class="flex gap-2">
                            <button type="submit" class="px-6 py-2.5 bg-[#BD6F22] text-white rounded-lg font-semibold hover:bg-[#a75e1c] transition-all duration-200 shadow-sm">
                                Upload New Resume
                            </button>
                            <button type="button" onclick="toggleUpdateForm()" class="px-6 py-2.5 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200 shadow-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <!-- No Resume - Upload Form -->
            <div class="px-6 py-5">
                <div class="flex items-start gap-3 mb-5 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="text-sm text-amber-800">
                        <p class="font-semibold mb-2">Important Information</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Make sure your resume is in <strong>PDF format only</strong> (Max: 25 MB)</li>
                            <li>Update your 201 files to boost your chances of getting hired (e.g., Certifications)</li>
                            <li>Your resume will be attached to all job applications you submit</li>
                        </ul>
                    </div>
                </div>

                <form action="{{ auth()->user()->role === 'applicant' ? route('applicant.application.store') : route('employee.application.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="w-full md:flex-1">
                            <label class="block mb-2 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Upload Your Resume (PDF only)
                            </label>
                            <div class="flex gap-4">
                                <input
                                    type="file"
                                    name="resume_file"
                                    id="resumeInput"
                                    accept=".pdf"
                                    required
                                    class="flex-1 border-2 border-gray-300 rounded-lg px-3 py-2.5 bg-white file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#BD6F22] file:text-white hover:file:bg-[#a75e1c] file:rounded-md transition focus:border-[#BD6F22] focus:ring-2 focus:ring-orange-100"
                                >
                                <button type="submit" class="md:hidden px-8 py-2.5 bg-[#BD6F22] text-white rounded-lg font-semibold hover:bg-[#a75e1c] transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2 whitespace-nowrap">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Upload Resume
                                </button>
                            </div>
                            <p id="fileName" class="text-xs text-gray-600 mt-2"></p>

                            @error('resume_file')
                                <p class="text-red-600 text-sm mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="hidden md:block md:self-end">
                            <button type="submit" class="px-8 py-2.5 bg-[#BD6F22] text-white rounded-lg font-semibold hover:bg-[#a75e1c] transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2 whitespace-nowrap">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Upload Resume
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- Tips Section -->
        <div class="px-6 py-4 bg-gray-50">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[#BD6F22] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-700 mb-1">Pro Tips</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li class="flex items-start gap-1.5">
                            <span class="text-[#BD6F22] mt-0.5">•</span>
                            <span>Keep your resume updated and highlight your most recent achievements</span>
                        </li>
                        <li class="flex items-start gap-1.5">
                            <span class="text-[#BD6F22] mt-0.5">•</span>
                            <span>Tailor your resume to match the job requirements for better chances</span>
                        </li>
                        <li class="flex items-start gap-1.5">
                            <span class="text-[#BD6F22] mt-0.5">•</span>
                            <span>Make sure all contact information is current and professional</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File input display logic for initial upload
        document.getElementById('resumeInput')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || '';
            const fileDisplay = document.getElementById('fileName');
            if (fileDisplay) {
                fileDisplay.textContent = fileName ? `Selected: ${fileName}` : '';
                fileDisplay.className = fileName ? 'text-xs text-green-600 mt-2 font-medium' : 'text-xs text-gray-600 mt-2';
            }
        });

        // File input display logic for update
        document.getElementById('resumeInputUpdate')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || '';
            const fileDisplay = document.getElementById('fileNameUpdate');
            if (fileDisplay) {
                fileDisplay.textContent = fileName ? `Selected: ${fileName}` : '';
                fileDisplay.className = fileName ? 'text-xs text-green-600 mt-2 font-medium' : 'text-xs text-gray-600 mt-2';
            }
        });

        // Toggle update form
        function toggleUpdateForm() {
            const form = document.getElementById('updateResumeForm');
            if (form) {
                form.classList.toggle('hidden');
            }
        }
    </script>

    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">My Applications</h1>

    @php
        use App\Enums\ApplicationStatus;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($applications ?? [] as $application)
        @php
            $displayStatus = $application->status_label ?? 'To Review';
            $isInactive = $application->status ? $application->status->isFailed() : false;
        @endphp

        <div class="border rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl
            {{ $isInactive ? 'border-red-200 bg-gray-50' : 'border-gray-200 bg-white hover:border-[#BD6F22]' }}">

            <!-- Card Header with Status Badge -->
            <div class="relative px-5 pt-5 pb-3 {{ $isInactive ? 'bg-gradient-to-br from-gray-100 to-gray-50' : 'bg-gradient-to-br from-orange-50 to-white' }}">
                <div class="absolute top-4 right-4">
                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm {{ $application->status_badge_class ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $displayStatus }}
                    </span>
                </div>

                <div class="pr-24">
                    <h3 class="text-lg font-bold leading-tight {{ $isInactive ? 'text-gray-600' : 'text-gray-900' }}">
                        {{ $application->job->job_title ?? 'No job title' }}
                    </h3>
                    <p class="text-sm font-medium mt-1.5 flex items-center gap-1.5 {{ $isInactive ? 'text-gray-500' : 'text-gray-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ $application->job->company_name ?? 'No company' }}
                    </p>
                </div>
            </div>

            <!-- Card Body -->
            <div class="px-5 py-4 space-y-3">
                <!-- Application Date -->
                <div class="flex items-center gap-2 text-xs {{ $isInactive ? 'text-gray-500' : 'text-gray-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium">Applied:</span>
                    <span>{{ optional($application->created_at)->format('M d, Y') ?? 'N/A' }}</span>
                </div>

                <!-- Resume Snapshot -->
                @if($application->resume_snapshot)
                    <div class="pt-2">
                        <a href="{{ $application->resume_snapshot_url }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-white border-2 border-[#BD6F22] text-[#BD6F22] text-sm font-medium px-4 py-2 rounded-lg hover:bg-[#BD6F22] hover:text-white transition-all duration-200 shadow-sm
                           {{ $isInactive ? 'pointer-events-none opacity-40' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View Resume
                        </a>
                    </div>
                @endif
            </div>

            <!-- Card Footer - Actions -->
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-3">
                <button type="button"
                        onclick="openStatusModal({{ $application->id }})"
                        class="flex items-center gap-1.5 text-[#BD6F22] hover:text-[#a05d1a] text-sm font-semibold transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    Track Status
                </button>

                @if(auth()->user()->role === 'applicant')
                {{-- Only allow delete if not in advanced stages or terminal --}}
                @if($application->status && !$application->isTerminal() && !in_array($application->status, [
                    ApplicationStatus::FOR_INTERVIEW,
                    ApplicationStatus::INTERVIEWED,
                    ApplicationStatus::SCHEDULED_FOR_TRAINING,
                    ApplicationStatus::TRAINED,
                    ApplicationStatus::FOR_EVALUATION,
                    ApplicationStatus::PASSED_EVALUATION
                ]))
                    <form action="{{ route('applicant.application.delete', $application->id) }}" method="POST" class="delete-application-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="deleteApplicationBtn confirm-delete flex items-center gap-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 px-3 py-2 rounded-lg transition-all duration-200"
                                data-title="Are you sure?"
                                data-text="You are about to delete this job application.">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                      00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                            </svg>
                            <span class="text-sm font-medium">Delete</span>
                        </button>
                    </form>
                @endif
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-500 font-medium">{{ auth()->user()->role === 'applicant' ? 'No applications found.' : 'You haven\'t applied to any jobs yet.' }}</p>
            <p class="text-gray-400 text-sm mt-1">Your job applications will appear here</p>
        </div>
    @endforelse
</div>


<x-shared.resume-modal />

<!-- Application Status Progress Modal -->
<div id="statusModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-5xl w-full p-6 shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-[#BD6F22]" id="modalJobTitle">Application Status</h2>
            <button onclick="closeStatusModal()" class="text-gray-500 hover:text-black text-2xl">&times;</button>
        </div>

        <div id="statusContent">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/shared/confirmations.js') }}"></script>

<script>
// Application data for status modal
const applicationsData = @json($applications ?? []);

function openStatusModal(applicationId) {
    const application = applicationsData.find(app => app.id === applicationId);
    if (!application) return;

    document.getElementById('modalJobTitle').textContent = application.job?.job_title || 'Application Status';

    const statusContent = document.getElementById('statusContent');
    statusContent.innerHTML = generateStatusTimeline(application);

    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

function generateStatusTimeline(application) {
    const currentStatus = application.status;

    // Define the hiring process stages
    const stages = [
        {
            key: 'pending',
            statuses: ['pending'],
            title: 'Application',
            icon: '📝',
            description: 'Application received and under review'
        },
        {
            key: 'approved',
            statuses: ['approved', 'for_interview'],
            title: 'Approved',
            icon: '✅',
            description: 'Application approved for interview'
        },
        {
            key: 'interview',
            statuses: ['interviewed'],
            title: 'Interview',
            icon: '💼',
            description: 'Interview completed'
        },
        {
            key: 'training',
            statuses: ['scheduled_for_training', 'trained'],
            title: 'Training',
            icon: '📚',
            description: 'Training in progress or completed'
        },
        {
            key: 'evaluation',
            statuses: ['for_evaluation', 'passed_evaluation'],
            title: 'Evaluation',
            icon: '📊',
            description: 'Performance evaluation'
        },
        {
            key: 'hired',
            statuses: ['hired'],
            title: 'Hired',
            icon: '🎉',
            description: 'Successfully hired'
        }
    ];

    // Failed statuses
    const failedStatuses = ['declined', 'failed_interview', 'failed_evaluation', 'rejected'];
    const isFailed = failedStatuses.includes(currentStatus);

    let html = '<div class="space-y-6">';

    // Add job info
    html += `
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <p class="text-xs text-gray-500">Company</p>
                    <p class="font-medium text-gray-900">${application.job?.company_name || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Applied On</p>
                    <p class="font-medium text-gray-900">${formatDate(application.created_at)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Current Status</p>
                    <span class="inline-block font-medium px-2 py-1 rounded text-sm ${getStatusColorClass(currentStatus)}">${getStatusLabel(currentStatus)}</span>
                </div>
            </div>
        </div>
    `;

    // If failed, show failure message
    if (isFailed) {
        const failureMessages = {
            'declined': 'Your application was declined during the initial review.',
            'failed_interview': 'Unfortunately, you did not pass the interview stage.',
            'failed_evaluation': 'You did not pass the evaluation stage.',
            'rejected': 'Your application has been rejected.'
        };

        html += `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-2 text-red-700 mb-2">
                    <span class="text-2xl">❌</span>
                    <h3 class="font-semibold">Application Unsuccessful</h3>
                </div>
                <p class="text-sm text-red-600">${failureMessages[currentStatus] || 'Your application was not successful.'}</p>
            </div>
        `;
    }

    // Horizontal Timeline
    html += '<div class="py-8 px-2 sm:px-4">';
    html += '<div class="relative">';

    // Progress bar background
    html += '<div class="absolute top-7 left-0 right-0 h-1 bg-gray-300" style="z-index: 0;"></div>';

    // Progress bar foreground (completed portion)
    const stageIndex = stages.findIndex(s => s.statuses.includes(currentStatus));
    const progressPercentage = stageIndex >= 0 && !isFailed ? ((stageIndex) / (stages.length - 1)) * 100 : 0;
    html += `<div class="absolute top-7 left-0 h-1 bg-green-500 transition-all duration-500" style="width: ${progressPercentage}%; z-index: 1;"></div>`;

    html += '<div class="relative flex items-start justify-between" style="z-index: 10;">';

    stages.forEach((stage, index) => {
        const isActive = stage.statuses.includes(currentStatus);
        const isPassed = index < stageIndex;
        const isCurrent = isActive && !isFailed;

        html += `
            <div class="flex flex-col items-center relative" style="flex: 1;">
                <!-- Icon -->
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full flex items-center justify-center text-2xl sm:text-3xl mb-3 transition-all ${
                    isCurrent ? 'bg-[#BD6F22] ring-4 ring-[#BD6F22] ring-opacity-30 shadow-lg scale-110' :
                    isPassed ? 'bg-green-500 text-white shadow-md' :
                    'bg-gray-200 text-gray-400'
                }">
                    ${isPassed && !isCurrent ? '✓' : stage.icon}
                </div>

                <!-- Stage Title -->
                <h3 class="font-semibold text-xs sm:text-sm text-center mb-1 ${
                    isCurrent ? 'text-[#BD6F22]' :
                    isPassed ? 'text-green-700' :
                    'text-gray-400'
                }">${stage.title}</h3>

                <!-- Stage Description -->
                <p class="text-[10px] sm:text-xs text-center px-1 sm:px-2 ${isCurrent || isPassed ? 'text-gray-600' : 'text-gray-400'}" style="max-width: 120px; line-height: 1.3;">
                    ${stage.description}
                </p>

                <!-- Status Badge -->
                <div class="mt-2 min-h-[24px]">
                    ${isCurrent ? '<span class="text-[10px] sm:text-xs font-medium px-2 sm:px-3 py-1 bg-[#BD6F22] text-white rounded-full shadow">Current</span>' : ''}
                </div>
            </div>
        `;
    });

    html += '</div>';
    html += '</div>';
    html += '</div>';

    // Additional Details Section
    html += '<div class="space-y-4">';

    // Interview Details
    if (application.interview && currentStatus !== 'pending') {
        html += `
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h4 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                    <span class="text-lg">💼</span>
                    Interview Details
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    ${application.interview.scheduled_at ? `
                        <div>
                            <p class="text-xs text-blue-700">Scheduled Date & Time</p>
                            <p class="text-sm font-medium text-blue-900">${formatDateTime(application.interview.scheduled_at)}</p>
                        </div>
                    ` : ''}
                    ${application.interview.location ? `
                        <div>
                            <p class="text-xs text-blue-700">Location</p>
                            <p class="text-sm font-medium text-blue-900">${application.interview.location}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    // Training Schedule Details
    if (application.training_schedule && ['scheduled_for_training', 'trained', 'for_evaluation', 'passed_evaluation'].includes(currentStatus)) {
        html += `
            <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                <h4 class="font-semibold text-purple-900 mb-3 flex items-center gap-2">
                    <span class="text-lg">📚</span>
                    Training Schedule
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    ${application.training_schedule.start_date ? `
                        <div>
                            <p class="text-xs text-purple-700">Start Date</p>
                            <p class="text-sm font-medium text-purple-900">${formatDate(application.training_schedule.start_date)}</p>
                        </div>
                    ` : ''}
                    ${application.training_schedule.end_date ? `
                        <div>
                            <p class="text-xs text-purple-700">End Date</p>
                            <p class="text-sm font-medium text-purple-900">${formatDate(application.training_schedule.end_date)}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    // Evaluation Scores
    if (application.evaluation && ['for_evaluation', 'passed_evaluation', 'failed_evaluation', 'hired'].includes(currentStatus)) {
        const eval = application.evaluation;
        const totalScore = (parseFloat(eval.attendance_score || 0) +
                           parseFloat(eval.attitude_score || 0) +
                           parseFloat(eval.performance_score || 0)) / 3;
        const passed = eval.result === 'Pass';

        html += `
            <div class="p-4 ${passed ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'} rounded-lg border">
                <h4 class="font-semibold ${passed ? 'text-green-900' : 'text-red-900'} mb-3 flex items-center gap-2">
                    <span class="text-lg">📊</span>
                    Evaluation Scores
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-xs ${passed ? 'text-green-700' : 'text-red-700'}">Attendance</p>
                        <p class="text-2xl font-bold ${passed ? 'text-green-900' : 'text-red-900'}">${eval.attendance_score || 'N/A'}</p>
                        <p class="text-xs text-gray-500">out of 100</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs ${passed ? 'text-green-700' : 'text-red-700'}">Attitude</p>
                        <p class="text-2xl font-bold ${passed ? 'text-green-900' : 'text-red-900'}">${eval.attitude_score || 'N/A'}</p>
                        <p class="text-xs text-gray-500">out of 100</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs ${passed ? 'text-green-700' : 'text-red-700'}">Performance</p>
                        <p class="text-2xl font-bold ${passed ? 'text-green-900' : 'text-red-900'}">${eval.performance_score || 'N/A'}</p>
                        <p class="text-xs text-gray-500">out of 100</p>
                    </div>
                </div>
                <div class="text-center p-3 ${passed ? 'bg-green-100' : 'bg-red-100'} rounded">
                    <p class="text-xs ${passed ? 'text-green-700' : 'text-red-700'} mb-1">Average Score</p>
                    <p class="text-3xl font-bold ${passed ? 'text-green-900' : 'text-red-900'}">${totalScore.toFixed(2)}</p>
                    <p class="text-sm font-medium ${passed ? 'text-green-800' : 'text-red-800'} mt-2">
                        ${passed ? '✓ PASSED' : '✗ FAILED'}
                    </p>
                </div>
                ${eval.remarks ? `
                    <div class="mt-4 p-3 bg-white rounded border ${passed ? 'border-green-200' : 'border-red-200'}">
                        <p class="text-xs ${passed ? 'text-green-700' : 'text-red-700'} mb-1">Remarks</p>
                        <p class="text-sm ${passed ? 'text-green-900' : 'text-red-900'}">${eval.remarks}</p>
                    </div>
                ` : ''}
            </div>
        `;
    }

    html += '</div>';
    html += '</div>';
    return html;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}

function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });
}

function getStatusColorClass(status) {
    const colorMap = {
        'pending': 'bg-gray-100 text-gray-800',
        'approved': 'bg-green-100 text-green-800',
        'declined': 'bg-red-100 text-red-800',
        'for_interview': 'bg-yellow-100 text-yellow-800',
        'interviewed': 'bg-blue-100 text-blue-800',
        'failed_interview': 'bg-red-100 text-red-800',
        'scheduled_for_training': 'bg-blue-100 text-blue-800',
        'trained': 'bg-purple-100 text-purple-800',
        'for_evaluation': 'bg-purple-100 text-purple-800',
        'passed_evaluation': 'bg-green-100 text-green-800',
        'failed_evaluation': 'bg-red-100 text-red-800',
        'hired': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800'
    };
    return colorMap[status] || 'bg-gray-100 text-gray-800';
}

function getStatusLabel(status) {
    const labelMap = {
        'pending': 'Pending',
        'approved': 'Approved',
        'declined': 'Declined',
        'for_interview': 'For Interview',
        'interviewed': 'Interviewed',
        'failed_interview': 'Failed Interview',
        'scheduled_for_training': 'Scheduled for Training',
        'trained': 'Trained',
        'for_evaluation': 'For Evaluation',
        'passed_evaluation': 'Passed Evaluation',
        'failed_evaluation': 'Failed Evaluation',
        'hired': 'Hired',
        'rejected': 'Rejected'
    };
    return labelMap[status] || 'Pending';
}

// Close modal when clicking outside
document.getElementById('statusModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});
</script>

@if(session('success'))
<script>
    showSuccess('Success', '{{ session('success') }}');
</script>
@endif

@endsection
