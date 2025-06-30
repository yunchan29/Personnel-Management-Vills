@extends('layouts.hrAdmin')

@section('content')
<section class="p-6 max-w-5xl mx-auto">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Job Posting</h1>
    <hr class="border-t border-gray-300 mb-6">

    @if($jobs->isNotEmpty())
        <!-- Search and Add Button Row -->
        <div class="flex flex-col md:flex-row md:justify-between gap-4 mb-6">
            <!-- Sort Icon + Search Form -->
            <form method="GET" action="{{ route('hrAdmin.jobPosting') }}" class="flex w-full md:flex-1 items-center gap-2" x-data="{ open: false }">
                
                <!-- Sort Icon + Dropdown -->
                <div class="relative">
                    <button 
                        type="button"
                        @click="open = !open"
                        class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-[#BD6F22] hover:text-[#a65e1d] transition"
                        title="Sort Options"
                    >
                        <!-- Sort Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M3 10h14M3 16h10" />
                        </svg>
                    </button>

                    <!-- Sort Dropdown -->
                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        x-transition
                        class="absolute z-10 mt-2 w-40 bg-white border border-gray-200 rounded-md shadow-md"
                    >
                        @php
                            $sortOptions = [
                                'latest' => 'Latest',
                                'oldest' => 'Oldest',
                                'position_asc' => 'Position A-Z',
                                'position_desc' => 'Position Z-A',
                            ];
                        @endphp

                        @foreach($sortOptions as $value => $label)
                            <button 
                                type="submit"
                                name="sort"
                                value="{{ $value }}"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ request('sort') === $value ? 'bg-gray-100 font-semibold' : '' }}"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Search Bar + Button (No Gap) -->
                <div class="flex w-full">
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search Position" 
                        class="w-full border border-gray-300 rounded-l-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]"
                    >

                    <button 
                        type="submit"
                        class="bg-[#BD6F22] text-white px-4 py-2 rounded-r-md hover:bg-[#a65e1d] transition"
                    >
                        Search
                    </button>
                </div>
            </form>

            <!-- Add Job Advertisement Button -->
            <div class="flex-shrink-0">
                <button 
                    x-data 
                    @click="$dispatch('open-job-modal')"
                    class="bg-[#BD6F22] text-white px-6 py-2 rounded-md hover:bg-[#a65e1d] transition w-full md:w-auto"
                >
                    Add Job Advertisement
                </button>
            </div>
        </div>
    @else
        <!-- Centered Add Button (No Jobs) -->
        <div class="flex justify-center flex gap-2 my-10">
            @if(request()->filled('search') && $jobs->isEmpty())
                <a href="{{ route('hrAdmin.jobPosting') }}"
                    class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-300 transition"
                >
                    Go Back
                </a>
            @endif

            <button 
                x-data 
                @click="$dispatch('open-job-modal')"
                class="bg-[#BD6F22] text-white px-6 py-2 rounded-md hover:bg-[#a65e1d] transition"
            >
                Add Job Advertisement
            </button>
        </div>
    @endif

        <!-- Search Result Message -->
        @if(request()->filled('search'))
            <div class="mb-4 text-sm text-gray-600 italic text-center">
                @if($jobs->count() > 0)
                    {{ $jobs->count() }} result{{ $jobs->count() > 1 ? 's' : '' }} found for “{{ request('search') }}”.
                @else
                    No results found for “{{ request('search') }}”.
                @endif
            </div>
        @endif

    <!-- Modal Component -->
    <x-hrAdmin.jobFormModal />

    <!-- Job Listing -->
    @if($jobs->isNotEmpty())
        <x-hrAdmin.jobListingDisplay :jobs="$jobs" />
    @endif
</section>
@endsection

<!-- SweetAlert for success message -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#BD6F22'
        });
    });
</script>
@endif

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
