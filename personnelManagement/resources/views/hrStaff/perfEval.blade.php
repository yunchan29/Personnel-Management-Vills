@extends('layouts.hrStaff')

@section('content')
<section class="p-6 max-w-6xl mx-auto"
         x-data
         x-init="$store.performance.tab = '{{ $selectedTab ?? 'postings' }}'">

    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Performance Evaluation</h1>
    <hr class="border-t border-gray-300 mb-6">

    {{-- Alpine Store --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('performance', {
                tab: 'postings',
                selectedJobId: null
            });
        });
    </script>

    {{-- Tabs --}}
    <div class="flex space-x-8 text-sm font-medium text-gray-600 border-b border-gray-300 mb-6">
        <button 
            @click="$store.performance.tab = 'postings'"
            :class="$store.performance.tab === 'postings' 
                ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' 
                : 'hover:text-[#BD9168]'"
            class="pb-2 focus:outline-none">
            Job Postings
        </button>

        <button 
            @click="$store.performance.tab = 'employees'"
            x-bind:disabled="$store.performance.selectedJobId === null"
            :class="{
                'text-[#BD9168] border-b-2 border-[#BD9168] pb-2': $store.performance.tab === 'employees',
                'text-gray-400 cursor-not-allowed': $store.performance.selectedJobId === null,
                'hover:text-[#BD9168]': $store.performance.selectedJobId !== null
            }"
            class="pb-2 focus:outline-none">
            Evaluation
        </button>
    </div>

    {{-- Job Postings --}}
    <div x-show="$store.performance.tab === 'postings'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-6">

       @forelse($jobs as $job)
    <x-hrStaff.performanceJobListing 
        :job="$job" 
        :employeeCount="$employees->where('job_id', $job->id)->count()" 
    />
@empty
    <p class="text-gray-600 text-lg text-center">No job postings found.</p>
@endforelse

    </div>

    {{-- Employees Tab --}}
    <div x-show="$store.performance.tab === 'employees'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0">

        @php $jobsMap = $jobs->keyBy('id'); @endphp

        @foreach($jobs as $job)
            <div x-show="$store.performance.selectedJobId === {{ $job->id }}">
                <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">{{ $job->job_title }}</h2>

                @php $filteredEmployees = $employees->where('job_id', $job->id); @endphp

                @if($filteredEmployees->isEmpty())
                    <p class="text-gray-600">No employees found for this position.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white text-base">
                            <thead class="bg-gray-100 text-left text-gray-800">
                                <tr>
                                    <th class="py-4 px-6">Name</th>
                                    <th class="py-4 px-6">Company</th>
                                    <th class="py-4 px-6">Resume</th>
                                    <th class="py-4 px-6">201 File</th>
                                    <th class="py-4 px-6">Start</th>
                                    <th class="py-4 px-6">End</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filteredEmployees as $employee)
                                    <tr class="hover:bg-gray-50 border-b-2 border-gray-300">
                                        <td class="py-4 px-6 text-lg">{{ $employee->full_name }}</td>
                                        <td class="py-4 px-6 text-lg">{{ $employee->company ?? '—' }}</td>
                                        <td class="py-4 px-6">
                                            <a href="{{ $employee->resume_url }}" 
                                               class="inline-block bg-[#BD6F22] text-white text-sm font-medium px-4 py-2 rounded hover:bg-[#a55f1d] transition">
                                                See Attachment
                                            </a>
                                        </td>
                                        <td class="py-4 px-6">
                                            <a href="{{ $employee->file_201_url }}" 
                                               class="inline-block bg-[#BD6F22] text-white text-sm font-medium px-4 py-2 rounded hover:bg-[#a55f1d] transition">
                                                View
                                            </a>
                                        </td>
                                        <td class="py-4 px-6 text-lg">{{ $employee->start_date ?? '—' }}</td>
                                        <td class="py-4 px-6 text-lg">{{ $employee->end_date ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>
@endsection

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
