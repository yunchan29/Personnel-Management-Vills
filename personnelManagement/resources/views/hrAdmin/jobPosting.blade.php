@extends('layouts.hrAdmin')

@section('content')
<section class="p-6 max-w-5xl mx-auto">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Job Posting</h1>
    <hr class="border-t border-gray-300 mb-6">

 <!-- Trigger Button -->
<div class="flex justify-center my-10">
    <button 
        x-data 
        @click="$dispatch('open-job-modal')"
        class="bg-[#BD6F22] text-white px-6 py-2 rounded-md hover:bg-[#a65e1d] transition"
    >
        Add Job Advertisement
    </button>
</div>

<!-- Modal Component -->
<x-hrAdmin.jobFormModal />

  <!--Job Listing Display-->
  <x-hrAdmin.jobListingDisplay :jobs="$jobs" />

</section>
@endsection

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
