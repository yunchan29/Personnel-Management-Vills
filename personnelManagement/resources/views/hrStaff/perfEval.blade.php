@extends('layouts.hrStaff')

@section('content')
<section class="p-6 max-w-6xl mx-auto" x-data="{ tab: 'postings', selectedJobId: null }">
    <h1 class="mb-6 text-xl font-bold text-[#BD6F22]">Performance Evaluation</h1>

    {{-- Tabs --}}
    <div class="flex border-b border-gray-300 mb-6 space-x-6">
        <button 
            @click="tab = 'postings'" 
            :class="tab === 'postings' ? 'text-[#BD6F22] border-b-2 border-[#BD6F22]' : 'text-gray-600'" 
            class="pb-2 font-semibold focus:outline-none"
        >
            Job Postings
        </button>
        <button 
            @click="tab = 'employees'" 
            :class="tab === 'employees' ? 'text-[#BD6F22] border-b-2 border-[#BD6F22]' : 'text-gray-600'" 
            class="pb-2 font-semibold focus:outline-none"
            :disabled="!selectedJobId"
        >
            Evaluation
        </button>
    </div>

    {{-- Job Postings Tab --}}
    <div x-show="tab === 'postings'" x-transition>
        <div class="grid gap-6">
            @forelse($jobs as $job)
                <div class="bg-white border shadow-md rounded-md px-6 py-4 flex justify-between items-start">
                    <div class="space-y-2 max-w-lg">
                        <div>
                            <h2 class="text-lg font-semibold text-[#BD6F22]">{{ $job->job_title }}</h2>
                            <p class="text-sm text-gray-600">{{ $job->company }}</p>
                        </div>

                        <div class="text-sm text-gray-800">
                            <p class="font-medium mb-1">Qualification :</p>
                            <ul class="list-disc list-inside text-sm space-y-1">
                                @foreach($job->qualifications as $qual)
                                    <li>{{ $qual }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#BD6F22]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C8.13401 2 5 5.13401 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.86599-3.134-7-7-7z" />
                                <circle cx="12" cy="9" r="2.5" fill="currentColor" />
                            </svg>
                            <span>{{ $job->location ?? 'No location' }}</span>
                        </div>

                        <span class="text-xs text-[#BD6F22] mt-1 inline-block cursor-pointer hover:underline">See More</span>
                    </div>

                    <div class="flex flex-col items-end justify-between h-full">
                        <button 
                            @click="tab = 'employees'; selectedJobId = {{ $job->id }}" 
                            class="text-sm font-medium text-gray-400 hover:text-gray-600 focus:outline-none"
                        >
                            View Employee
                        </button>
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs text-white bg-red-600 rounded-full mt-2">
                            {{ $employees->where('job_id', $job->id)->count() }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-gray-600 text-lg">No job postings found.</p>
            @endforelse
        </div>
    </div>

    {{-- Employees Tab --}}
    <div x-show="tab === 'employees'" x-transition>
        <template x-if="selectedJobId">
            <div>
                @php
                    $jobsMap = $jobs->keyBy('id');
                @endphp

                @foreach($jobs as $job)
                    <div x-show="selectedJobId === {{ $job->id }}">
                        <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">{{ $job->job_title }}</h2>

                        @php
                            $filteredEmployees = $employees->where('job_id', $job->id);
                        @endphp

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
                                                    <a href="{{ $employee->resume_url }}" class="inline-block bg-[#BD6F22] text-white text-sm font-medium px-4 py-2 rounded hover:bg-[#a55f1d] transition">
                                                        See Attachment
                                                    </a>
                                                </td>
                                                <td class="py-4 px-6">
                                                    <a href="{{ $employee->file_201_url }}" class="inline-block bg-[#BD6F22] text-white text-sm font-medium px-4 py-2 rounded hover:bg-[#a55f1d] transition">
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
        </template>
    </div>
</section>
@endsection
