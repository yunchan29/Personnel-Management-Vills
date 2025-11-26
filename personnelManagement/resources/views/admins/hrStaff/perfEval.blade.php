@extends('layouts.hrStaff')

@section('content')
<section class="p-6 max-w-6xl mx-auto">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Training Evaluation</h1>
    <hr class="border-t border-gray-300 mb-6">

<div x-data="applicantsHandler()" x-init="init()" class="relative">
    <div x-data="{
        ...perfEvalHandler(),
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
                && $app->trainingSchedule
                && (!$app->evaluation || $app->evaluation->result !== 'Passed');
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
                        @click="checkAndOpenModal('{{ $applicant->user->full_name }}', {{ $applicant->id }}, {{ $applicant->evaluation ? 'true' : 'false' }})"
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
        return in_array($app->status->value, ['trained', 'for_evaluation', 'scheduled_for_training'])
            && $app->trainingSchedule
            && (!$app->evaluation || $app->evaluation->result !== 'Passed');
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
<script src="{{ asset('js/utils/holidayUtils.js') }}"></script>
<script src="{{ asset('js/applicantsHandler.js') }}"></script>
<script src="{{ asset('js/perfEvalHandler.js') }}"></script>
