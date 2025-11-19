@extends('layouts.hrStaff')

@section('content')
<section class="p-6 max-w-6xl mx-auto">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Training Evaluation</h1>
    <hr class="border-t border-gray-300 mb-6">

<div x-data="applicantsHandler()" x-init="init()" class="relative">
    <div x-data="{
        ...evaluationModal({{ $applicants }}),
        ...requirementsModal(),
        ...actionDropdown(),
        activeTab: 'for-evaluation'
    }">

    <!-- Tabs -->
    <div class="bg-white rounded-t-lg shadow-lg">
        <div class="flex border-b">
            <button
                @click="activeTab = 'for-evaluation'"
                :class="activeTab === 'for-evaluation' ? 'border-b-2 border-[#BD6F22] text-[#BD6F22] font-semibold' : 'text-gray-600 hover:text-[#BD6F22]'"
                class="px-6 py-3 text-sm focus:outline-none transition-colors"
            >
                For Evaluation
            </button>
            <button
                @click="activeTab = 'passer'"
                :class="activeTab === 'passer' ? 'border-b-2 border-[#BD6F22] text-[#BD6F22] font-semibold' : 'text-gray-600 hover:text-[#BD6F22]'"
                class="px-6 py-3 text-sm focus:outline-none transition-colors"
            >
                Passer
            </button>
        </div>
    </div>

    <!-- For Evaluation Tab Content -->
    <div x-show="activeTab === 'for-evaluation'" class="bg-white rounded-b-lg shadow-lg p-6">
        <!-- Bulk Actions Bar -->
        <div x-show="selectedApplicants.length > 0"
             x-transition
             class="flex flex-wrap gap-2 mb-4">

            <!-- Master Checkbox -->
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input
                    type="checkbox"
                    x-ref="masterCheckbox"
                    @change="toggleSelectAll($event)"
                    class="rounded border-gray-300"
                >
                <span>Select All</span>
            </label>

            <!-- Archive Button -->
            <button
                @click="bulkArchive()"
                class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2 hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M20.25 6.75H3.75M19.5 21H4.5A2.25 2.25 0 012.25 18.75v-12m19.5 0v12A2.25 2.25 0 0119.5 21zM9 12h6" />
                </svg>
                <span class="text-sm" x-text="`Archive (${selectedApplicants.length})`"></span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
    <thead class="border-b font-semibold bg-gray-50">
        <tr>
            <th class="py-3 px-4"></th>
            <th class="py-3 px-4">Name</th>
            <th class="py-3 px-4">Job Position</th>
            <th class="py-3 px-4">Company</th>
            <th class="py-3 px-4">Training End Date</th>
            <th class="py-3 px-4">Status</th>
            <th class="py-3 px-4">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($applicants->filter(function($app) {
            return in_array($app->status->value, ['trained', 'for_evaluation', 'scheduled_for_training'])
                && $app->trainingSchedule;
        })->sortBy(fn($app) => $app->evaluation ? 1 : 0) as $applicant)
        <tr
            x-show="showAll || '{{ $applicant->status->value }}' !== 'hired'"
            class="border-b hover:bg-gray-50"
        >
            <!-- Checkbox -->
            <td class="py-3 px-4">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        class="applicant-checkbox rounded border-gray-300"
                        :value="JSON.stringify({
                            application_id: {{ $applicant->id }},
                            user_id: {{ $applicant->user_id }},
                            name: '{{ $applicant->user->full_name }}',
                            has_evaluation: {{ $applicant->evaluation ? 'true' : 'false' }}
                        })"
                        :checked="selectedApplicants.some(a => a.application_id === {{ $applicant->id }})"
                        @change="toggleItem($event, {{ $applicant->id }}); updateMasterCheckbox()"
                    />
                </label>
            </td>
            <!-- Name -->
            <td class="py-3 px-4 align-middle font-medium whitespace-nowrap">
                {{ $applicant->user->full_name }}
            </td>

            <!-- Job Position -->
            <td class="py-3 px-4 align-middle whitespace-nowrap">
                {{ $applicant->job->job_title ?? '—' }}
            </td>

            <!-- Company -->
            <td class="py-3 px-4 align-middle whitespace-nowrap">
                {{ $applicant->job->company_name ?? '—' }}
            </td>

            <!-- Training End Date -->
            <td class="py-3 px-4 align-middle whitespace-nowrap">
                {{ $applicant->trainingSchedule ? \Carbon\Carbon::parse($applicant->trainingSchedule->end_date)->format('M d, Y') : '—' }}
            </td>

            <!-- Status + Score -->
            <td class="py-3 px-4 align-middle whitespace-nowrap">
                @if($applicant->evaluation)
                    @php
                        $score = $applicant->evaluation->total_score ?? 0;
                    @endphp

                    @if($score >= 70)
                        <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                            Passed ({{ $score }}/100)
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">
                            Failed ({{ $score }}/100)
                        </span>
                    @endif
                @else
                    <span class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded">
                        Pending
                    </span>
                @endif
            </td>

            <!-- Actions Icons -->
            <td class="py-3 px-4 align-middle whitespace-nowrap">
                <div class="flex gap-2">
                    @php
                        $hasPassed = $applicant->evaluation && ($applicant->evaluation->total_score ?? 0) >= 70;
                    @endphp

                    @if($hasPassed)
                    <!-- Send Invitation Icon (for passed applicants) -->
                    <button
                        @click="openContractSigningInvitation('{{ $applicant->user->full_name }}', {{ $applicant->id }})"
                        class="p-2 text-gray-700 hover:text-[#BD6F22] hover:bg-gray-100 rounded transition-colors"
                        title="Send Invitation"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </button>
                    @else
                    <!-- Evaluate Icon (for not evaluated or failed applicants) -->
                    <button
                        @click="openModal('{{ $applicant->user->full_name }}', {{ $applicant->id }}, {{ $applicant->evaluation ? 'true' : 'false' }}, null)"
                        class="p-2 text-gray-700 hover:text-[#BD6F22] hover:bg-gray-100 rounded transition-colors"
                        title="Evaluate"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z" />
                        </svg>
                    </button>
                    @endif

                    <!-- View Requirements Icon -->
                    <button
                        @click="openRequirements('{{ $applicant->user->full_name }}', {{ $applicant->user_id }})"
                        class="p-2 text-gray-700 hover:text-[#BD6F22] hover:bg-gray-100 rounded transition-colors"
                        title="View Requirements"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h4l2-2h8l2 2h4v12H3V7z" />
                        </svg>
                    </button>
                </div>
            </td>

        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-6 text-gray-500 italic">
                No applicants pending for evaluation.
            </td>
        </tr>
        @endforelse
        </tbody>
        </table>

        </div>

        <!-- Show All / Hide Hired Toggle -->
        <div class="flex justify-center mt-4">
            <button
                @click="showAll = !showAll"
                class="text-sm text-[#BD6F22] hover:underline focus:outline-none"
            >
                <span x-text="showAll ? 'Hide Hired' : 'Show All'"></span>
            </button>
        </div>
    </div>

    <!-- Passer Tab Content -->
    <div x-show="activeTab === 'passer'" class="bg-white rounded-b-lg shadow-lg p-6" x-data="{ selectedPassers: [] }">
        <!-- Invitation Summary -->
        @php
            $passers = $applicants->filter(function($app) {
                return $app->evaluation
                    && $app->evaluation->result === 'Passed';
            });
            $totalInvitations = $applicants->sum('contract_invitations_count');
            $passersWithInvitations = $applicants->filter(fn($app) => $app->evaluation && $app->evaluation->result === 'Passed' && $app->contract_invitations_count > 0)->count();
        @endphp
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold text-blue-900">Total Invitations Sent: <span class="text-blue-700">{{ $totalInvitations }}</span></span>
                </div>
                <div class="text-blue-700">
                    {{ $passersWithInvitations }} of {{ $passers->count() }} passers have received invitations
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar for Passers -->
        <div x-show="selectedPassers.length > 0"
             x-transition
             class="flex flex-wrap gap-2 mb-4">

            <!-- Master Checkbox -->
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input
                    type="checkbox"
                    x-ref="passerMasterCheckbox"
                    @change="
                        if ($event.target.checked) {
                            selectedPassers = Array.from(document.querySelectorAll('.passer-checkbox')).map(cb => JSON.parse(cb.value));
                        } else {
                            selectedPassers = [];
                        }
                    "
                    class="rounded border-gray-300"
                >
                <span>Select All</span>
            </label>

            <!-- Send Invitation Button -->
            <button
                @click="bulkSendInvitation()"
                class="min-w-[180px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2 hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="text-sm" x-text="`Send Invitation (${selectedPassers.length})`"></span>
            </button>

            <!-- Promote Button (includes contract setting) -->
            <button
                @click="bulkPromotePassers()"
                class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2 hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <span class="text-sm" x-text="`Promote (${selectedPassers.length})`"></span>
            </button>

            <!-- Archive Button -->
            <button
                @click="bulkArchivePassers()"
                class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2 hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M20.25 6.75H3.75M19.5 21H4.5A2.25 2.25 0 012.25 18.75v-12m19.5 0v12A2.25 2.25 0 0119.5 21zM9 12h6" />
                </svg>
                <span class="text-sm" x-text="`Archive (${selectedPassers.length})`"></span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="border-b font-semibold bg-gray-50">
                    <tr>
                        <th class="py-3 px-4"></th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Job Position</th>
                        <th class="py-3 px-4">Company</th>
                        <th class="py-3 px-4">Training End Date</th>
                        <th class="py-3 px-4">Score</th>
                        <th class="py-3 px-4">Invitation Sent</th>
                        <th class="py-3 px-4">Requirements</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($passers as $applicant)
                    <tr class="border-b hover:bg-gray-50">
                        <!-- Checkbox -->
                        <td class="py-3 px-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    class="passer-checkbox rounded border-gray-300"
                                    :value="JSON.stringify({
                                        application_id: {{ $applicant->id }},
                                        user_id: {{ $applicant->user_id }},
                                        name: '{{ $applicant->user->full_name }}',
                                        has_invitation: {{ $applicant->contract_signing_schedule ? 'true' : 'false' }}
                                    })"
                                    :checked="selectedPassers.some(a => a.application_id === {{ $applicant->id }})"
                                    @change="
                                        if ($event.target.checked) {
                                            selectedPassers.push(JSON.parse($event.target.value));
                                        } else {
                                            selectedPassers = selectedPassers.filter(a => a.application_id !== {{ $applicant->id }});
                                        }
                                        const allCheckboxes = document.querySelectorAll('.passer-checkbox');
                                        const checkedCheckboxes = Array.from(allCheckboxes).filter(cb => cb.checked);
                                        $refs.passerMasterCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
                                        $refs.passerMasterCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
                                    "
                                />
                            </label>
                        </td>

                        <!-- Name -->
                        <td class="py-3 px-4 align-middle font-medium whitespace-nowrap">
                            {{ $applicant->user->full_name }}
                        </td>

                        <!-- Job Position -->
                        <td class="py-3 px-4 align-middle whitespace-nowrap">
                            {{ $applicant->job->job_title ?? '—' }}
                        </td>

                        <!-- Company -->
                        <td class="py-3 px-4 align-middle whitespace-nowrap">
                            {{ $applicant->job->company_name ?? '—' }}
                        </td>

                        <!-- Training End Date -->
                        <td class="py-3 px-4 align-middle whitespace-nowrap">
                            {{ $applicant->trainingSchedule ? \Carbon\Carbon::parse($applicant->trainingSchedule->end_date)->format('M d, Y') : '—' }}
                        </td>

                        <!-- Score -->
                        <td class="py-3 px-4 align-middle whitespace-nowrap">
                            @php
                                $score = $applicant->evaluation->total_score ?? 0;
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                                {{ $score }}/100
                            </span>
                        </td>

                        <!-- Invitation Sent Status -->
                        <td class="py-3 px-4 align-middle whitespace-nowrap text-center">
                            @if($applicant->contract_invitations_count > 0)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Sent ({{ $applicant->contract_invitations_count }}x)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Pending
                                </span>
                            @endif
                        </td>

                        <!-- Requirements Button -->
                        <td class="py-3 px-4 align-middle whitespace-nowrap">
                            <button
                                @click="openRequirements('{{ $applicant->user->full_name }}', {{ $applicant->user_id }})"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#BD6F22]"
                            >
                                <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h4l2-2h8l2 2h4v12H3V7z" />
                                </svg>
                                View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500 italic">
                            No applicants have passed the evaluation yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Hidden Forms for Bulk Actions -->
    @foreach ($applicants->filter(function($app) {
        return in_array($app->status->value, ['trained', 'for_evaluation', 'scheduled_for_training']) && $app->trainingSchedule;
    }) as $applicant)
        <!-- Promote Form -->
        @if($applicant->status->value !== 'hired')
        <form method="POST" action="{{ route('hrStaff.evaluation.promote', $applicant->id) }}" style="display: none;" id="promote-form-{{ $applicant->id }}">
            @csrf
        </form>
        @endif

        <!-- Archive Form -->
        @if($applicant->status->value !== 'hired')
        <form action="{{ route('hrStaff.archive.store', $applicant->id) }}" method="POST" style="display: none;" id="archive-form-{{ $applicant->id }}" class="archive-form">
            @csrf
        </form>
        @endif
    @endforeach

    <!-- Evaluation Modal -->
    <x-hrStaff.evaluationModal />

    <!-- Requirements Modal -->
    <x-hrStaff.requirementsModal />

    <!-- Feedback Toast -->
    <x-shared.feedbackToast />

    <script>
        @if(session('success'))
            window.contractScheduleSuccess = "{{ session('success') }}";
        @endif
    </script>

    </div>
