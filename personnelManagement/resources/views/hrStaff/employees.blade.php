@extends('layouts.hrStaff')

@section('content')
<section class="p-6 max-w-6xl mx-auto" x-data="{ tab: 'postings', selectedJobId: null }">
    <h1 class="mb-6 text-xl font-bold text-[#BD6F22]">Employees</h1>

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
            Employee
        </button>
    </div>

    {{-- Job Postings Tab --}}
    <div x-show="tab === 'postings'" x-transition>
        <div class="grid gap-6">
            @forelse($jobs as $job)
                @php
                    $jobEmployees = $employees->where('job_id', $job->id);
                @endphp

                @if($jobEmployees->isNotEmpty())
                    <div class="bg-white border shadow-sm rounded-md p-6 flex flex-col gap-3">
                        <!-- Job Title + Company -->
                        <div>
                            <h2 class="text-lg font-semibold text-[#BD6F22]">{{ $job->job_title }}</h2>
                            <p class="text-sm text-gray-600">{{ $job->company_name }}</p>
                        </div>

                        <!-- Location + View Employee -->
                        <div class="flex justify-between items-center text-sm text-gray-700">
                            <div class="flex items-center gap-1">
                                <img src="{{ asset('images/location.png') }}" alt="Location" class="w-4 h-4 object-contain">
                                <span>{{ $job->location ?? 'No location' }}</span>
                            </div>

                            <div class="flex gap-4 items-center">
                                <button 
                                    @click="tab = 'employees'; selectedJobId = {{ $job->id }}" 
                                    class="relative text-sm font-medium text-[#BD6F22] hover:underline flex items-center gap-2"
                                >
                                    View
                                    <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                        {{ $jobEmployees->count() }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-gray-600 text-lg">No job postings found.</p>
            @endforelse
        </div>
    </div>

    {{-- Employees Tab --}}
    <div x-show="tab === 'employees'" x-transition>
        <template x-if="selectedJobId">
            <div>
                @foreach($jobs as $job)
                    @php
                        $filteredEmployees = $employees->where('job_id', $job->id);
                    @endphp

                    @if($filteredEmployees->isNotEmpty())
                        <div x-show="selectedJobId === {{ $job->id }}">
                            <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">{{ $job->job_title }}</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white text-base">
                                    <thead class="bg-gray-100 text-left text-gray-800">
                                        <tr>
                                            <th class="py-4 px-6">Name</th>
                                            <th class="py-4 px-6">Company</th>
                                            <th class="py-4 px-6">Resume</th>
                                            <th class="py-4 px-6">Profile</th>
                                            <th class="py-4 px-6">Duration</th>
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
                                                <td class="py-4 px-6">
                                                    <select class="bg-white text-gray-700 border border-gray-300 text-sm font-medium px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-[#BD6F22] transition">
                                                        <option value="" disabled selected>Set Duration</option>
                                                        <option value="6months">6 Months</option>
                                                        <option value="1year">1 Year</option>
                                                    </select>
                                                </td>
                                                <td class="py-4 px-6 text-lg">{{ $employee->start_date ?? '—' }}</td>
                                                <td class="py-4 px-6 text-lg">{{ $employee->end_date ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </template>
    </div>
</section>
@endsection
