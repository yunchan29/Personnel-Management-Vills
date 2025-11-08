<div x-data="applicantsHandler()" x-init="init(); pageContext = 'applicants'" class="relative">

    <!-- Applicants Report Modal + Button -->
    <div x-data="{ openReportModal: false, reportType: 'all', dateRange: 'monthly' }">

        <!-- Floating Button -->
        <button @click="openReportModal = true"
            class="fixed bottom-6 right-6 bg-[#BD6F22] hover:bg-[#a95e1d] text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium z-40">
            Generate Applicants Report
        </button>

        <!-- Modal Overlay -->
        <div x-show="openReportModal" x-transition.opacity x-cloak
            class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">

            <!-- Modal Card -->
            <div @click.away="openReportModal = false"
                class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 relative">

                <!-- Close Button -->
                <button @click="openReportModal = false"
                    class="absolute top-4 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
                    &times;
                </button>

                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    Generate Applicants Report
                </h2>

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Report Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Report Type</label>
                        <select x-model="reportType"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                            <option value="all">All Applicants</option>
                            <option value="approved">Approved Applicants</option>
                            <option value="disapproved">Declined Applicants</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Date Range</label>
                        <select x-model="dateRange"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                            <option value="monthly">This Month</option>
                            <option value="quarterly">This Quarter</option>
                            <option value="yearly">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Range -->
                <template x-if="dateRange === 'custom'">
                    <div class="mt-4 flex items-center gap-2">
                        <input type="date" name="start"
                            class="w-1/2 border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                        <span class="text-gray-500">to</span>
                        <input type="date" name="end"
                            class="w-1/2 border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                    </div>
                </template>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end gap-3">
                    <!-- PDF -->
                    <form method="GET"
                        :action="`{{ route('hrAdmin.reports.applicants', 'pdf') }}?job_id={{ $selectedJob->id ?? '' }}&status=${reportType}&range=${dateRange}`">
                        <button type="submit"
                            class="bg-[#BD6F22] hover:bg-[#a95e1d] text-white px-5 py-2 rounded-lg shadow-md text-sm font-medium transition">
                            Download PDF
                        </button>
                    </form>

                    <!-- Excel -->
                    <form method="GET"
                        :action="`{{ route('hrAdmin.reports.applicants', 'excel') }}?job_id={{ $selectedJob->id ?? '' }}&status=${reportType}&range=${dateRange}`">
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md text-sm font-medium transition">
                            Download Excel
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Applicants Table -->
    <div class="overflow-x-auto relative bg-white p-4 rounded-lg shadow-lg">
        <!-- Bulk Approve Button -->
        <template x-if="selectedApplicants.length > 0">
            <div class="flex gap-2 mb-4">
                 <!-- Left side: Master Checkbox -->
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input 
                        type="checkbox" 
                        x-ref="masterCheckbox"
                        @change="toggleSelectAll($event)"
                        class="rounded border-gray-300"
                    >
                    <span>Select All</span>
                </label>
                <!-- Bulk Approve -->
                <div class="relative">
                    <button
                        @click="bulkAction('approved')"
                        :disabled="selectedApplicants.length === 0"
                        class="min-w-[160px] bg-[#8B4513] text-white px-5 py-2.5 rounded-lg shadow-sm flex items-center justify-center gap-2
                                hover:bg-[#6F3610] transition-all duration-200 ease-in-out 
                                disabled:opacity-50 disabled:cursor-not-allowed focus:ring-2 focus:ring-[#BD9168]/40 focus:outline-none">
                    <!-- Lucide: Graduation Cap -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" 
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5L2 10z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    Mass Approve
                </button>
                </div>

                <!-- Bulk Decline -->
                <div class="relative">
                    <button
                        @click="bulkAction('declined')"
                        :disabled="selectedApplicants.length === 0"
                         class="min-w-[160px] bg-[#8B4513] text-white px-5 py-2.5 rounded-lg shadow-sm flex items-center justify-center gap-2
                                hover:bg-[#6F3610] transition-all duration-200 ease-in-out 
                                disabled:opacity-50 disabled:cursor-not-allowed focus:ring-2 focus:ring-[#BD9168]/40 focus:outline-none">
                    <!-- Lucide: Graduation Cap -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" 
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5L2 10z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    Mass Decline
                </button>
                </div>
            </div>
        </template>

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
                        x-show="(showAll || (!['approved', 'interviewed', 'for_interview', 'scheduled_for_training','trained', 'hired', 'fail_interview'].includes('{{ $application->status }}'))) && !removedApplicants.includes({{ $application->id }})"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        @applicant-approved.window="if ($event.detail.id === {{ $application->id }}) removedApplicants.push({{ $application->id }})"
                        class="border-b hover:bg-gray-50"
                    >   
                        @if($application->status === 'Pending')
                        <td class="py-3 px-2">
                        <input 
                            type="checkbox"
                            class="applicant-checkbox"
                            :value="JSON.stringify({
                                application_id: {{ $application->id }},
                                user_id: {{ $application->user_id }},
                                name: '{{ $application->user->first_name }} {{ $application->user->last_name }}',
                            })"
                            :checked="selectedApplicants.some(a => a.application_id === {{ $application->id }})"
                            @change="toggleItem($event, {{ $application->id }})"
                        />
                        </td>
                        @endif
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
                            @if($application->resume_snapshot)
                                <button
                                    @click="openResume('{{ asset('storage/' . $application->resume_snapshot) }}')"
                                    class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                    View
                                </button>
                            @elseif($application->user->resume && $application->user->resume->resume)
                                <button
                                    @click="openResume('{{ asset('storage/' . $application->user->resume->resume) }}')"
                                    class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                    View
                                </button>
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
                            @if($application->status === 'interviewed')
                                <span class="text-xs bg-green-200 text-green-800 px-2 py-1 rounded-full transition-colors duration-300">
                                    Interviewed
                                </span>
                            @elseif($application->status === 'fail_interview')
                                <span class="text-xs bg-red-200 text-red-800 px-2 py-1 rounded-full transition-colors duration-300">
                                    Failed Interview
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
                            @click="confirmStatus(
                                '{{ $application->status === 'approved' ? 'declined' : 'approved' }}',
                                {{ $application->id }},
                                '{{ $application->user->first_name }} {{ $application->user->last_name }}',
                                '{{ $application->status }}'
                            )"
                            class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                            Approve/Decline
                          </button>


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
    <x-shared.feedbackToast />

    <!-- ✅ INCLUDE MODALS INSIDE THE ROOT x-data DIV -->
    @include('components.hrAdmin.modals.resume')
    @include('components.hrAdmin.modals.statusConfirmation')

    @foreach ($applications as $application)
        @include('components.hrAdmin.modals.profile', ['user' => $application->user])
    @endforeach
</div>

<script>
    window.bulkApproveUrl = "{{ route('hrAdmin.applications.bulkUpdateStatus') }}";
</script>

<!-- Scripts -->
<script src="{{ asset('js/utils/checkboxUtils.js') }}"></script>
<script src="{{ asset('js/applicantsHandler.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Alpine Cloak -->
<style>[x-cloak] { display: none !important; }</style>