</div>

</section>

@endsection

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Handlers -->
<script src="{{ asset('js/utils/checkboxUtils.js') }}"></script>
<script src="{{ asset('js/applicantsHandler.js') }}"></script>

<script>
function actionDropdown() {
    return {
        currentIndex: 0,
        processingApplicants: [],
        actionType: '',

        bulkEvaluate() {
            if (this.selectedApplicants.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant to evaluate.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }
            if (this.selectedApplicants.length > 1) {
                Swal.fire({
                    title: 'Single Selection Only',
                    text: 'Evaluation requires individual assessment. Please select only ONE applicant at a time.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }
            // Open evaluation modal for single applicant
            const applicant = this.selectedApplicants[0];
            this.openModal(applicant.name, applicant.application_id, applicant.has_evaluation, null);
        },

        bulkViewRequirements() {
            if (this.selectedApplicants.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }
            if (this.selectedApplicants.length > 1) {
                Swal.fire({
                    title: 'Single Selection Only',
                    text: 'Requirements viewing is for individual applicants. Please select only ONE applicant at a time.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }
            // Open requirements modal for single applicant
            const applicant = this.selectedApplicants[0];
            this.openRequirements(applicant.name, applicant.user_id);
        },

        async bulkPromote() {
            if (this.selectedApplicants.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant to promote.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDate = tomorrow.toISOString().split('T')[0];

            Swal.fire({
                title: `Promote ${this.selectedApplicants.length} Applicant(s) to Employee`,
                html: `
                    <div class="text-left space-y-4">
                        <p class="text-sm text-gray-600 mb-4">
                            Set the employment contract period for all selected applicants.
                        </p>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Contract Start Date</label>
                            <input type="date" id="bulk_promote_contract_start"
                                   class="w-full px-3 py-2 border rounded"
                                   min="${minDate}" required>

                            <label class="block text-sm text-gray-700 mb-1 mt-2">Contract Period</label>
                            <select id="bulk_promote_contract_period" class="w-full px-3 py-2 border rounded">
                                <option value="6m" selected>6 Months</option>
                                <option value="1y">1 Year</option>
                            </select>

                            <label class="block text-sm text-gray-700 mb-1 mt-2">Contract End Date (Auto-calculated)</label>
                            <input type="text" id="bulk_promote_contract_end"
                                   class="w-full px-3 py-2 border rounded bg-gray-100"
                                   readonly placeholder="Will be calculated">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Promote All',
                cancelButtonText: 'Cancel',
                didOpen: () => {
                    const startInput = document.getElementById('bulk_promote_contract_start');
                    const periodSelect = document.getElementById('bulk_promote_contract_period');
                    const endInput = document.getElementById('bulk_promote_contract_end');

                    const calculateEnd = () => {
                        if (!startInput.value) return;
                        const start = new Date(startInput.value);
                        const period = periodSelect.value;

                        if (period === '6m') {
                            start.setMonth(start.getMonth() + 6);
                        } else if (period === '1y') {
                            start.setFullYear(start.getFullYear() + 1);
                        }

                        endInput.value = start.toISOString().split('T')[0];
                    };

                    startInput.addEventListener('change', calculateEnd);
                    periodSelect.addEventListener('change', calculateEnd);
                },
                preConfirm: () => {
                    const contractStart = document.getElementById('bulk_promote_contract_start').value;
                    const contractPeriod = document.getElementById('bulk_promote_contract_period').value;

                    if (!contractStart) {
                        Swal.showValidationMessage('Please fill in the contract start date');
                        return false;
                    }

                    return {
                        contract_start: contractStart,
                        period: contractPeriod
                    };
                }
            }).then(async (result) => {
                if (result.isConfirmed && result.value) {
                    const contractData = result.value;
                    let successCount = 0;
                    let errorCount = 0;

                    // Show loading modal
                    Swal.fire({
                        title: 'Processing...',
                        html: `
                            <div class="flex flex-col items-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#BD6F22] mb-4"></div>
                                <p class="text-sm text-gray-600">Promoting <strong>${this.selectedApplicants.length}</strong> applicant(s) to employee...</p>
                                <p class="text-xs text-gray-500 mt-2">Setting contract dates and updating records...</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    for (const applicant of this.selectedApplicants) {
                        try {
                            // Set contract dates
                            const contractResponse = await fetch(`/hrStaff/contract-dates/${applicant.application_id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(contractData)
                            });

                            if (!contractResponse.ok) throw new Error('Failed to set contract');

                            // Promote to employee
                            const promoteResponse = await fetch(`/hrStaff/evaluation/promote/${applicant.application_id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (!promoteResponse.ok) throw new Error('Failed to promote');

                            successCount++;
                        } catch (error) {
                            errorCount++;
                            console.error(`Error promoting ${applicant.name}:`, error);
                        }
                    }

                    Swal.fire({
                        title: 'Complete!',
                        html: `<p>Successfully promoted: <strong>${successCount}</strong></p>
                               ${errorCount > 0 ? `<p class="text-red-600">Failed: <strong>${errorCount}</strong></p>` : ''}`,
                        icon: errorCount > 0 ? 'warning' : 'success',
                        confirmButtonColor: '#BD6F22'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        },

        async bulkArchive() {
            if (this.selectedApplicants.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant to archive.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }

            Swal.fire({
                title: `Archive ${this.selectedApplicants.length} Applicant(s)?`,
                text: 'Are you sure you want to archive all selected applicants?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, archive all',
                cancelButtonText: 'Cancel'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Show loading modal
                    Swal.fire({
                        title: 'Processing...',
                        html: `
                            <div class="flex flex-col items-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#BD6F22] mb-4"></div>
                                <p class="text-sm text-gray-600">Archiving <strong>${this.selectedApplicants.length}</strong> applicant(s)...</p>
                                <p class="text-xs text-gray-500 mt-2">Please wait...</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    let successCount = 0;
                    let errorCount = 0;

                    for (const applicant of this.selectedApplicants) {
                        try {
                            const response = await fetch(`/hrStaff/archive/${applicant.application_id}`, {

                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (!response.ok) throw new Error('Failed to archive');

                            successCount++;
                        } catch (error) {
                            errorCount++;
                            console.error(`Error archiving ${applicant.name}:`, error);
                        }
                    }

                    Swal.fire({
                        title: 'Complete!',
                        html: `<p>Successfully archived: <strong>${successCount}</strong></p>
                               ${errorCount > 0 ? `<p class="text-red-600">Failed: <strong>${errorCount}</strong></p>` : ''}`,
                        icon: errorCount > 0 ? 'warning' : 'success',
                        confirmButtonColor: '#BD6F22'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        },

        startSteppedAction(actionType, applicants) {
            this.actionType = actionType;
            this.processingApplicants = [...applicants];
            this.currentIndex = 0;
            this.processNextApplicant();
        },

        processNextApplicant() {
            if (this.currentIndex >= this.processingApplicants.length) {
                // All done
                Swal.fire({
                    title: 'Complete!',
                    text: `All ${this.processingApplicants.length} applicant(s) have been processed.`,
                    icon: 'success',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }

            const applicant = this.processingApplicants[this.currentIndex];
            const progress = `(${this.currentIndex + 1}/${this.processingApplicants.length})`;

            switch(this.actionType) {
                case 'evaluate':
                    this.openEvaluationModal(applicant, progress);
                    break;
                case 'requirements':
                    this.openRequirementsForApplicant(applicant, progress);
                    break;
                case 'promote':
                    this.promptPromote(applicant, progress);
                    break;
                case 'archive':
                    this.promptArchive(applicant, progress);
                    break;
            }
        },

        openEvaluationModal(applicant, progress) {
            // Open evaluation modal for this applicant
            this.openModal(applicant.name, applicant.application_id, applicant.has_evaluation, null);
            // Note: The modal close should call continueToNext()
        },

        openContractModal(applicant, progress) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDate = tomorrow.toISOString().split('T')[0];

            Swal.fire({
                title: `Set Contract Dates for ${applicant.name} ${progress}`,
                html: `
                    <div class="text-left space-y-4">
                        <p class="text-sm text-gray-600 mb-4">
                            Set the employment contract period for <strong>${applicant.name}</strong>.
                        </p>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Contract Start Date</label>
                            <input type="date" id="contract_start"
                                   class="w-full px-3 py-2 border rounded"
                                   min="${minDate}" required>

                            <label class="block text-sm text-gray-700 mb-1 mt-2">Contract Period</label>
                            <select id="contract_period_length" class="w-full px-3 py-2 border rounded">
                                <option value="6m" selected>6 Months</option>
                                <option value="1y">1 Year</option>
                            </select>

                            <label class="block text-sm text-gray-700 mb-1 mt-2">Contract End Date (Auto-calculated)</label>
                            <input type="text" id="contract_end"
                                   class="w-full px-3 py-2 border rounded bg-gray-100"
                                   readonly placeholder="Will be calculated">
                        </div>
                    </div>
                `,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                denyButtonColor: '#6B7280',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Save & Next',
                denyButtonText: 'Skip',
                cancelButtonText: 'Stop',
                didOpen: () => {
                    // Auto-calculate end date
                    const startInput = document.getElementById('contract_start');
                    const periodSelect = document.getElementById('contract_period_length');
                    const endInput = document.getElementById('contract_end');

                    const calculateEnd = () => {
                        if (!startInput.value) return;
                        const start = new Date(startInput.value);
                        const period = periodSelect.value;

                        if (period === '6m') {
                            start.setMonth(start.getMonth() + 6);
                        } else if (period === '1y') {
                            start.setFullYear(start.getFullYear() + 1);
                        }

                        endInput.value = start.toISOString().split('T')[0];
                    };

                    startInput.addEventListener('change', calculateEnd);
                    periodSelect.addEventListener('change', calculateEnd);
                },
                preConfirm: () => {
                    const contractStart = document.getElementById('contract_start').value;
                    const contractPeriodLength = document.getElementById('contract_period_length').value;

                    if (!contractStart) {
                        Swal.showValidationMessage('Please fill in the contract start date');
                        return false;
                    }

                    return {
                        contract_start: contractStart,
                        period: contractPeriodLength
                    };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // Submit contract data via AJAX
                    this.submitContractData(applicant.application_id, result.value);
                } else if (result.isDenied) {
                    this.continueToNext();
                }
            });
        },

        async submitContractData(applicationId, data) {
            try {
                // Submit contract dates only
                const datesResponse = await fetch(`/hrStaff/contract-dates/${applicationId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        contract_start: data.contract_start,
                        period: data.period
                    })
                });

                if (!datesResponse.ok) throw new Error('Failed to set contract dates');

                Swal.fire({
                    title: 'Success!',
                    text: 'Contract dates saved successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

                this.continueToNext();
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#BD6F22'
                });
            }
        },

        openRequirementsForApplicant(applicant, progress) {
            this.openRequirements(applicant.name, applicant.user_id);
            // Continue after viewing
            setTimeout(() => {
                this.continueToNext();
            }, 1000);
        },

        async promptPromote(applicant, progress) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDate = tomorrow.toISOString().split('T')[0];

            Swal.fire({
                title: `Promote to Employee ${progress}`,
                html: `
                    <div class="text-left space-y-4">
                        <p class="text-sm text-gray-600 mb-4">
                            Set the employment contract period for <strong>${applicant.name}</strong>.
                        </p>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Contract Start Date</label>
                            <input type="date" id="promote_contract_start"
                                   class="w-full px-3 py-2 border rounded"
                                   min="${minDate}" required>

                            <label class="block text-sm text-gray-700 mb-1 mt-2">Contract Period</label>
                            <select id="promote_contract_period" class="w-full px-3 py-2 border rounded">
                                <option value="6m" selected>6 Months</option>
                                <option value="1y">1 Year</option>
                            </select>

                            <label class="block text-sm text-gray-700 mb-1 mt-2">Contract End Date (Auto-calculated)</label>
                            <input type="text" id="promote_contract_end"
                                   class="w-full px-3 py-2 border rounded bg-gray-100"
                                   readonly placeholder="Will be calculated">
                        </div>
                    </div>
                `,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                denyButtonColor: '#6B7280',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Promote & Set Contract',
                denyButtonText: 'Skip',
                cancelButtonText: 'Stop',
                didOpen: () => {
                    // Auto-calculate end date
                    const startInput = document.getElementById('promote_contract_start');
                    const periodSelect = document.getElementById('promote_contract_period');
                    const endInput = document.getElementById('promote_contract_end');

                    const calculateEnd = () => {
                        if (!startInput.value) return;
                        const start = new Date(startInput.value);
                        const period = periodSelect.value;

                        if (period === '6m') {
                            start.setMonth(start.getMonth() + 6);
                        } else if (period === '1y') {
                            start.setFullYear(start.getFullYear() + 1);
                        }

                        endInput.value = start.toISOString().split('T')[0];
                    };

                    startInput.addEventListener('change', calculateEnd);
                    periodSelect.addEventListener('change', calculateEnd);
                },
                preConfirm: () => {
                    const contractStart = document.getElementById('promote_contract_start').value;
                    const contractPeriod = document.getElementById('promote_contract_period').value;

                    if (!contractStart) {
                        Swal.showValidationMessage('Please fill in the contract start date');
                        return false;
                    }

                    return {
                        contract_start: contractStart,
                        period: contractPeriod
                    };
                }
            }).then(async (result) => {
                if (result.isConfirmed && result.value) {
                    try {
                        // First, set contract dates
                        const contractResponse = await fetch(`/hrStaff/contract-dates/${applicant.application_id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                contract_start: result.value.contract_start,
                                period: result.value.period
                            })
                        });

                        if (!contractResponse.ok) throw new Error('Failed to set contract dates');

                        // Then, promote to employee
                        const promoteResponse = await fetch(`/hrStaff/evaluation/promote/${applicant.application_id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (!promoteResponse.ok) throw new Error('Failed to promote applicant');

                        Swal.fire({
                            title: 'Success!',
                            text: `${applicant.name} has been promoted to employee with contract set.`,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        this.continueToNext();
                    } catch (error) {
                        Swal.fire({
                            title: 'Error',
                            text: error.message,
                            icon: 'error',
                            confirmButtonColor: '#BD6F22'
                        });
                    }
                } else if (result.isDenied) {
                    this.continueToNext();
                }
            });
        },

        async promptArchive(applicant, progress) {
            Swal.fire({
                title: `Archive Applicant ${progress}`,
                text: `Archive ${applicant.name}?`,
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                denyButtonColor: '#6B7280',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive!',
                denyButtonText: 'Skip',
                cancelButtonText: 'Stop'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Submit archive via AJAX
                    try {
                        const response = await fetch(`/hrStaff/archive/${applicant.application_id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (!response.ok) throw new Error('Failed to archive applicant');

                        Swal.fire({
                            title: 'Archived!',
                            text: `${applicant.name} has been archived.`,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        this.continueToNext();
                    } catch (error) {
                        Swal.fire({
                            title: 'Error',
                            text: error.message,
                            icon: 'error',
                            confirmButtonColor: '#BD6F22'
                        });
                    }
                } else if (result.isDenied) {
                    this.continueToNext();
                }
            });
        },

        continueToNext() {
            this.currentIndex++;
            this.processNextApplicant();
        },

        skipToEnd() {
            this.currentIndex = this.processingApplicants.length;
        },

        // Bulk actions for Passer tab
        async bulkSendInvitation() {
            const selectedPassers = window.Alpine ? Alpine.raw(this.$data.selectedPassers || []) : [];
            if (selectedPassers.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }

            // Edge Case: Check if any selected applicants already have recent invitations
            const passersWithRecentInvitations = selectedPassers.filter(p => p.has_invitation);
            if (passersWithRecentInvitations.length > 0) {
                const result = await Swal.fire({
                    title: 'Invitation Warning',
                    html: `<p class="text-sm text-gray-700">${passersWithRecentInvitations.length} of the selected applicants already have invitations.</p>
                           <p class="mt-2 text-sm text-gray-600">Do you want to send new invitations anyway?</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#BD6F22',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Yes, send anyway',
                    cancelButtonText: 'Cancel'
                });

                if (!result.isConfirmed) return;
            }

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDate = tomorrow.toISOString().split('T')[0];

            Swal.fire({
                title: `Send Contract Signing Invitation`,
                html: `
                    <div class="text-left space-y-4">
                        <p class="text-sm text-gray-600 mb-4">
                            Schedule the contract signing for <strong>${selectedPassers.length}</strong> selected applicant(s).
                        </p>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Signing Date</label>
                            <input type="date" id="bulk_contract_date"
                                   class="w-full px-3 py-2 border rounded"
                                   min="${minDate}" required>

                            <label class="block text-sm text-gray-700 mb-1 mt-2">Signing Time</label>
                            <div class="flex gap-2">
                                <select id="bulk_contract_hour" class="flex-1 px-2 py-2 border rounded">
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8" selected>8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <select id="bulk_contract_minute" class="flex-1 px-2 py-2 border rounded">
                                    <option value="00" selected>00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                                <select id="bulk_contract_period" class="px-3 py-2 border rounded bg-gray-100">
                                    <option value="AM" selected>AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Send Invitations',
                cancelButtonText: 'Cancel',
                didOpen: () => {
                    const hourSelect = document.getElementById('bulk_contract_hour');
                    const periodAMPM = document.getElementById('bulk_contract_period');

                    hourSelect.addEventListener('change', () => {
                        const hour = parseInt(hourSelect.value);
                        if (hour >= 6 && hour <= 11) {
                            periodAMPM.value = 'AM';
                        } else {
                            periodAMPM.value = 'PM';
                        }
                    });
                },
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    const contractDate = document.getElementById('bulk_contract_date').value;
                    const contractHour = document.getElementById('bulk_contract_hour').value;
                    const contractMinute = document.getElementById('bulk_contract_minute').value;
                    const contractPeriod = document.getElementById('bulk_contract_period').value;

                    if (!contractDate) {
                        Swal.showValidationMessage('Please fill in the signing date');
                        return false;
                    }

                    // Edge Case: Validate date/time is not in the past
                    const selectedDateTime = new Date(`${contractDate} ${contractHour}:${contractMinute} ${contractPeriod}`);
                    const now = new Date();

                    if (selectedDateTime <= now) {
                        Swal.showValidationMessage('The scheduled date and time cannot be in the past or current time');
                        return false;
                    }

                    // Edge Case: Validate business hours (6 AM - 5 PM)
                    let hour24 = parseInt(contractHour);
                    if (contractPeriod === 'PM' && hour24 !== 12) {
                        hour24 += 12;
                    } else if (contractPeriod === 'AM' && hour24 === 12) {
                        hour24 = 0;
                    }

                    if (hour24 < 6 || hour24 >= 17) {
                        Swal.showValidationMessage('Contract signing must be scheduled during business hours (6:00 AM - 5:00 PM)');
                        return false;
                    }

                    const invitationData = {
                        contract_date: contractDate,
                        contract_signing_time: `${contractHour}:${contractMinute} ${contractPeriod}`
                    };

                    let successCount = 0;
                    let errorCount = 0;
                    let errorMessages = [];

                    for (const applicant of selectedPassers) {
                        try {
                            const response = await fetch(`/hrStaff/contract-schedule/${applicant.application_id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(invitationData)
                            });

                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(errorData.message || 'Failed to send invitation');
                            }

                            successCount++;
                        } catch (error) {
                            errorCount++;
                            errorMessages.push(`${applicant.name}: ${error.message}`);
                            console.error(`Error sending invitation to ${applicant.name}:`, error);
                        }
                    }

                    return {
                        successCount: successCount,
                        errorCount: errorCount,
                        errorMessages: errorMessages
                    };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const finalResult = result.value;

                    let htmlContent = `<p>Invitations sent successfully: <strong>${finalResult.successCount}</strong></p>`;

                    if (finalResult.errorCount > 0) {
                        htmlContent += `<p class="text-red-600 mt-2">Failed: <strong>${finalResult.errorCount}</strong></p>`;

                        // Show detailed error messages if available
                        if (finalResult.errorMessages && finalResult.errorMessages.length > 0) {
                            htmlContent += `<div class="mt-3 text-left max-h-48 overflow-y-auto">
                                <p class="text-sm font-semibold text-gray-700 mb-1">Error Details:</p>
                                <ul class="text-xs text-gray-600 space-y-1">`;
                            finalResult.errorMessages.forEach(msg => {
                                htmlContent += `<li class="pl-2">• ${msg}</li>`;
                            });
                            htmlContent += `</ul></div>`;
                        }
                    }

                    Swal.fire({
                        title: 'Complete!',
                        html: htmlContent,
                        icon: finalResult.errorCount > 0 ? 'warning' : 'success',
                        confirmButtonColor: '#BD6F22',
                        allowOutsideClick: false,
                        width: '600px'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        },

        async bulkPromotePassers() {
            const selectedPassers = window.Alpine ? Alpine.raw(this.$data.selectedPassers || []) : [];
            if (selectedPassers.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant to promote.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }

            // Check if all selected applicants have received an invitation
            const passersWithoutInvitation = selectedPassers.filter(p => !p.has_invitation);
            if (passersWithoutInvitation.length > 0) {
                const names = passersWithoutInvitation.map(p => p.name).join(', ');
                Swal.fire({
                    title: 'Invitation Required',
                    html: `<p class="text-sm text-gray-700">The following applicant(s) must receive a contract signing invitation before they can be promoted:</p>
                           <p class="mt-2 font-semibold text-gray-900">${names}</p>
                           <p class="mt-2 text-sm text-gray-600">Please send them an invitation first by using the "Send Invitation" button.</p>`,
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDate = tomorrow.toISOString().split('T')[0];

            Swal.fire({
                title: `Promote ${selectedPassers.length} Applicant(s) to Employee`,
                html: `
                    <div class="text-left space-y-4">
                        <p class="text-sm text-gray-600 mb-4">
                            Set the employment contract period for all selected applicants.
                        </p>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Contract Start Date</label>
                            <input type="date" id="bulk_passer_promote_contract_start"
                                   class="w-full px-3 py-2 border rounded"
                                   min="${minDate}" required>

                            <label class="block text-sm text-gray-700 mb-1 mt-2">Contract Period</label>
                            <select id="bulk_passer_promote_contract_period" class="w-full px-3 py-2 border rounded">
                                <option value="6m" selected>6 Months</option>
                                <option value="1y">1 Year</option>
                            </select>

                            <label class="block text-sm text-gray-700 mb-1 mt-2">Contract End Date (Auto-calculated)</label>
                            <input type="text" id="bulk_passer_promote_contract_end"
                                   class="w-full px-3 py-2 border rounded bg-gray-100"
                                   readonly placeholder="Will be calculated">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Promote All',
                cancelButtonText: 'Cancel',
                didOpen: () => {
                    const startInput = document.getElementById('bulk_passer_promote_contract_start');
                    const periodSelect = document.getElementById('bulk_passer_promote_contract_period');
                    const endInput = document.getElementById('bulk_passer_promote_contract_end');

                    const calculateEnd = () => {
                        if (!startInput.value) return;
                        const start = new Date(startInput.value);
                        const period = periodSelect.value;

                        if (period === '6m') {
                            start.setMonth(start.getMonth() + 6);
                        } else if (period === '1y') {
                            start.setFullYear(start.getFullYear() + 1);
                        }

                        endInput.value = start.toISOString().split('T')[0];
                    };

                    startInput.addEventListener('change', calculateEnd);
                    periodSelect.addEventListener('change', calculateEnd);
                },
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    const contractStart = document.getElementById('bulk_passer_promote_contract_start').value;
                    const contractPeriod = document.getElementById('bulk_passer_promote_contract_period').value;

                    if (!contractStart) {
                        Swal.showValidationMessage('Please fill in the contract start date');
                        return false;
                    }

                    const contractData = {
                        contract_start: contractStart,
                        period: contractPeriod
                    };

                    let successCount = 0;
                    let errorCount = 0;

                    for (const applicant of selectedPassers) {
                        try {
                            // Set contract dates
                            const contractResponse = await fetch(`/hrStaff/contract-dates/${applicant.application_id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(contractData)
                            });

                            if (!contractResponse.ok) throw new Error('Failed to set contract');

                            // Promote to employee
                            const promoteResponse = await fetch(`/hrStaff/evaluation/promote/${applicant.application_id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (!promoteResponse.ok) throw new Error('Failed to promote');

                            successCount++;
                        } catch (error) {
                            errorCount++;
                            console.error(`Error promoting ${applicant.name}:`, error);
                        }
                    }

                    return {
                        successCount: successCount,
                        errorCount: errorCount
                    };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const finalResult = result.value;
                    Swal.fire({
                        title: 'Complete!',
                        html: `<p>Successfully promoted: <strong>${finalResult.successCount}</strong></p>
                               ${finalResult.errorCount > 0 ? `<p class="text-red-600">Failed: <strong>${finalResult.errorCount}</strong></p>` : ''}`,
                        icon: finalResult.errorCount > 0 ? 'warning' : 'success',
                        confirmButtonColor: '#BD6F22',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        },

        async bulkArchivePassers() {
            const selectedPassers = window.Alpine ? Alpine.raw(this.$data.selectedPassers || []) : [];
            if (selectedPassers.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant to archive.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }

            Swal.fire({
                title: `Archive ${selectedPassers.length} Applicant(s)?`,
                text: 'Are you sure you want to archive all selected applicants?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, archive all',
                cancelButtonText: 'Cancel'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Show loading modal
                    Swal.fire({
                        title: 'Processing...',
                        html: `
                            <div class="flex flex-col items-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#BD6F22] mb-4"></div>
                                <p class="text-sm text-gray-600">Archiving <strong>${selectedPassers.length}</strong> applicant(s)...</p>
                                <p class="text-xs text-gray-500 mt-2">Please wait...</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    let successCount = 0;
                    let errorCount = 0;

                    for (const applicant of selectedPassers) {
                        try {
                            const response = await fetch(`/hrStaff/archive/${applicant.application_id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (!response.ok) throw new Error('Failed to archive');

                            successCount++;
                        } catch (error) {
                            errorCount++;
                            console.error(`Error archiving ${applicant.name}:`, error);
                        }
                    }

                    Swal.fire({
                        title: 'Complete!',
                        html: `<p>Successfully archived: <strong>${successCount}</strong></p>
                               ${errorCount > 0 ? `<p class="text-red-600">Failed: <strong>${errorCount}</strong></p>` : ''}`,
                        icon: errorCount > 0 ? 'warning' : 'success',
                        confirmButtonColor: '#BD6F22'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        },

        startSteppedActionPassers(actionType, applicants) {
            this.actionType = actionType;
            this.processingApplicants = [...applicants];
            this.currentIndex = 0;
            this.processNextApplicantPasser();
        },

        processNextApplicantPasser() {
            if (this.currentIndex >= this.processingApplicants.length) {
                Swal.fire({
                    title: 'Complete!',
                    text: `All ${this.processingApplicants.length} applicant(s) have been processed.`,
                    icon: 'success',
                    confirmButtonColor: '#BD6F22'
                }).then(() => {
                    window.location.reload();
                });
                return;
            }

            const applicant = this.processingApplicants[this.currentIndex];
            const progress = `(${this.currentIndex + 1}/${this.processingApplicants.length})`;

            switch(this.actionType) {
                case 'invitation':
                    this.openContractSigningInvitation(applicant.name, applicant.application_id);
                    break;
                case 'promote':
                    this.promptPromote(applicant, progress);
                    break;
                case 'archive':
                    this.promptArchive(applicant, progress);
                    break;
            }
        }
    };
}
</script>

<script>
function requirementsModal() {
    return {
        requirementsOpen: false,
        requirementsApplicantName: '',
        requirementsApplicantId: null,
        requirementsFile201: null,
        requirementsOtherFiles: [],
        requiredDocs: [
            'Barangay Clearance',
            'NBI Clearance',
            'Police Clearance',
            'Medical Certificate',
            'Birth Certificate'
        ],

        isSubmitted(doc) {
            return this.requirementsOtherFiles.some(f => f.type === doc);
        },
        hasMissingRequirements() {
            return this.requiredDocs.some(doc => !this.isSubmitted(doc));
        },
        
       async openRequirements(name, user_id) {
        this.requirementsApplicantName = name;
        this.requirementsApplicantId = user_id;
        this.requirementsOpen = true;

        try {
            const response = await fetch(`/hrStaff/requirements/${user_id}`);
            if (!response.ok) throw new Error('Failed to fetch applicant files');

            const data = await response.json();
            this.requirementsFile201 = data.file201;
            this.requirementsOtherFiles = data.otherFiles;
        } catch (error) {
            console.error(error);
            Swal.fire({
                title: 'Error',
                text: 'Unable to load applicant requirements.',
                icon: 'error',
                confirmButtonColor: '#BD6F22'
            });
        }
    },
        
        async sendEmailRequirements() {
            if (!this.requirementsApplicantId) return;

            // Show loading indicator
            Swal.fire({
                title: 'Sending Email...',
                html: 'Please wait while we send the requirements email.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(`/hrStaff/applicants/${this.requirementsApplicantId}/send-missing-requirements`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                // Close loading and show success
                Swal.fire({
                    title: 'Success',
                    html: `
                        <p class="mb-2">${data.message || 'Requirements email sent successfully!'}</p>
                        ${data.notified_at ? `<p class="text-sm text-gray-600">Sent at: ${data.notified_at}</p>` : ''}
                        ${data.reminder_text ? `<p class="text-sm font-semibold text-orange-600 mt-1">This is the ${data.reminder_text}</p>` : ''}
                        <p class="text-xs text-gray-500 mt-3">Previous notification replaced. You can send additional reminders if needed.</p>
                    `,
                    icon: 'success',
                    confirmButtonColor: '#BD6F22'
                });

            } catch (error) {
                console.error(error);
                // Close loading and show error
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to send requirements email. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#BD6F22'
                });
            }
        },

        closeRequirements() {
            this.requirementsOpen = false;
        }
    }
}




function evaluationModal(applicants) {
    return {
        showModal: false,
        showAll: false,
        selectedEmployee: '',
        selectedApplicationId: null,
        alreadyEvaluated: false,
        scores: { knowledge: 0, skill: 0, participation: 0, professionalism: 0 },
        categories: {
            knowledge: ['I. Knowledge & Understanding', 30],
            skill: ['II. Skill Application', 30],
            participation: ['III. Participation & Engagement', 20],
            professionalism: ['IV. Professionalism & Attitude', 20]
        },
        totalScore: 0,
        result: '',

        init() {
            // Initialization if needed
        },

        openModal(employeeName, applicationId, evaluated = false, previousScores = null) {
            this.selectedEmployee = employeeName;
            this.selectedApplicationId = applicationId;
            this.showModal = true;
            this.alreadyEvaluated = evaluated;

            if (evaluated && previousScores) {
                this.scores.knowledge = previousScores.knowledge_score ?? 0;
                this.scores.skill = previousScores.skill_score ?? 0;
                this.scores.participation = previousScores.participation_score ?? 0;
                this.scores.professionalism = previousScores.professionalism_score ?? 0;
            } else {
                for (let key in this.scores) this.scores[key] = 0;
            }

            this.computeResult();
        },

        validateScore(key) {
            if(this.alreadyEvaluated) return;
            const max = this.categories[key][1];
            if (this.scores[key] > max) this.scores[key] = max;
            else if (this.scores[key] < 0 || isNaN(this.scores[key])) this.scores[key] = 0;
            this.computeResult();
        },

        computeResult() {
            let sum = Object.entries(this.categories).reduce((acc, [key, [, max]]) => {
                const score = this.scores[key] || 0;
                return acc + Math.min(score, max);
            }, 0);
            this.totalScore = sum;
            this.result = this.totalScore >= 70 ? 'Passed' : 'Failed';
        },

        submitEvaluation() {
            this.computeResult();
            const passed = this.result === 'Passed';

            Swal.fire({
                title: 'Evaluation Submitted!',
                html: `<p><strong>${this.selectedEmployee}</strong> has been evaluated.</p>
                       <p>Result: <strong>${this.result}</strong></p>
                       <p>Total Score: <strong>${this.totalScore}</strong></p>
                       ${passed ? '<p class="mt-2 text-sm text-gray-600">The applicant will now appear in the <strong>Passer</strong> tab.</p>' : ''}`,
                icon: passed ? 'success' : 'error',
                confirmButtonColor: '#BD6F22'
            }).then(() => {
                // Submit the form traditionally (server redirect will handle reload)
                this.$refs.evaluationForm.submit();
            });
        },

        openContractSigningInvitation(employeeName, applicationId) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDate = tomorrow.toISOString().split('T')[0];

            Swal.fire({
                title: `Send Contract Signing Invitation`,
                html: `
                    <div class="text-left space-y-4">
                        <p class="text-sm text-gray-600 mb-4">
                            <strong>${employeeName}</strong> has passed the evaluation. Schedule the contract signing.
                        </p>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Signing Date</label>
                            <input type="date" id="contract_date"
                                   class="w-full px-3 py-2 border rounded"
                                   min="${minDate}" required>

                            <label class="block text-sm text-gray-700 mb-1 mt-2">Signing Time</label>
                            <div class="flex gap-2">
                                <select id="contract_hour" class="flex-1 px-2 py-2 border rounded">
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8" selected>8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <select id="contract_minute" class="flex-1 px-2 py-2 border rounded">
                                    <option value="00" selected>00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                                <select id="contract_period" class="px-3 py-2 border rounded bg-gray-100">
                                    <option value="AM" selected>AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Send Invitation',
                cancelButtonText: 'Skip',
                didOpen: () => {
                    // Auto-update period AM/PM based on hour
                    const hourSelect = document.getElementById('contract_hour');
                    const periodAMPM = document.getElementById('contract_period');

                    hourSelect.addEventListener('change', () => {
                        const hour = parseInt(hourSelect.value);
                        if (hour >= 6 && hour <= 11) {
                            periodAMPM.value = 'AM';
                        } else {
                            periodAMPM.value = 'PM';
                        }
                    });
                },
                preConfirm: () => {
                    const contractDate = document.getElementById('contract_date').value;
                    const contractHour = document.getElementById('contract_hour').value;
                    const contractMinute = document.getElementById('contract_minute').value;
                    const contractPeriod = document.getElementById('contract_period').value;

                    if (!contractDate) {
                        Swal.showValidationMessage('Please fill in the signing date');
                        return false;
                    }

                    // Edge Case: Validate date/time is not in the past
                    const selectedDateTime = new Date(`${contractDate} ${contractHour}:${contractMinute} ${contractPeriod}`);
                    const now = new Date();

                    if (selectedDateTime <= now) {
                        Swal.showValidationMessage('The scheduled date and time cannot be in the past or current time');
                        return false;
                    }

                    // Edge Case: Validate business hours (6 AM - 5 PM)
                    let hour24 = parseInt(contractHour);
                    if (contractPeriod === 'PM' && hour24 !== 12) {
                        hour24 += 12;
                    } else if (contractPeriod === 'AM' && hour24 === 12) {
                        hour24 = 0;
                    }

                    if (hour24 < 6 || hour24 >= 17) {
                        Swal.showValidationMessage('Contract signing must be scheduled during business hours (6:00 AM - 5:00 PM)');
                        return false;
                    }

                    return {
                        contract_date: contractDate,
                        contract_signing_time: `${contractHour}:${contractMinute} ${contractPeriod}`
                    };
                }
            }).then(async (result) => {
                if (result.isConfirmed && result.value) {
                    // Show loading state
                    Swal.fire({
                        title: 'Sending invitation...',
                        html: 'Please wait while we send the contract signing invitation.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit contract signing schedule
                    try {
                        const response = await fetch(`/hrStaff/contract-schedule/${applicationId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(result.value)
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.message || 'Failed to send invitation');
                        }

                        const responseData = await response.json();

                        Swal.fire({
                            title: 'Success!',
                            text: responseData.message || 'Contract signing invitation sent successfully.',
                            icon: responseData.email_sent === false ? 'warning' : 'success',
                            confirmButtonColor: '#BD6F22',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } catch (error) {
                        Swal.fire({
                            title: 'Error',
                            html: `<p class="text-sm">${error.message}</p>`,
                            icon: 'error',
                            confirmButtonColor: '#BD6F22',
                            allowOutsideClick: false
                        });
                    }
                } else {
                    // User clicked skip - just reload
                    window.location.reload();
                }
            });
        },

        confirmPromotion(event, applicationId, employeeName) {
            Swal.fire({
                title: 'Promote Applicant?',
                text: `Are you sure you want to promote ${employeeName} to employee?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, promote!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });
        },

        confirmPromotionById(applicationId, employeeName) {
            Swal.fire({
                title: 'Promote Applicant?',
                text: `Are you sure you want to promote ${employeeName} to employee?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, promote!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed) {
                    document.getElementById('promote-form-' + applicationId).submit();
                }
            });
        }
    };
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

     document.addEventListener('submit', function (e) {
        if (e.target.matches('.contractschedule-form')) {
            e.preventDefault();
            Swal.fire({
                title: 'Set Contract Dates?',
                text: "Do you want to save this contract dates ?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, save!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        }
    });
    // ✅ Event Delegation for schedule-form
    document.addEventListener('submit', function (e) {
        if (e.target.matches('.schedule-form')) {
            e.preventDefault();
            Swal.fire({
                title: 'Set Contract Schedule?',
                text: "Do you want to save this contract signing schedule?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, save!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        }
    });

    // ✅ Event Delegation for archive-form
    document.addEventListener('submit', function (e) {
        if (e.target.matches('.archive-form')) {
            e.preventDefault();
            Swal.fire({
                title: 'Archive Applicant?',
                text: "This applicant will be moved to the archive list.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6B7280',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        }
    });

    // ✅ Success Toast
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: @json(session('success')),
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    @endif

});
</script>

