@extends(auth()->user()->role === 'applicant' ? 'layouts.applicantHome' : 'layouts.employeeHome')

@section('content')

@if(auth()->user()->role === 'applicant')
    {{-- APPLICANT DASHBOARD --}}
    <div class="max-w-7xl mx-auto p-6">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, {{ auth()->user()->first_name }}!</h1>
            <p class="text-gray-600">Find your dream job and start your career journey</p>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Applications -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold">{{ count($appliedJobIds ?? []) }}</span>
                </div>
                <p class="text-sm font-medium text-blue-100">Total Applications</p>
            </div>

            <!-- Available Jobs -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold">{{ count($jobs ?? []) }}</span>
                </div>
                <p class="text-sm font-medium text-green-100">Available Jobs</p>
            </div>

            <!-- Resume Status -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold">{{ isset($resume) && $resume?->resume ? 'Active' : 'None' }}</span>
                </div>
                <p class="text-sm font-medium text-orange-100">Resume Status</p>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Jobs</label>
                    <input type="text"
                           id="jobSearch"
                           placeholder="Search by job title, company, or location..."
                           class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-[#BD6F22] focus:ring-2 focus:ring-orange-100 transition">
                </div>
                <div class="md:w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Industry</label>
                    <select onchange="window.location.href='?industry='+encodeURIComponent(this.value)" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-[#BD6F22] focus:ring-2 focus:ring-orange-100 transition">
                        @php
                            $currentIndustry = is_string($industry ?? null) ? $industry : '';
                            $userPreference = auth()->user()->job_industry;
                            $industries = [
                                "Accounting", "Administration", "Architecture", "Arts and Design",
                                "Automotive", "Banking and Finance", "Business Process Outsourcing (BPO)",
                                "Construction", "Customer Service", "Data and Analytics", "Education",
                                "Engineering", "Entertainment", "Environmental Services", "Food and Beverage",
                                "Government", "Healthcare", "Hospitality", "Human Resources",
                                "Information Technology", "Insurance", "Legal", "Logistics and Supply Chain",
                                "Manufacturing", "Marketing", "Media and Communications", "Nonprofit",
                                "Pharmaceuticals", "Public Relations", "Real Estate", "Retail", "Sales",
                                "Science and Research", "Skilled Trades", "Sports and Recreation",
                                "Telecommunications", "Tourism", "Transportation", "Utilities",
                                "Warehouse and Distribution", "Writing and Publishing"
                            ];
                        @endphp

                        <option value="">All Industries</option>

                        @if(!empty($userPreference) && is_string($userPreference))
                            <option value="{{ $userPreference }}" {{ $currentIndustry === $userPreference ? 'selected' : '' }} class="font-semibold bg-blue-50">
                                ⭐ {{ $userPreference }} (Your Preference)
                            </option>
                        @endif

                        <option disabled>──────────</option>

                        @foreach($industries as $industryOption)
                            @if($industryOption !== $userPreference)
                                <option value="{{ $industryOption }}" {{ $currentIndustry === $industryOption ? 'selected' : '' }}>
                                    {{ $industryOption }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

        </div>

        <!-- Job Listings -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Available Job Openings</h2>
        </div>

        <div id="jobGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($jobs ?? [] as $job)
                <div class="job-card border border-gray-200 rounded-xl shadow-lg overflow-hidden bg-white hover:shadow-xl transition-all duration-300 hover:border-[#BD6F22]"
                     data-title="{{ strtolower($job->job_title) }}"
                     data-company="{{ strtolower($job->company_name) }}"
                     data-location="{{ strtolower($job->location ?? '') }}">

                    <!-- Card Header -->
                    <div class="bg-gradient-to-br from-orange-50 to-white p-5 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $job->job_title }}</h3>
                        <div class="flex items-center gap-2 text-sm text-gray-700 mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="font-medium">{{ $job->company_name }}</span>
                        </div>
                        @if($job->location)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $job->location }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 space-y-3">
                        <!-- Qualifications -->
                        @if($job->qualifications)
                            <div>
                                <p class="text-xs font-semibold text-gray-700 mb-1">Qualifications:</p>
                                <div class="text-xs text-gray-600 max-h-20 overflow-hidden qualifications-content">
                                    {!! nl2br(e($job->qualifications)) !!}
                                </div>
                                @if(strlen($job->qualifications) > 100)
                                    <button type="button" class="text-xs text-[#BD6F22] hover:text-[#a75e1c] font-medium mt-1 toggle-qualifications">
                                        See More
                                    </button>
                                @endif
                            </div>
                        @endif

                        <!-- Vacancies & Deadline -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div class="flex items-center gap-1 text-xs text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="font-medium">{{ $job->vacancies }} positions</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                Posted {{ $job->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div class="text-xs text-gray-600">
                            <span class="font-medium">Deadline:</span> {{ \Carbon\Carbon::parse($job->apply_until)->format('M d, Y') }}
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="p-5 bg-gray-50 border-t border-gray-100">
                        @if(in_array($job->id, $appliedJobIds ?? []))
                            <button disabled class="w-full py-2.5 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed opacity-60">
                                Already Applied
                            </button>
                        @elseif(!isset($resume) || !$resume?->resume)
                            <button disabled class="w-full py-2.5 bg-gray-300 text-gray-600 rounded-lg font-semibold cursor-not-allowed" title="Upload resume first">
                                Upload Resume First
                            </button>
                        @else
                            <button type="button"
                                    class="apply-btn w-full py-2.5 bg-[#BD6F22] text-white rounded-lg font-semibold hover:bg-[#a75e1c] transition-all duration-200 shadow-sm hover:shadow-md"
                                    data-job-id="{{ $job->id }}">
                                Apply Now
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium text-lg">No job openings available at the moment</p>
                    <p class="text-gray-400 text-sm mt-2">Check back later for new opportunities</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- SweetAlert2 --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Search functionality
        const searchInput = document.getElementById('jobSearch');
        const jobCards = document.querySelectorAll('.job-card');

        searchInput?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            jobCards.forEach(card => {
                const title = card.dataset.title || '';
                const company = card.dataset.company || '';
                const location = card.dataset.location || '';

                if (title.includes(searchTerm) || company.includes(searchTerm) || location.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Toggle qualifications
        document.querySelectorAll('.toggle-qualifications').forEach(button => {
            button.addEventListener('click', function () {
                const content = this.previousElementSibling;
                if (content.classList.contains('max-h-20')) {
                    content.classList.remove('max-h-20', 'overflow-hidden');
                    this.textContent = 'See Less';
                } else {
                    content.classList.add('max-h-20', 'overflow-hidden');
                    this.textContent = 'See More';
                }
            });
        });

        // Apply functionality
        document.querySelectorAll('button.apply-btn').forEach(button => {
            button.addEventListener('click', function () {
                const jobId = this.dataset.jobId;
                const btn = this;

                Swal.fire({
                    title: 'Confirm Application',
                    text: "Are you sure you want to apply for this job?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#BD6F22',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, apply!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/applicant/apply/${jobId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({})
                        })
                        .then(async response => {
                            let data = {};

                            try {
                                data = await response.clone().json();
                            } catch (e) {
                                data.message = 'Unexpected response. Please try again.';
                            }

                            if (response.ok) {
                                Swal.fire(
                                    'Applied!',
                                    data.message || 'You have successfully applied for this job.',
                                    'success'
                                );
                                btn.disabled = true;
                                btn.textContent = 'Already Applied';
                                btn.classList.remove('bg-[#BD6F22]', 'hover:bg-[#a75e1c]');
                                btn.classList.add('bg-gray-400', 'cursor-not-allowed', 'opacity-60');
                            } else {
                                Swal.fire(
                                    'Application Failed',
                                    data.message || `Something went wrong (Error ${response.status})`,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message, 'error');
                        });
                    }
                });
            });
        });
    });
    </script>

@else
    {{-- EMPLOYEE DASHBOARD --}}
    <div class="flex gap-6 p-6 max-w-[1600px] mx-auto">
        <!-- Main Content Area (Left Side) -->
        <div class="flex-1 space-y-6">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-[#BD6F22] to-[#a75e1c] rounded-xl shadow-lg p-6 text-white">
                <h1 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->first_name }}!</h1>
                <p class="text-orange-100">Here's what's happening with your account today</p>
            </div>

            <!-- Requirements Section -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-br from-blue-50 to-white px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-500 text-white rounded-lg p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Requirements</h2>
                            <p class="text-sm text-gray-600">Documents pending submission</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if(isset($requirements) && count($requirements) > 0)
                        <div class="space-y-3">
                            @foreach($requirements as $requirement)
                                <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-200 rounded-lg hover:shadow-md transition">
                                    <div class="flex items-start gap-3">
                                        <div class="bg-amber-100 text-amber-600 rounded-lg p-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $requirement->title ?? 'Document Required' }}</p>
                                            <p class="text-sm text-gray-600 mt-1">{{ $requirement->description ?? 'Please submit this document' }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Due: {{ $requirement->deadline ?? 'As soon as possible' }}</p>
                                        </div>
                                    </div>
                                    <button class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition font-medium text-sm">
                                        Submit
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-600 font-medium">All requirements submitted!</p>
                            <p class="text-gray-400 text-sm mt-1">You're all caught up</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Leave Forms Status Section -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-br from-purple-50 to-white px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="bg-purple-500 text-white rounded-lg p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Leave Requests</h2>
                            <p class="text-sm text-gray-600">Your leave application status</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if(isset($leaveForms) && count($leaveForms) > 0)
                        <div class="space-y-3">
                            @foreach($leaveForms as $leave)
                                @php
                                    $statusColors = [
                                        'pending' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-800', 'badge' => 'bg-yellow-100'],
                                        'approved' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'badge' => 'bg-green-100'],
                                        'rejected' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'badge' => 'bg-red-100'],
                                    ];
                                    $color = $statusColors[$leave->status] ?? $statusColors['pending'];
                                @endphp

                                <div class="flex items-center justify-between p-4 {{ $color['bg'] }} border {{ $color['border'] }} rounded-lg hover:shadow-md transition">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="px-3 py-1 {{ $color['badge'] }} {{ $color['text'] }} rounded-full text-xs font-semibold uppercase">
                                                {{ $leave->status }}
                                            </span>
                                            <span class="text-sm font-semibold text-gray-900">{{ $leave->leave_type }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700 mb-1">{{ $leave->date_range }}</p>
                                        <p class="text-xs text-gray-600">{{ $leave->about }}</p>
                                        <p class="text-xs text-gray-500 mt-2">Submitted: {{ $leave->created_at->format('M d, Y') }}</p>
                                    </div>
                                    @if($leave->status === 'pending')
                                        <div class="ml-4">
                                            <div class="animate-pulse flex items-center gap-2 text-yellow-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-sm font-medium">Pending</span>
                                            </div>
                                        </div>
                                    @elseif($leave->status === 'approved')
                                        <div class="ml-4">
                                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="ml-4">
                                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('employee.leaveForm') }}" class="text-[#BD6F22] hover:text-[#a75e1c] font-medium text-sm">
                                View All Leave Requests →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-600 font-medium">No leave requests yet</p>
                            <p class="text-gray-400 text-sm mt-1">Submit a leave request when you need time off</p>
                            <a href="{{ route('employee.leaveForm') }}" class="inline-block mt-4 px-6 py-2 bg-[#BD6F22] text-white rounded-lg hover:bg-[#a75e1c] transition font-medium">
                                Submit Leave Request
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="w-96 space-y-6">
            <!-- Mini Calendar -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-br from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="font-bold text-gray-900">Calendar</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div id="miniCalendar">
                        <!-- Calendar will be generated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-br from-orange-50 to-white px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('employee.profile') }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="bg-blue-100 text-blue-600 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 text-sm">View Profile</p>
                            <p class="text-xs text-gray-600">Update your information</p>
                        </div>
                    </a>

                    <a href="{{ route('employee.leaveForm') }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="bg-purple-100 text-purple-600 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 text-sm">Request Leave</p>
                            <p class="text-xs text-gray-600">Submit a new request</p>
                        </div>
                    </a>

                    <a href="{{ route('employee.files') }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="bg-green-100 text-green-600 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 text-sm">My 201 Files</p>
                            <p class="text-xs text-gray-600">View your documents</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Mini Calendar Generator
    function generateMiniCalendar() {
        const today = new Date();
        const currentMonth = today.getMonth();
        const currentYear = today.getFullYear();
        const currentDay = today.getDate();

        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

        let html = `
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-gray-900">${monthNames[currentMonth]} ${currentYear}</h4>
                    <div class="text-xs text-gray-600">${today.toLocaleDateString('en-US', { weekday: 'long' })}</div>
                </div>
                <div class="grid grid-cols-7 gap-1 mb-2">
        `;

        // Days of week header
        daysOfWeek.forEach(day => {
            html += `<div class="text-center text-xs font-semibold text-gray-600 py-2">${day}</div>`;
        });

        html += '</div><div class="grid grid-cols-7 gap-1">';

        // Empty cells before first day
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="aspect-square"></div>';
        }

        // Calendar days
        for (let day = 1; day <= daysInMonth; day++) {
            const isToday = day === currentDay;
            const className = isToday
                ? 'aspect-square flex items-center justify-center rounded-lg bg-[#BD6F22] text-white font-bold text-sm'
                : 'aspect-square flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-700 text-sm';

            html += `<div class="${className}">${day}</div>`;
        }

        html += '</div></div>';

        document.getElementById('miniCalendar').innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', function() {
        generateMiniCalendar();
    });
    </script>
@endif

@endsection
