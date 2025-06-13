@extends('layouts.hrAdmin')

@section('content')
<section class="p-6 max-w-5xl mx-auto">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Job Posting</h1>
    <hr class="border-t border-gray-300 mb-6">

    {{-- Add Job Advertisement Button --}}
    <div class="flex justify-center my-10">
        <button 
            onclick="document.getElementById('jobModal').classList.remove('hidden')" 
            class="bg-[#BD6F22] text-white px-6 py-2 rounded-md hover:bg-[#a65e1d] transition"
        >
            Add Job Advertisement
        </button>
    </div>

    {{-- Modal for Add Job Form --}}
    <div id="jobModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
        <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg p-6 relative">
            <button 
                onclick="document.getElementById('jobModal').classList.add('hidden')" 
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800"
            >
                ✕
            </button>

            <h3 class="text-lg font-semibold text-[#BD6F22] mb-4">Add Job</h3>
            <form action="{{ route('hrAdmin.jobPosting.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="job_title" class="block font-medium mb-1">Job Title</label>
                        <input type="text" name="job_title" id="job_title" class="w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div>
                        <label for="company_name" class="block font-medium mb-1">Company Name</label>
                        <input type="text" name="company_name" id="company_name" class="w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div>
                        <label for="location" class="block font-medium mb-1">Location</label>
                        <input type="text" name="location" id="location" class="w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label for="vacancies" class="block font-medium mb-1">Number of Vacancies</label>
                            <input type="number" name="vacancies" id="vacancies" class="w-full border border-gray-300 rounded-md p-2">
                        </div>
                        <div class="flex-1">
                            <label for="apply_until" class="block font-medium mb-1">Apply until</label>
                            <input type="date" name="apply_until" id="apply_until" class="w-full border border-gray-300 rounded-md p-2">
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="qualifications" class="block font-medium mb-1">Qualifications</label>
                        <textarea name="qualifications" id="qualifications" rows="5" class="w-full border border-gray-300 rounded-md p-2"></textarea>
                    </div>
                    <div>
                        <label for="additional_info" class="block font-medium mb-1">Additional Information</label>
                        <textarea name="additional_info" id="additional_info" rows="5" class="w-full border border-gray-300 rounded-md p-2"></textarea>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="bg-[#BD6F22] text-white px-6 py-2 rounded-md hover:bg-[#a65e1d] transition">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Job Listing Display --}}
<div class="mt-10 space-y-6">
    @forelse($jobs as $job)
        <div class="bg-white border rounded-md shadow-sm p-6 relative">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h4 class="text-lg font-semibold text-[#BD6F22]">{{ $job->job_title }}</h4>
                    <p class="text-gray-700">{{ $job->company_name }}</p>
                </div>
                <a href="{{ route('hrAdmin.jobPosting.edit', $job->id) }}" class="text-gray-600 hover:text-[#BD6F22]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5h2m2 14H9m2-10h.01M5 13l4 4L19 7" />
                    </svg>
                </a>
            </div>

            <div class="flex items-start text-sm mb-2 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-1 text-[#BD6F22]" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2a4 4 0 00-4-4H4a4 4 0 100 8h1a4 4 0 004-4zm6 0v-2a4 4 0 014-4h1a4 4 0 110 8h-1a4 4 0 01-4-4z" />
                </svg>
                <div>
                    <strong>Qualification:</strong>
                    <ul class="list-disc ml-6">
                      @foreach($job->qualifications as $line)
    <li>{{ $line }}</li>
@endforeach

                    </ul>
                </div>
            </div>

            <div class="flex items-center text-sm text-gray-600 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#BD6F22]" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 12l4.243-4.243M6.343 7.343L10.586 12l-4.243 4.243" />
                </svg>
                {{ $job->location }}
            </div>

            <div class="flex justify-between items-center text-sm text-gray-500">
                <p>Last Posted: {{ $job->created_at->diffForHumans() }}</p>
                <p>Apply until: {{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</p>
            </div>

            <div class="text-right mt-2">
                <a href="{{ route('hrAdmin.jobPosting.show', $job->id) }}" class="text-sm text-[#BD6F22] hover:underline">
                    See More
                </a>
            </div>
        </div>
    @empty
        <p class="text-center text-gray-500">No job postings available.</p>
    @endforelse
</div>

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
