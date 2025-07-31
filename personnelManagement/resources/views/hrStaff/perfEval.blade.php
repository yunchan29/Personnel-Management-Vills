@extends('layouts.hrStaff')

@section('content')
<section class="p-6 max-w-6xl mx-auto"
         x-data="evaluationModal()">

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
            @click="tab = 'evaluation'"
            :class="tab === 'evaluation' 
                ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' 
                : 'hover:text-[#BD9168]'"
            class="pb-2 focus:outline-none">
            Evaluation
        </button>
    </div>

    <!-- Job Postings (placeholder only) -->
    <div x-show="tab === 'job_postings'" x-transition>
         <div class="flex flex-col items-center justify-center p-6 bg-orange-50 rounded-xl border border-dashed border-[#BD6F22] shadow-sm">
        
        <!-- Capybara with construction hat -->
    <img src="/images/capy.png" alt="..." class="border-4 border-dashed border-[#BD6F22] rounded-lg p-1" />


        <!-- Message -->
        <p class="text-center text-l text-gray-600 mb-1">
            Oops! This section is still under development.
        </p>
    
    </div>
    </div>

    <!-- Evaluation Table -->
    <div x-show="tab === 'evaluation'" x-transition class="overflow-x-auto">
        <table class="min-w-full bg-white text-base">
            <thead class="bg-gray-100 text-left text-gray-800">
                <tr>
                    <th class="py-4 px-6">Name</th>
                    <th class="py-4 px-6">Job Position</th>
                    <th class="py-4 px-6">Company</th>
                    <th class="py-4 px-6">Start</th>
                    <th class="py-4 px-6">End</th>
                    <th class="py-4 px-6">Action</th>
                    <th class="py-4 px-6">Progress</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="employee in dummyEmployees" :key="employee.id">
                    <tr class="hover:bg-gray-50 border-b border-gray-300">
                        <td class="py-4 px-6" x-text="employee.name"></td>
                        <td class="py-4 px-6" x-text="employee.job_title"></td>
                        <td class="py-4 px-6" x-text="employee.company"></td>
                        <td class="py-4 px-6" x-text="employee.start_date"></td>
                        <td class="py-4 px-6" x-text="employee.end_date"></td>
                        <td class="py-4 px-6">
                            <button 
                                @click="openModal(employee.name)"
                                class="bg-[#BD6F22] hover:bg-[#a55f1d] text-white px-4 py-2 rounded">
                                Evaluate
                            </button>
                        </td>
                        <td class="py-4 px-6">
                            <template x-if="employee.progress === 'Passed'">
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-green-500 text-white">Passed</span>
                            </template>
                            <template x-if="employee.progress === 'Failed'">
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-red-500 text-white">Failed</span>
                            </template>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

   <!-- Evaluation Modal -->
<div x-show="showModal"
     x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl" @click.away="showModal = false">
        <h2 class="text-xl font-bold text-[#BD6F22] mb-4">Training Evaluation</h2>
        <p class="text-sm text-gray-600 mb-4">Employee: <span class="font-semibold" x-text="selectedEmployee"></span></p>

        <div class="space-y-4 mb-6">
            <template x-for="(label, key) in categories" :key="key">
                <div class="flex justify-between items-center">
                    <span x-text="label[0]"></span>
                    <div class="relative">
                        <input type="number"
                            x-model.number="scores[key]"
                            @input="validateScore(key)"
                            :max="label[1]"
                            min="0"
                            class="border border-gray-300 rounded pl-2 pr-10 py-1 w-28 text-right focus:ring-[#BD6F22]"
                            placeholder="0" />
                        <span class="absolute inset-y-0 right-2 flex items-center text-sm text-gray-500" x-text="'/' + label[1]"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Overall Score -->
        <div class="mb-6 text-right text-sm text-gray-700 font-semibold">
            Overall Score: <span x-text="totalScore + '/100'"></span>
        </div>

       <div class="mb-6">
    <label class="block font-medium text-sm mb-1">Result</label>
    <div class="flex items-center space-x-3">
        <input type="text" x-model="result" readonly class="border border-gray-300 bg-gray-100 rounded px-3 py-2 w-full" />
        
        <!-- Badge -->
        <template x-if="result === 'Passed'">
            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-green-500 text-white">Passed</span>
        </template>
        <template x-if="result === 'Failed'">
            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-red-500 text-white">Failed</span>
        </template>
    </div>
</div>



        <div class="text-sm text-gray-700 border-t pt-4 mt-4">
            <p class="font-medium mb-2">Scoring and Interpretation:</p>
            <div class="grid grid-cols-2 gap-2">
                <div>70 – 100</div><div>Passed</div>
                <div>60 and below</div><div>Failed</div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button @click="showModal = false"
                    class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">
                Cancel
            </button>
            <button @click="submitEvaluation()"
                    class="bg-[#BD6F22] hover:bg-[#a55f1d] text-white px-4 py-2 rounded">
                Submit
            </button>
        </div>
    </div>
</div>

</section>
@endsection

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function evaluationModal() {
        return {
            tab: 'evaluation',
            showModal: false,
            selectedEmployee: '',
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
            dummyEmployees: [
                {
                    id: 1,
                    name: 'Charlene S. Manzanilla',
                    job_title: 'Production Operator',
                    company: 'Yazaki-Torres Manufacturing Inc.',
                    start_date: 'May 5, 2025',
                    end_date: 'May 9, 2025',
                    progress: 'Passed'
                },
                {
                    id: 2,
                    name: 'John Doe',
                    job_title: 'Machine Technician',
                    company: 'ABC Corporation',
                    start_date: 'May 1, 2025',
                    end_date: 'May 15, 2025',
                    progress: 'Failed'
                }
            ],
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

                // Update result live
                this.result = this.computedResult;
            },
            openModal(employeeName) {
                this.selectedEmployee = employeeName;
                this.showModal = true;

                for (let key in this.scores) {
                    this.scores[key] = 0;
                }

                // Ensure result resets to starting value
                this.result = this.computedResult;
            },
            submitEvaluation() {
                alert(`Submitted for ${this.selectedEmployee} with ${this.totalScore}/100. Result: ${this.result}`);
                this.showModal = false;
            }
        };
    }
</script>
