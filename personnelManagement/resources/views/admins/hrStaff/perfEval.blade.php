@extends('layouts.hrStaff')

@section('content')
<section class="p-6 max-w-6xl mx-auto">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Training Evaluation</h1>
    <hr class="border-t border-gray-300 mb-6">

<div x-data="applicantsHandler()" x-init="init()" class="relative">
    <div x-data="{
        ...evaluationModal({{ $applicants }}),
        ...requirementsModal(),
        ...actionDropdown()
    }">

    <!-- Applicants List for Evaluation -->
    <div class="bg-white rounded-lg shadow-lg p-6">
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

            <!-- Evaluate Button -->
            <button
                @click="bulkEvaluate()"
                class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2 hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 5h6M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z" />
                </svg>
                <span class="text-sm" x-text="`Evaluate (${selectedApplicants.length})`"></span>
            </button>

            <!-- Set Contract Signing Button -->
            <button
                @click="bulkSetContractSigning()"
                class="min-w-[180px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2 hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm" x-text="`Contract Signing (${selectedApplicants.length})`"></span>
            </button>

            <!-- Set Contract Dates Button -->
            <button
                @click="bulkSetContractDates()"
                class="min-w-[180px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2 hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 2h6a2 2 0 012 2v2h-2V4H9v2H7V4a2 2 0 012-2zM7 8h10v12a2 2 0 01-2 2H9a2 2 0 01-2-2V8z"/>
                </svg>
                <span class="text-sm" x-text="`Contract Dates (${selectedApplicants.length})`"></span>
            </button>

            <!-- View Requirements Button -->
            <button
                @click="bulkViewRequirements()"
                class="min-w-[180px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2 hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M3 7h4l2-2h8l2 2h4v12H3V7z" />
                </svg>
                <span class="text-sm" x-text="`Requirements (${selectedApplicants.length})`"></span>
            </button>

            <!-- Promote Button -->
            <button
                @click="bulkPromote()"
                class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2 hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <span class="text-sm" x-text="`Promote (${selectedApplicants.length})`"></span>
            </button>

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
        </tr>
    </thead>
    <tbody>
        @forelse ($applicants->filter(function($app) {
            return $app->status === 'scheduled_for_training'
                && $app->trainingSchedule;
        })->sortBy(fn($app) => $app->evaluation ? 1 : 0) as $applicant)
        <tr
            x-show="showAll || '{{ $applicant->status }}' !== 'hired'"
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


        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-6 text-gray-500 italic">
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

    <!-- Hidden Forms for Bulk Actions -->
    @foreach ($applicants->filter(function($app) {
        return $app->status === 'scheduled_for_training' && $app->trainingSchedule;
    }) as $applicant)
        <!-- Promote Form -->
        @if($applicant->status !== 'hired')
        <form method="POST" action="{{ route('hrStaff.evaluation.promote', $applicant->id) }}" style="display: none;" id="promote-form-{{ $applicant->id }}">
            @csrf
        </form>
        @endif

        <!-- Archive Form -->
        @if($applicant->status !== 'hired')
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
            this.startSteppedAction('evaluate', this.selectedApplicants);
        },

        bulkSetContractSigning() {
            if (this.selectedApplicants.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }
            this.startSteppedAction('contractSigning', this.selectedApplicants);
        },

        bulkSetContractDates() {
            if (this.selectedApplicants.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }
            this.startSteppedAction('contractDates', this.selectedApplicants);
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
            this.startSteppedAction('requirements', this.selectedApplicants);
        },

        bulkPromote() {
            if (this.selectedApplicants.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant to promote.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }
            this.startSteppedAction('promote', this.selectedApplicants);
        },

        bulkArchive() {
            if (this.selectedApplicants.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one applicant to archive.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22'
                });
                return;
            }
            this.startSteppedAction('archive', this.selectedApplicants);
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
                case 'contractSigning':
                    this.openContractSigningModal(applicant, progress);
                    break;
                case 'contractDates':
                    this.openContractDatesModal(applicant, progress);
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

        openContractSigningModal(applicant, progress) {
            Swal.fire({
                title: `Set Contract Signing ${progress}`,
                html: `
                    <div class="text-left">
                        <p class="mb-4"><strong>Applicant:</strong> ${applicant.name}</p>
                        <p class="text-sm text-gray-600">This would open the contract signing modal. You can skip or continue to next applicant.</p>
                    </div>
                `,
                icon: 'info',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                denyButtonColor: '#6B7280',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Set & Next',
                denyButtonText: 'Skip',
                cancelButtonText: 'Stop'
            }).then((result) => {
                if (result.isConfirmed || result.isDenied) {
                    this.continueToNext();
                }
            });
        },

        openContractDatesModal(applicant, progress) {
            Swal.fire({
                title: `Set Contract Dates ${progress}`,
                html: `
                    <div class="text-left">
                        <p class="mb-4"><strong>Applicant:</strong> ${applicant.name}</p>
                        <p class="text-sm text-gray-600">This would open the contract dates modal. You can skip or continue to next applicant.</p>
                    </div>
                `,
                icon: 'info',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                denyButtonColor: '#6B7280',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Set & Next',
                denyButtonText: 'Skip',
                cancelButtonText: 'Stop'
            }).then((result) => {
                if (result.isConfirmed || result.isDenied) {
                    this.continueToNext();
                }
            });
        },

        openRequirementsForApplicant(applicant, progress) {
            this.openRequirements(applicant.name, applicant.user_id);
            // Continue after viewing
            setTimeout(() => {
                this.continueToNext();
            }, 1000);
        },

        promptPromote(applicant, progress) {
            Swal.fire({
                title: `Promote Applicant ${progress}`,
                text: `Are you sure you want to promote ${applicant.name} to employee?`,
                icon: 'question',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                denyButtonColor: '#6B7280',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, promote!',
                denyButtonText: 'Skip',
                cancelButtonText: 'Stop'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit promote form
                    const form = document.getElementById(`promote-form-${applicant.application_id}`);
                    if (form) {
                        form.submit();
                    }
                    this.continueToNext();
                } else if (result.isDenied) {
                    // Skip this one
                    this.continueToNext();
                }
            });
        },

        promptArchive(applicant, progress) {
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
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit archive form
                    const form = document.getElementById(`archive-form-${applicant.application_id}`);
                    if (form) {
                        form.submit();
                    }
                    this.continueToNext();
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

            try {
                const response = await fetch(`/hrStaff/applicants/${this.requirementsApplicantId}/send-missing-requirements`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                Swal.fire({
                    title: 'Success',
                    text: data.message || 'Requirements email sent successfully!',
                    icon: 'success',
                    confirmButtonColor: '#BD6F22'
                });

            } catch (error) {
                console.error(error);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to send requirements email.',
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
            Swal.fire({
                title: 'Evaluation Submitted!',
                html: `<p><strong>${this.selectedEmployee}</strong> has been evaluated.</p>
                       <p>Result: <strong>${this.result}</strong></p>
                       <p>Total Score: <strong>${this.totalScore}</strong></p>`,
                icon: this.result === 'Passed' ? 'success' : 'error',
                confirmButtonColor: '#BD6F22'
            }).then(() => {
                this.$refs.evaluationForm.submit();
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

