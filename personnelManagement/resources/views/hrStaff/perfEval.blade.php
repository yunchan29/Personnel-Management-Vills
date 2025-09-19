@extends('layouts.hrStaff')

@section('content')
<section class="p-6 max-w-6xl mx-auto"
         x-data="{ 
             ...evaluationModal({{ $applicants }}), 
             ...requirementsModal() 
         }"
         x-init="init()">

    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Training Evaluation</h1>
    <hr class="border-t border-gray-300 mb-6">

    <!-- Tabs -->
    <div class="flex space-x-8 text-sm font-medium text-gray-600 border-b border-gray-300 mb-6">
        <button 
            @click="tab = 'job_postings'"
            :class="tab === 'job_postings' 
                ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' 
                : 'hover:text-[#BD9168]'"
            class="pb-2 focus:outline-none">
            Job Postings
        </button>

        <button 
            @click="if (selectedJobId) tab = 'evaluation'"
            :class="[tab === 'evaluation' ? 'text-[#BD9168] border-b-2 border-[#BD9168]' : 'hover:text-[#BD9168]',
                     !selectedJobId ? 'text-gray-400 cursor-not-allowed pointer-events-none' : '']">
            Evaluation
        </button>
    </div>

    <!-- Job Listings -->
    <div x-show="tab === 'job_postings'" x-transition>
        @php
            $jobsToShow = $jobs;
        @endphp

        @if ($jobsToShow->isNotEmpty())
            @foreach ($jobsToShow as $job)
                <x-hrStaff.jobListingDisplay :job="$job" />
            @endforeach
        @else
            <div class="text-center py-6 text-gray-500 italic">
                No applicants available for any job postings.
            </div>
        @endif
    </div>

    <!-- Evaluation Tab -->
    <div x-show="tab === 'evaluation'" x-transition x-cloak>
        <template x-if="selectedJobId">
            <div class="bg-white rounded-lg shadow-lg">
                <!-- Currently Evaluating Notice -->
                <div x-data="{ showNotice: true }"
                    x-show="showNotice"
                    x-transition
                    class="mb-4 bg-blue-100 border border-blue-300 text-blue-800 px-4 py-3 rounded-lg flex justify-between items-center">
                    <span>
                        You are currently evaluating applicants for:
                        <strong class="text-[#1E3A8A]" x-text="selectedJobTitle"></strong> 
                        <span class="text-gray-500">—</span> 
                        <strong class="text-[#BD6F22]" x-text="selectedCompany"></strong>
                    </span>
                    <button @click="showNotice = false" class="text-sm text-blue-600 hover:underline">Dismiss</button>
                </div>

                <!-- Evaluation Table -->
                <div class="overflow-x-auto px-6 pb-4">
                   <table class="min-w-full text-sm text-left text-gray-700 align-middle">
    <thead class="border-b font-semibold bg-gray-50">
        <tr>
            <th class="py-3 px-4">Name</th>
            <th class="py-3 px-4">Job Position</th>
            <th class="py-3 px-4">Company</th>
            <th class="py-3 px-4">Status</th>
            <th class="py-3 px-4">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($applicants->sortBy(fn($app) => $app->evaluation || $app->status === 'hired') as $applicant)
        <tr 
            x-show="shouldShow('{{ $applicant->job_id }}', '{{ $applicant->status }}')"
            class="border-b hover:bg-gray-50"
        >
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

 <!-- Actions -->
