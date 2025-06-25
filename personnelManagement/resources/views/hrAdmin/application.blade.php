@extends('layouts.hrAdmin')

@section('content')
<section class="p-6 max-w-6xl mx-auto" x-data="{ tab: 'postings' }">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Applications</h1>
    <hr class="border-t border-gray-300 mb-6">

    {{-- Tab Navigation --}}
    <div class="flex space-x-8 text-sm font-medium text-gray-600 border-b border-gray-300 mb-6">
        <button 
            @click="tab = 'postings'" 
            :class="tab === 'postings' ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' : 'hover:text-[#BD9168]'"
            class="pb-2 focus:outline-none"
        >
            Job Postings
        </button>
        <button 
            @click="tab = 'applicants'" 
            :class="tab === 'applicants' ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' : 'hover:text-[#BD9168]'"
            class="pb-2 focus:outline-none"
        >
            Applicants
        </button>
        <button 
            @click="tab = 'interview'" 
            :class="tab === 'interview' ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' : 'hover:text-[#BD9168]'"
            class="pb-2 focus:outline-none"
        >
            Interview Schedule
        </button>
        <button 
            @click="tab = 'training'" 
            :class="tab === 'training' ? 'text-[#BD9168] border-b-2 border-[#BD9168] pb-2' : 'hover:text-[#BD9168]'"
            class="pb-2 focus:outline-none"
        >
            Training Schedule
        </button>
    </div>

    {{-- Tab Panels --}}
    <!-- Job Postings Tab -->
    <div 
        x-show="tab === 'postings'" 
        x-transition:enter="transition ease-out duration-300" 
        x-transition:enter-start="opacity-0 translate-y-2" 
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="space-y-6"
    >
        <!-- Search -->
        <div class="flex items-center mb-6">
            <div class="mr-4">
                <label for="search" class="font-medium text-sm block">Search Position</label>
            </div>
            <input 
                type="text" 
                id="search" 
                name="search" 
                class="flex-1 border border-gray-300 rounded-md p-2 text-sm"
                placeholder="Enter job title..."
            >
            <button class="ml-4 bg-[#BD9168] text-white px-6 py-2 rounded-md hover:bg-[#a37654] transition text-sm flex items-center gap-2">
                <img src="{{ asset('images/Search2.png') }}" alt="Search" class="h-4 w-4">
                <span>Search</span>
            </button>
        </div>

        {{-- Job Listings --}}
        @forelse($jobs as $job)
            <x-hrAdmin.applicationJobListing :job="$job" />
        @empty
            <p class="text-center text-gray-500">No job applications available.</p>
        @endforelse
    </div>

    <!-- Applicants Tab -->
    <div 
        x-show="tab === 'applicants'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="space-y-4"
    >
        {{-- Example applicants panel --}}
        @include('hrAdmin.applicants') {{-- Replace with actual view or markup --}}
    </div>

    <!-- Interview Schedule Tab -->
    <div 
        x-show="tab === 'interview'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="space-y-4"
    >
        <p class="text-gray-500">Interview schedule content goes here.</p>
    </div>

    <!-- Training Schedule Tab -->
    <div 
        x-show="tab === 'training'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="space-y-4"
    >
        <p class="text-gray-500">Training schedule content goes here.</p>
    </div>
</section>
@endsection

{{-- AlpineJS --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
