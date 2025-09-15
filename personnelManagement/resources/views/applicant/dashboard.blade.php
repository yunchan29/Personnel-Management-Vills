@extends('layouts.applicantHome')

@section('content')
<section class="w-full">
    {{-- Show banner only if profile is incomplete --}}
<input type="hidden" id="isProfileIncomplete" value="{{ auth()->user()->is_profile_complete ? '0' : '1' }}">

    <div class="p-6 bg-white">
        <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Home</h1>

        <div class="flex gap-4 mb-6">
            <input type="text" placeholder="Search..." class="border px-4 py-2 rounded-lg w-full">
            <button class="bg-[#BD9168] text-white px-4 py-2 rounded-lg">Search</button>
        </div>

@php
    $selectedIndustry = request('industry') ?? auth()->user()->job_industry;
@endphp

@if (!is_null($industry))
    <div class="mb-6">
        <div class="inline-flex items-center bg-gray-200 text-gray-700 px-4 py-1 rounded-full text-sm font-medium shadow-sm">
            <a href="{{ route('applicant.dashboard', ['industry' => '']) }}" 
               class="mr-2 text-gray-600 hover:text-red-600 font-bold" 
               title="Clear filter">
                &times;
            </a>
            {{ $industry }}
        </div>
    </div>
@endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse($jobs as $job)
    <div class="p-4 rounded-lg shadow-md">
    <x-applicant.job-card 
        :jobId="$job->id"
        :title="$job->job_title"
        :company="$job->company_name"
        :location="$job->location"
        :qualifications="$job->qualifications"
        :addinfo="$job->additional_info"
        :lastPosted="$job->created_at->diffForHumans()"
        :deadline="\Carbon\Carbon::parse($job->apply_until)->format('F d, Y')"
        :hasResume="!is_null($resume) && !empty($resume->resume)"
        :hasApplied="in_array($job->id, $appliedJobIds)"
        :vacancies="$job->vacancies"
    />
    </div>
@empty
        <p class="text-gray-500">No job openings available at the moment.</p>
    @endforelse
</div>

    </div>
</section>

{{-- SweetAlert2 --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Check if profile is incomplete
    const isProfileIncomplete = document.getElementById('isProfileIncomplete').value === '1';
    if (isProfileIncomplete) {
        Swal.fire({
            title: 'Complete Your Profile',
            text: "Please complete your profile to apply for jobs.",
            icon: 'warning',
            confirmButtonColor: '#BD6F22',
            confirmButtonText: 'Go to Profile',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('applicant.profile') }}";
            }
        });
    }
    
    // SweetAlert2 Apply Functionality
    document.querySelectorAll('button.apply-btn').forEach(button => {
        button.addEventListener('click', function () {
            const jobId = this.dataset.jobId;
            const btn = this;

            Swal.fire({
                title: 'Confirm Application',
                text: "Are you sure you want to apply for this job?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, apply!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/applicant/apply/${jobId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json', // important for Laravel to always return JSON
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({})
                    })
                    .then(async response => {
                        let data = {};

                        try {
                            data = await response.clone().json(); // clone for safe fallback if non-JSON
                        } catch (e) {
                            // fallback if not JSON
                            data.message = 'Unexpected response. Please try again.';
                        }

                        if (response.ok) {
                            Swal.fire(
                                'Applied!',
                                data.message || 'You have successfully applied for this job.',
                                'success'
                            );
                            btn.disabled = true;
                            btn.textContent = 'Applied';
                            btn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            Swal.fire(
                                'Application Failed',
                                data.message || `Something went wrong (Error ${response.status})`,
                                'error'
                            );
                        }
                    })

                    .catch(error => {
                        Swal.fire('Error', error.message, 'error');
                    });
                }
            });
        });
    });

    // See More / See Less toggle
    document.querySelectorAll('.toggle-btn').forEach(button => {
        button.addEventListener('click', function () {
            const content = this.previousElementSibling;
            if (content.classList.contains('max-h-24')) {
                content.classList.remove('max-h-24', 'overflow-hidden');
                this.textContent = 'See Less';
            } else {
                content.classList.add('max-h-24', 'overflow-hidden');
                this.textContent = 'See More';
            }
        });
    });
});
</script>
@endsection