<td class="py-3 px-4 align-middle whitespace-nowrap text-left">
    <div class="flex space-x-2">
        <!-- Evaluate -->
        <div class="relative group">
            <button 
                class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-100 hover:bg-blue-200 ring-2 ring-transparent hover:ring-blue-400 transition-all"
                @click="openModal(
                    {{ Js::from($applicant->user->full_name) }},
                    {{ Js::from($applicant->id) }},
                    {{ $applicant->evaluation ? 'true' : 'false' }},
                    {{ Js::from($applicant->evaluation ? [
                        'knowledge_score' => $applicant->evaluation->knowledge ?? 0,
                        'skill_score' => $applicant->evaluation->skill ?? 0,
                        'participation_score' => $applicant->evaluation->participation ?? 0,
                        'professionalism_score' => $applicant->evaluation->professionalism ?? 0
                    ] : null) }}
                )"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z" />
                </svg>
            </button>
            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 text-xs text-white bg-blue-600 rounded opacity-0 group-hover:opacity-100 transition">
                {{ $applicant->evaluation ? 'View Evaluation' : 'Evaluate' }}
            </span>
        </div>

        <!-- Set Schedule -->
        <div class="relative group">
            <button class="w-10 h-10 flex items-center justify-center rounded-full bg-purple-100 hover:bg-purple-200 ring-2 ring-transparent hover:ring-purple-400 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </button>
            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 text-xs text-white bg-purple-600 rounded opacity-0 group-hover:opacity-100 transition">
                Set Schedule
            </span>
        </div>

        <!-- Set Contract -->
        @if($applicant->status !== 'hired')
        <div class="relative group">
            <button class="w-10 h-10 flex items-center justify-center rounded-full bg-green-100 hover:bg-green-200 ring-2 ring-transparent hover:ring-green-400 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h10M7 15h6m2 6H9a2 2 0 01-2-2V5a2 2 0 012-2h6l6 6v10a2 2 0 01-2 2z" />
                </svg>
            </button>
            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 text-xs text-white bg-green-600 rounded opacity-0 group-hover:opacity-100 transition">
                Set Contract
            </span>
        </div>
        @endif

        <!-- View Requirements -->
        <div class="relative group">
            <button 
                class="w-10 h-10 flex items-center justify-center rounded-full bg-indigo-100 hover:bg-indigo-200 ring-2 ring-transparent hover:ring-indigo-400 transition-all"
                @click="openRequirements(
                    {{ Js::from($applicant->user->full_name) }},
                    {{ Js::from($applicant->id) }},
                    {{ Js::from($applicant->job->job_title ?? '') }},
                    {{ Js::from($applicant->job->company_name ?? '') }}
                )"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h4l2-2h8l2 2h4v12H3V7z" />
                </svg>
            </button>
            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 text-xs text-white bg-indigo-600 rounded opacity-0 group-hover:opacity-100 transition">
                View Requirements
            </span>
        </div>

        <!-- Promote -->
        @if($applicant->status !== 'hired')
        <form method="POST" action="{{ route('hrStaff.evaluation.promote', $applicant->id) }}">
            @csrf
            <div class="relative group">
                <button 
                    type="button"
                    class="w-10 h-10 flex items-center justify-center rounded-full bg-yellow-100 hover:bg-yellow-200 ring-2 ring-transparent hover:ring-yellow-400 transition-all"
                    @click="confirmPromotion($event, {{ $applicant->id }}, '{{ $applicant->user->full_name }}')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                </button>
                <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 text-xs text-white bg-yellow-600 rounded opacity-0 group-hover:opacity-100 transition">
                    Promote
                </span>
            </div>
        </form>
        @endif

        <!-- Archive -->
        @if($applicant->status !== 'hired')
        <form action="{{ route('hrStaff.archive.store', $applicant->id) }}" method="POST" class="archive-form">
            @csrf
            <div class="relative group">
                <button 
                    type="submit"
                    class="w-10 h-10 flex items-center justify-center rounded-full bg-red-100 hover:bg-red-200 ring-2 ring-transparent hover:ring-red-400 transition-all"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.75H3.75M19.5 21H4.5A2.25 2.25 0 012.25 18.75v-12m19.5 0v12A2.25 2.25 0 0119.5 21zM9 12h6" />
                    </svg>
                </button>
                <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 text-xs text-white bg-red-600 rounded opacity-0 group-hover:opacity-100 transition">
                    Archive
                </span>
            </div>
        </form>
        @endif
    </div>
</td>

        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-6 text-gray-500 italic">
                No applicants yet.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

                </div>

                <div class="flex justify-center px-6 pb-4">
                    <button
                        @click="showAll = !showAll"
                        class="text-sm text-[#BD6F22] hover:underline focus:outline-none"
                    >
                        <span x-text="showAll ? 'Hide Hired' : 'Show All'"></span>
                    </button>
                </div>

                <!-- Evaluation Modal -->
                <x-hrStaff.evaluationModal />

                <!-- Requirements Modal -->
                <x-hrStaff.requirementsModal />

            </div>
        </template>
    </div>
</section>

@endsection

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function requirementsModal() {
    return {
        requirementsOpen: false,
        requirementsApplicantName: '',
        requirementsApplicantId: null,
        requirementsJobTitle: '',
        requirementsCompanyName: '',
        requirementsFile201: null,
        requirementsOtherFiles: [],

        openRequirements(name, id, jobTitle, company) {
            this.requirementsOpen = true;
            this.requirementsApplicantName = name ?? '';
            this.requirementsApplicantId = id ?? null;
            this.requirementsJobTitle = jobTitle ?? '';
            this.requirementsCompanyName = company ?? '';

            fetch(`/hrStaff/requirements/${id}`)
                .then(res => res.json())
                .then(data => {
                    this.requirementsFile201 = data.file201 ?? null;
                    this.requirementsOtherFiles = data.otherFiles ?? [];
                })
                .catch(() => {
                    this.requirementsFile201 = null;
                    this.requirementsOtherFiles = [];
                });
        },

        closeRequirements() {
            this.requirementsOpen = false;
        }
    }
}




function evaluationModal(applicants) {
    return {
        tab: 'job_postings',
        selectedJobId: null,
        selectedJobTitle: '',
        selectedCompany: '',
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
            // listen for job selection events
            this.$el.addEventListener('select-job', (e) => {
                this.selectedJobId = e.detail.id;
                this.selectedJobTitle = e.detail.title;
                this.selectedCompany = e.detail.company;
                this.tab = 'evaluation';
            });
        },

        shouldShow(jobId, status) {
            if (jobId != this.selectedJobId) return false;
            return this.showAll || status !== 'hired';
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
        }
    };
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.archive-form').forEach(form => {
        form.addEventListener('submit', function (e) {
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
                    form.submit();
                }
            });
        });
    });

    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    @endif
});
</script>
