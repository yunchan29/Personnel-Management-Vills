@extends('layouts.hrStaff')

@section('content')
<section class="p-6 max-w-6xl mx-auto" x-data="evaluationModal()">
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
            :class="[
                'pb-2 focus:outline-none',
                tab === 'evaluation' ? 'text-[#BD9168] border-b-2 border-[#BD9168]' : 'hover:text-[#BD9168]',
                !selectedJobId ? 'text-gray-400 cursor-not-allowed pointer-events-none' : ''
            ]">
            Evaluation
        </button>
    </div>

    <!-- Job Listings -->
    <div x-show="tab === 'job_postings'" x-transition>
        @foreach ($jobs as $job)
            <x-hrStaff.jobListingDisplay :job="$job" />
        @endforeach
    </div>
    
<!-- Evaluation Tab -->
<div x-ref="evaluationSection" x-show="tab === 'evaluation'" x-transition x-cloak>
    <template x-if="selectedJobId">
        <div class="bg-white rounded-lg shadow-lg">

        <!-- Currently Evaluating Notice -->
<div 
    x-data="{ showNotice: true }"
    x-show="showNotice"
    x-transition
class="mb-4 bg-blue-100 border border-blue-300 text-blue-800 px-4 py-3 rounded-lg flex justify-between items-center"
>
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
    <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="border-b font-semibold bg-gray-50">
            <tr>
                <th class="py-3 px-4">Name</th>
                <th class="py-3 px-4">Start</th>
                <th class="py-3 px-4">End</th>
                <th class="py-3 px-4">Action</th>
                <th class="py-3 px-4">Progress</th>
            </tr>
        </thead>
        
     <tbody>
    @foreach ($applicants as $applicant)
        <tr 
            x-show="shouldShow({{ $applicant->job_id }}, {{ $applicant->evaluation ? 'true' : 'false' }})"
            class="border-b hover:bg-gray-50"
        >
            <td class="py-3 px-4 font-medium whitespace-nowrap flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full {{ $applicant->user->active_status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                {{ $applicant->user->full_name }}
            </td>
            <td class="py-3 px-4 whitespace-nowrap">{{ $applicant->trainingSchedule->start_date ?? '-' }}</td>
            <td class="py-3 px-4 whitespace-nowrap">{{ $applicant->trainingSchedule->end_date ?? '-' }}</td>
            <td class="py-3 px-4 whitespace-nowrap">
                <button 
                    @click="openModal('{{ $applicant->user->full_name }}', {{ $applicant->id }})"
                    class="bg-[#BD6F22] hover:bg-[#a55f1d] text-white text-sm font-medium h-8 px-3 rounded shadow"
                >
                    Evaluate
                </button>
            </td>
            <td class="py-3 px-4 whitespace-nowrap">
                @if ($applicant->evaluation)
                    <span class="text-xs px-2 py-1 rounded-full font-semibold text-white 
                        {{ strtolower($applicant->evaluation->result) === 'passed' ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ strtolower($applicant->evaluation->result) === 'passed' ? 'Hired' : 'Failed' }}
                    </span>
                @else
                    <span class="text-sm text-gray-400 italic">Pending</span>
                @endif
            </td>
        </tr>
    @endforeach

    <!-- "No applicants" row -->
    <tr x-show="!document.querySelectorAll('tbody tr[style*=\'display: table-row\']').length">
        <td colspan="5" class="text-center py-6 text-gray-500 italic">
            No applicants yet
        </td>
    </tr>
</tbody>

    </table>
</div>

<!-- Show All Toggle (centered) -->
<div class="flex justify-center px-6 pb-4">
    <button
        @click="showAll = !showAll"
        class="text-sm text-[#BD6F22] hover:underline focus:outline-none"
    >
        <span x-text="showAll ? 'Hide Evaluated' : 'Show All'"></span>
    </button>
</div>

    <!-- Evaluation Modal -->
    <x-hrStaff.evaluationModal />
</section>
@endsection

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function evaluationModal() {
        return {
            tab: 'job_postings',
            selectedJobId: null,
            selectedJobTitle: '',
            selectedCompany: '',
            showModal: false,
            showAll: false,
            selectedEmployee: '',
            selectedApplicationId: null,
            result: '',
            scores: {
                knowledge: 0,
                skill: 0,
                participation: 0,
                professionalism: 0
            },
            categories: {
                knowledge: ['I. Knowledge & Understanding', 30],
                skill: ['II. Skill Application', 30],
                participation: ['III. Participation & Engagement', 20],
                professionalism: ['IV. Professionalism & Attitude', 20]
            },
            get totalScore() {
                return Object.entries(this.categories).reduce((sum, [key, [, max]]) => {
                    const score = this.scores[key] || 0;
                    return sum + Math.min(score, max);
                }, 0);
            },
            get computedResult() {
                return this.totalScore >= 70 ? 'Passed' : 'Failed';
            },
            validateScore(key) {
                const max = this.categories[key][1];
                if (this.scores[key] > max) {
                    this.scores[key] = max;
                } else if (this.scores[key] < 0 || isNaN(this.scores[key])) {
                    this.scores[key] = 0;
                }
                this.result = this.computedResult;
            },
            openModal(employeeName, applicationId) {
                this.selectedEmployee = employeeName;
                this.selectedApplicationId = applicationId;
                this.showModal = true;
                for (let key in this.scores) {
                    this.scores[key] = 0;
                }
                this.result = this.computedResult;
            },
            shouldShow(jobId, isEvaluated) {
                return this.selectedJobId == jobId && (this.showAll || !isEvaluated);
            }
        };
    }
</script>
