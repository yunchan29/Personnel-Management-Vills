@extends('layouts.hrAdmin')

@section('content')
<section 
    class="p-6 max-w-6xl mx-auto"
    x-data
    x-init="$store.applications.tab = '{{ $selectedTab ?? 'postings' }}'"
>
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
    @php $hasJob = isset($selectedJob); @endphp
    <div class="flex space-x-8 text-sm font-medium text-gray-600 border-b border-gray-300 mb-6">
        <!-- Job Postings -->
        <button
            @click="$store.applications.tab = 'postings'"
            :class="$store.applications.tab === 'postings' 
                ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' 
                : 'hover:text-[#BD9168]'"
            class="pb-2 focus:outline-none"
        >
            Job Postings
        </button>

        <!-- Applicants -->
        <button
            x-on:click="$store.applications.tab = 'applicants'"
            x-bind:disabled="{{ $hasJob ? 'false' : 'true' }}"
            :class="{
                'text-[#BD9168] border-b-2 border-[#BD9168] pb-2': $store.applications.tab === 'applicants',
                'text-gray-400 cursor-not-allowed': {{ $hasJob ? 'false' : 'true' }},
                'hover:text-[#BD9168]': {{ $hasJob ? 'true' : 'false' }}
            }"
            class="pb-2 focus:outline-none"
        >
            Applicants
        </button>

        <!-- Interview Schedule -->
        <button
            x-on:click="$store.applications.tab = 'interview'"
            x-bind:disabled="{{ $hasJob ? 'false' : 'true' }}"
            :class="{
                'text-[#BD9168] border-b-2 border-[#BD9168] pb-2': $store.applications.tab === 'interview',
                'text-gray-400 cursor-not-allowed': {{ $hasJob ? 'false' : 'true' }},
                'hover:text-[#BD9168]': {{ $hasJob ? 'true' : 'false' }}
            }"
            class="pb-2 focus:outline-none"
        >
            Interview Schedule
        </button>

        <!-- Training Schedule -->
        <button
            x-on:click="$store.applications.tab = 'training'"
            x-bind:disabled="{{ $hasJob ? 'false' : 'true' }}"
            :class="{
                'text-[#BD9168] border-b-2 border-[#BD9168] pb-2': $store.applications.tab === 'training',
                'text-gray-400 cursor-not-allowed': {{ $hasJob ? 'false' : 'true' }},
                'hover:text-[#BD9168]': {{ $hasJob ? 'true' : 'false' }}
            }"
            class="pb-2 focus:outline-none"
        >
            Training Schedule
        </button>
    </div>

    {{-- Job Postings Tab --}}
    <div 
        x-data="{ search: '' }" 
        x-show="$store.applications.tab === 'postings'" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="space-y-6"
    >
        {{-- Search + Sort --}}
<div class="flex items-center mb-6 gap-4">
    {{-- Search --}}
    <div class="flex items-center flex-1">
        <label for="search" class="mr-4 font-medium text-sm block">Search Position</label>
        <input 
            type="text"
            id="search"
            name="search"
            x-model="search"
            class="flex-1 border border-gray-300 rounded-md p-2 text-sm"
            placeholder="Enter job title..."
        >
    </div>
    {{-- Filters --}}
    <div x-data="{ open: false }" class="relative">
        <button type="button" 
            @click="open = !open" 
            class="border border-gray-300 rounded-md px-4 py-2 text-sm bg-white">
            Filters ▾
        </button>

        <div x-show="open" 
            @click.away="open = false"
            class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg p-4 z-50">

            <form method="GET" action="{{ route('hrAdmin.application') }}" class="space-y-3">
                
                {{-- Company --}}
                <div>
                    <p class="font-medium text-sm mb-1">Company</p>
                    @foreach($companies as $company)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="company_name[]" value="{{ $company }}"
                                {{ collect(request('company_name'))->contains($company) ? 'checked' : '' }}>
                            <span class="text-sm">{{ $company }}</span>
                        </label>
                    @endforeach
                </div>

                {{-- Sort --}}
                <div>
                    <p class="font-medium text-sm mb-1">Sort</p>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="sort" value="latest" {{ request('sort') === 'latest' ? 'checked' : '' }}>
                        <span class="text-sm">Latest</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="sort" value="oldest" {{ request('sort') === 'oldest' ? 'checked' : '' }}>
                        <span class="text-sm">Oldest</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="sort" value="position_asc" {{ request('sort') === 'position_asc' ? 'checked' : '' }}>
                        <span class="text-sm">Position (A–Z)</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="sort" value="position_desc" {{ request('sort') === 'position_desc' ? 'checked' : '' }}>
                        <span class="text-sm">Position (Z–A)</span>
                    </label>
                </div>

                <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-1 rounded-md text-sm">
                    Apply
                </button>
            </form>
        </div>
    </div>
</div>


        {{-- Job Listings --}}
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
                <x-hrAdmin.applicationJobListing :job="$job" />
            </div>
        @empty
            <p class="text-center text-gray-500">No job applications available.</p>
        @endforelse
    </div>

    {{-- Applicants Tab --}}
    <div 
        x-show="$store.applications.tab === 'applicants'" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
    >
        @if(isset($selectedJob) && isset($applications))
        <div 
            x-data="{ showNotice: true }"
            x-show="showNotice"
            x-transition
            x-cloak
            class="mb-4 bg-blue-100 border border-blue-300 text-blue-800 px-4 py-3 rounded-lg flex justify-between items-center"
            x-if="showNotice"
        >
            <span>
                You are viewing applicants for: 
                <strong class="text-[#1E3A8A]">{{ $selectedJob->company_name }}</strong> 
                <span class="text-gray-500">—</span> 
                <strong class="text-[#BD6F22]">{{ $selectedJob->job_title }}</strong>
            </span>

            <button 
                @click="showNotice = false" 
                class="text-sm text-blue-600 hover:underline"
            >
                Dismiss
            </button>
        </div>


            @include('hrAdmin.applicants', [
                'applications' => $applications,
                'selectedJob' => $selectedJob,
            ])
        @else
            <p class="text-gray-500 text-center">No applicants selected yet.</p>
        @endif
    </div>
    

    {{-- Interview Schedule Tab --}}
    <div 
        x-show="$store.applications.tab === 'interview'" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="space-y-4"
    >
        @if(isset($approvedApplicants) && $approvedApplicants->count() > 0)
            @include('hrAdmin.interviewSchedule',['approvedApplicants' => $approvedApplicants])
        @else
            <p class="text-center text-gray-500">No applicants scheduled for interview.</p>
        @endif
    </div>

    {{-- Training Schedule Tab --}}
    <div 
        x-show="$store.applications.tab === 'training'" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="space-y-4"
    >
        @if(isset($interviewApplicants) && $interviewApplicants->isNotEmpty())
            @include('hrAdmin.trainingSchedule',['interviewApplicants' => $interviewApplicants])
        @else
            <p class="text-center text-gray-500">No approved applicants for training yet.</p>
        @endif
    </div>
</section>
@endsection

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
