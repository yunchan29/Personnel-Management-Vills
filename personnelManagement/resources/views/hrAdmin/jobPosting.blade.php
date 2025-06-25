@extends('layouts.hrAdmin')

@section('content')
<section class="p-6 max-w-5xl mx-auto">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Job Posting</h1>
    <hr class="border-t border-gray-300 mb-6">

    @if($jobs->isNotEmpty())
        <!-- Search and Add Button Row -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <!-- Search Bar with Button -->
            <div class="flex w-full md:flex-1">
                <input 
                    type="text" 
                    placeholder="Search Position" 
                    class="w-full border border-gray-300 rounded-l-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]"
                >
                <button 
                    class="bg-[#BD6F22] text-white px-4 py-2 rounded-r-md hover:bg-[#a65e1d] transition"
                >
                    Search
                </button>
            </div>

            <!-- Add Job Button -->
            <div class="flex-shrink-0">
                <button 
                    x-data 
                    @click="$dispatch('open-job-modal')"
                    class="bg-[#BD6F22] text-white px-6 py-2 rounded-md hover:bg-[#a65e1d] transition"
                >
                    Add Job Advertisement
                </button>
            </div>
        </div>
    @else
        <!-- Centered Add Button (No Jobs) -->
        <div class="flex justify-center my-10">
            <button 
                x-data 
                @click="$dispatch('open-job-modal')"
                class="bg-[#BD6F22] text-white px-6 py-2 rounded-md hover:bg-[#a65e1d] transition"
            >
                Add Job Advertisement
            </button>
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
