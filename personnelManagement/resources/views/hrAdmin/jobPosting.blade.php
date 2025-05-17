@extends('layouts.hrAdmin')

@section('content')
<section class="p-6 max-w-5xl mx-auto">
    <h2 class="text-xl font-semibold text-[#BD6F22] mb-2">Job Posting</h2>
    <hr class="border-t border-gray-300 mb-6">
    <div class="border rounded-md shadow-md p-6 bg-white">
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
