@extends('layouts.hrAdmin')

@section('content')
<section
    class="p-6 w-full max-w-full mx-auto"
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
        x-show="$store.applications.tab === 'postings'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="space-y-6"
    >
        {{-- Search + Filters --}}
        <div class="mb-6 bg-white border border-gray-200 rounded-lg shadow-sm p-4" x-data="{ showFilters: {{ request('company_name') || request('sort') ? 'true' : 'false' }} }">
            <form method="GET" action="{{ route('hrAdmin.application') }}" class="space-y-4">

                {{-- Search Bar with Filter Toggle --}}
                <div class="flex items-center gap-2 border border-gray-300 rounded-md px-3 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="flex-1 border-0 focus:ring-0 text-sm p-0"
                        placeholder="Search by job title, company, location, or qualifications..."
                    >

                    {{-- Filter Toggle Button --}}
                    <button
                        type="button"
                        @click="showFilters = !showFilters"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm font-medium transition whitespace-nowrap"
                        :class="showFilters ? 'bg-[#BD6F22] text-white' : 'text-gray-500 hover:text-[#BD6F22] hover:bg-gray-50'"
                        title="Toggle filters"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filters</span>
                    </button>
                </div>

                {{-- Filter Row (Collapsible) --}}
                <div
                    x-show="showFilters"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-gray-200"
                >

                    {{-- Company Dropdown --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Company</label>
                        <select
                            name="company_name"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]"
                        >
                            <option value="">All Companies</option>
                            @foreach($companies as $company)
                                <option value="{{ $company }}" {{ request('company_name') === $company ? 'selected' : '' }}>
                                    {{ $company }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort By --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                        <select
                            name="sort"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]"
                        >
                            <option value="latest" {{ request('sort') === 'latest' || !request('sort') ? 'selected' : '' }}>Latest First</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="position_asc" {{ request('sort') === 'position_asc' ? 'selected' : '' }}>Position (A-Z)</option>
                            <option value="position_desc" {{ request('sort') === 'position_desc' ? 'selected' : '' }}>Position (Z-A)</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="flex-1 bg-[#BD6F22] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#a75e1c] transition"
                        >
                            Apply Filters
                        </button>
                        <a
                            href="{{ route('hrAdmin.application') }}"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition"
                        >
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>


        {{-- Job Listings --}}
        @forelse($jobs as $job)
            <x-hrAdmin.applicationJobListing :job="$job" />
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


            @include('admins.hrAdmin.applicants', [
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
            @include('admins.hrAdmin.interviewSchedule',['applications' => $approvedApplicants])
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
            @include('admins.hrAdmin.trainingSchedule',['applications' => $interviewApplicants])
        @else
            <p class="text-center text-gray-500">No approved applicants for training yet.</p>
        @endif
    </div>
</section>
@endsection

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
