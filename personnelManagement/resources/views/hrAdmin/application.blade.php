@extends('layouts.hrAdmin')

@section('content')

<section class="p-6 max-w-6xl mx-auto"
         x-data
         x-init="$store.applications.tab = '{{ $selectedTab ?? 'postings' }}'">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Applications</h1>
    <hr class="border-t border-gray-300 mb-6">

    {{-- Alpine Store --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('applications', {
                tab: 'postings'
            });
        });
    </script>

    {{-- Tabs --}}
    <div class="flex space-x-8 text-sm font-medium text-gray-600 border-b border-gray-300 mb-6">
        <button @click="$store.applications.tab = 'postings'"
                :class="$store.applications.tab === 'postings' ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' : 'hover:text-[#BD9168]'"
                class="pb-2 focus:outline-none">Job Postings</button>

        <button @click="$store.applications.tab = 'applicants'"
                :class="$store.applications.tab === 'applicants' ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' : 'hover:text-[#BD9168]'"
                class="pb-2 focus:outline-none {{ !isset($selectedJob) ? 'text-gray-400 cursor-not-allowed' : '' }}"
                {{ !isset($selectedJob) ? 'disabled' : '' }}>Applicants</button>

        <button @click="$store.applications.tab = 'interview'"
                :class="$store.applications.tab === 'interview' ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' : 'hover:text-[#BD9168]'"
                class="pb-2 focus:outline-none">Interview Schedule</button>

        <button @click="$store.applications.tab = 'training'"
                :class="$store.applications.tab === 'training' ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' : 'hover:text-[#BD9168]'"
                class="pb-2 focus:outline-none">Training Schedule</button>
    </div>

    {{-- Job Postings --}}
    <div x-data="{ search: '' }" x-show="$store.applications.tab === 'postings'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-6">
        
        {{-- Search --}}
        <div class="flex items-center mb-6">
            <label for="search" class="mr-4 font-medium text-sm block">Search Position</label>
            <input type="text"
                id="search"
                name="search"
                x-model="search"
                class="flex-1 border border-gray-300 rounded-md p-2 text-sm"
                placeholder="Enter job title...">
            <button class="ml-4 bg-[#BD9168] text-white px-6 py-2 rounded-md hover:bg-[#a37654] transition text-sm flex items-center gap-2">
                <img src="{{ asset('images/Search2.png') }}" alt="Search" class="h-4 w-4">
                <span>Search</span>
            </button>
        </div>

        {{-- Listings --}}
        @forelse($jobs as $job)
            @php
                $jobTitle = Js::from(strtolower($job->job_title));
                $companyName = Js::from(strtolower($job->company_name));
                $location = Js::from(strtolower($job->location));
                $qualifications = Js::from(strtolower(implode(' ', $job->qualifications)));
                $additionalInfo = Js::from(strtolower(implode(' ', $job->additional_info ?? [])));
            @endphp

            <div 
                x-show="search === '' 
                    || {{ $jobTitle }}.includes(search.toLowerCase()) 
                    || {{ $companyName }}.includes(search.toLowerCase()) 
                    || {{ $location }}.includes(search.toLowerCase()) 
                    || {{ $qualifications }}.includes(search.toLowerCase()) 
                    || {{ $additionalInfo }}.includes(search.toLowerCase())"
                x-transition
            >
                <x-hradmin.applicationJobListing :job="$job" />
            </div>
        @empty
            <p class="text-center text-gray-500">No job applications available.</p>
        @endforelse
    </div>

    {{-- Applicants Tab --}}
    <div x-show="$store.applications.tab === 'applicants'" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0">
        @if(isset($selectedJob) && isset($applications))
            <div x-data="{ showNotice: true }"
                 x-show="showNotice"
                 x-transition
                 class="mb-4 bg-blue-100 border border-blue-300 text-blue-800 px-4 py-3 rounded-lg flex justify-between items-center">
                <span>
                    You are currently viewing applicants for: 
                    <strong class="text-[#BD6F22]">{{ $selectedJob->job_title }}</strong>
                </span>
                <button @click="showNotice = false" class="text-sm text-blue-600 hover:underline">Dismiss</button>
            </div>

            @include('hrAdmin.applicants', [
                'applications' => $applications,
                'selectedJob' => $selectedJob,
            ])
        @else
            <p class="text-gray-500 text-center">No applicants selected yet.</p>
        @endif
    </div>

    {{-- Interview Tab --}}
<div x-show="$store.applications.tab === 'interview'" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="space-y-4">

    @include('hrAdmin.interviewSchedule', ['interviewApplicants' => $interviewApplicants ?? null])

</div>


    {{-- Training Tab --}}
    {{-- Training Tab --}}
<div x-show="$store.applications.tab === 'training'" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="space-y-4">

    @if(isset($approvedApplicants) && $approvedApplicants->count() > 0)
        @include('hrAdmin.trainingSchedule', ['approvedApplicants' => $approvedApplicants])
    @else
        <p class="text-center text-gray-500">No approved applicants for training yet.</p>
    @endif

</div>


</section>

@endsection

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
