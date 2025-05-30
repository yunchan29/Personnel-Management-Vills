@extends('layouts.hrAdmin')

@section('content')
<section class="p-6 max-w-6xl mx-auto">
    <h2 class="text-xl font-semibold text-[#BD9168] mb-2">Applications</h2>
    <hr class="border-t border-gray-300 mb-6">

    {{-- Tab Navigation --}}
    <div class="flex space-x-8 text-sm font-medium text-gray-600 border-b border-gray-300 mb-4">
        <a href="#" class="text-[#BD9168] border-b-2 border-[#BD9168] pb-2">Job Postings</a>
        <a href="#" class="hover:text-[#BD9168]">Applicants</a>
        <a href="#" class="hover:text-[#BD9168]">Interview Schedule</a>
        <a href="#" class="hover:text-[#BD9168]">Training Schedule</a>
    </div>

    {{-- Search Input --}}
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
    <div class="space-y-6">
        @forelse($jobs as $job)
            <div class="bg-white border rounded-md shadow-sm p-4 relative">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="text-base font-semibold text-[#BD9168]">{{ $job->job_title }}</h4>
                        <p class="text-sm text-gray-800">{{ $job->company_name }}</p>
                    </div>
                    <div class="text-right text-xs text-gray-500 leading-tight">
                        <p>Last Posted: {{ $job->created_at->diffForHumans() }}</p>
                        <p>Apply until: {{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</p>
                    </div>
                </div>

                <div class="flex text-sm text-gray-600 mb-2">
                    <div class="mr-2 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#BD9168]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 17v-2a4 4 0 00-4-4H4a4 4 0 100 8h1a4 4 0 004-4zm6 0v-2a4 4 0 014-4h1a4 4 0 110 8h-1a4 4 0 01-4-4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium">Qualification :</p>
                        <ul class="list-disc ml-5">
                            @foreach($job->qualifications as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="flex text-sm text-gray-600 mb-4 items-center">
                    <div class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#BD9168]" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 12l4.243-4.243M6.343 7.343L10.586 12l-4.243 4.243"/>
                        </svg>
                    </div>
                    <span>{{ $job->location }}</span>
                </div>

                <div class="flex justify-between items-center text-sm">
                    <a href="{{ route('hrAdmin.jobPosting.show', $job->id) }}" class="text-[#BD9168] hover:underline">
                        See More
                    </a>
                    <a href="{{ route('hrAdmin.viewApplicants', $job->id) }}" class="text-[#BD9168] hover:underline flex items-center gap-1">
                        View Applicants
                        <span class="bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                            {{ $job->applicants_count }}
                        </span>
                    </a>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500">No job applications available.</p>
        @endforelse
    </div>
</section>
@endsection
