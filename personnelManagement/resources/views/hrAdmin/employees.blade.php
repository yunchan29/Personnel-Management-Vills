@extends('layouts.hrAdmin')

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
                @php $jobsMap = $jobs->keyBy('id'); @endphp

                @foreach($jobs as $job)
                    <div x-show="selectedJobId === {{ $job->id }}">
                        <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">{{ $job->job_title }}</h2>

                        @php
                            $filteredEmployees = $groupedEmployees[$job->id] ?? collect();
                        @endphp

                        <x-hrAdmin.employeeTable :employees="$filteredEmployees" />
                    </div>
                @endforeach
            </div>
        </template>
    </div>

    {{-- Modals --}}
    @include('components.hrAdmin.modals.resume')

    @foreach ($groupedEmployees->flatten() as $employee)
      <x-hrAdmin.modals.profile :user="$employee" />

    @endforeach
</section>

<!-- Optional Alpine Cloak -->
<style>[x-cloak] { display: none !important; }</style>
@endsection
