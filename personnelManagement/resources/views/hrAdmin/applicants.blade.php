<div x-data="applicantsHandler()" x-init="init(); pageContext = 'applicants'" class="relative">


    <!-- Applicants Table -->
    <div class="overflow-x-auto relative bg-white p-6 rounded-lg shadow-lg">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="border-b font-semibold bg-gray-50">
                <tr>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Position</th>
                    <th class="py-3 px-4">Company</th>
                    <th class="py-3 px-4">Applied On</th>
                    <th class="py-3 px-4">Resume</th>
                    <th class="py-3 px-4">Profile</th>
                    <th class="py-3 px-4">Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                    <tr
                        data-applicant-id="{{ $application->id }}"
                        data-status="{{ $application->status }}"
                        x-show="(showAll || (!['approved', 'interviewed', 'for_interview', 'scheduled_for_training','trained', 'hired'].includes('{{ $application->status }}'))) && !removedApplicants.includes({{ $application->id }})"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        @applicant-approved.window="if ($event.detail.id === {{ $application->id }}) removedApplicants.push({{ $application->id }})"
                        class="border-b hover:bg-gray-50"
                    >

                        <td class="py-3 px-4 font-medium whitespace-nowrap flex items-center gap-2">
                            <span class="inline-block w-3 h-3 rounded-full {{ $application->user->active_status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $application->user->first_name . ' ' . $application->user->last_name }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            {{ $application->job->job_title ?? 'N/A' }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            {{ $application->job->company_name ?? 'N/A' }}
                        </td>
                        <td class="py-3 px-4 italic whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($application->created_at)->format('F d, Y') }}
                        </td>

                          <!-- Resume -->
                        
                        <td class="py-3 px-4">
                            @if($application->user->active_status === 'Active' && $application->resume_snapshot)
                                <button
                                    @click="openResume('{{ asset('storage/' . $application->resume_snapshot) }}')"
                                    class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                    View
                                </button>
                            @elseif($application->user->active_status === 'Inactive')
                                <span class="text-gray-400 italic">Inactive</span>
                            @else
                                <span class="text-gray-500 italic">None</span>
                            @endif
                        </td>

                           <!-- Profile -->

                        <td class="py-3 px-4">
                            @if($application->user->active_status === 'Active')
                                <button
                                    @click="openProfile({{ $application->user->id }})"
                                    class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                    View
                                </button>
                            @else
                                <span class="text-gray-400 italic">Inactive</span>
                            @endif
                        </td>

                        <!-- Action -->
                         
                        <td class="py-3 px-4">
                            @if($application->user->active_status === 'Active')
                                @if($application->status === 'interviewed')
                                <span class="text-xs bg-green-200 text-green-800 px-2 py-1 rounded-full transition-colors duration-300">
                                    Interviewed
                                </span>
                            @elseif($application->status === 'approved')
                                <button 
                                    class="bg-green-600 text-white text-sm font-medium h-8 px-3 rounded shadow cursor-not-allowed opacity-70"
                                    disabled>
                                    Approved
                                </button>
                            @elseif($application->status === 'for_interview')
                                <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full transition-colors duration-300">
                                    For Interview
                                </span>
                            @elseif($application->status === 'scheduled_for_training')
                                <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded-full transition-colors duration-300">
                                    Scheduled for Training
                                </span>     
                            @elseif($application->status === 'declined')
                                <span class="text-xs bg-red-200 text-red-800 px-2 py-1 rounded-full transition-colors duration-300">
                                    Declined
                                </span>
                            @elseif($application->status === 'hired')
                             <span class="text-xs bg-green-200 text-green-800 px-2 py-1 rounded-full transition-colors duration-300">
                                    Hired
                                </span>
                            @else
                                <button
                                    @click="confirmStatus('approved', {{ $application->id }}, '{{ $application->user->first_name }} {{ $application->user->last_name }}', '{{ $application->status }}')"
                                    class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                    Approve/Disapprove
                                </button>
                            @endif

                            @else
                                <span class="text-gray-400 italic">Inactive</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">No applicants yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Toggle Button -->
    <div class="flex justify-center mb-4">
        <button
            @click="showAll = !showAll"
            class="px-4 py-2 bg-[#bd6f2200] text-black text-sm font-medium hover:text-[#a95e1d]">
            <span x-text="showAll ? 'Show Only Pending Applicants' : 'Show All Applicants'"></span>
        </button>
    </div>

    <!-- Feedback Toast -->
    <div 
        x-show="feedbackVisible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-4 rounded-xl shadow-lg z-50 w-80 overflow-hidden"
        x-cloak
    >
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-white animate-checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="font-semibold text-sm" x-text="feedbackMessage"></span>
        </div>
        <div class="mt-3 h-1 w-full bg-white/20 rounded overflow-hidden">
            <div class="h-full bg-white animate-progress-bar"></div>
        </div>
    </div>

    <!-- ✅ INCLUDE MODALS INSIDE THE ROOT x-data DIV -->
    @include('components.hrAdmin.modals.resume')
    @include('components.hrAdmin.modals.statusConfirmation')

@foreach ($applications as $application)
    @include('components.hrAdmin.modals.profile', ['user' => $application->user])
@endforeach




</div>

<!-- Scripts: Load Alpine FIRST -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="{{ asset('js/applicantsHandler.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Alpine Cloak -->
<style>[x-cloak] { display: none !important; }</style>
