@extends('layouts.hrAdmin')

@section('content')
<section class="p-6 max-w-6xl mx-auto">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Applications</h1>
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

  <!--Application Job Listing-->
  {{-- Job Listings --}}
<div class="space-y-6">
    @forelse($jobs as $job)
        <x-hrAdmin.applicationJobListing :job="$job" />
    @empty
        <p class="text-center text-gray-500">No job applications available.</p>
    @endforelse
</div>

</section>
@endsection
