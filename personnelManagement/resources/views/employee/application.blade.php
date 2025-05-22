@extends('layouts.employeeHome')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <!-- Section Title -->
    <h2 class="text-xl font-semibold mb-4" style="color: #BD6F22;">My Applications</h2>

    <!-- Resume Upload Notice -->
    <div class="border border-gray-300 rounded-md shadow-sm p-4 mb-6">
        <div class="flex items-start gap-2 mb-4">
            <span class="text-xl">⚠️</span>
            <div class="text-sm text-gray-800" style="color: #BD6F22;">
                <ul class="list-disc pl-4 space-y-1">
                    <li>Make sure your resume is in PDF format only.</li>
                    <li>Update your 201 files to boost your chances of getting hired. (ex. Certifications, etc.)</li>
                </ul>
            </div>
        </div>

        <form
            action="{{ route('employee.application.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="flex flex-col md:flex-row items-center gap-4"
        >
            @csrf

            <label class="w-full md:flex-1">
                <span class="block mb-1 text-sm text-gray-700">
                    {{ $resume ? 'Replace your resume (PDF only)' : 'Please upload your resume (PDF only)' }}
                </span>
                <input
                    type="file"
                    name="resume_file"
                    accept=".pdf"
                    required
                    class="w-full border border-gray-300 rounded px-3 py-2"
                >
                @error('resume_file')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </label>

            <button
                type="submit"
                class="px-6 py-2 text-white rounded"
                style="background-color: #BD6F22;"
            >
                {{ $resume ? 'Replace' : 'Upload' }}
            </button>
        </form>
    </div>

    @if($resume)
        <div class="mb-6 flex items-center gap-4">
            <!-- Show Resume Button -->
            <a
                href="{{ asset('storage/' . $resume->resume) }}"
                target="_blank"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
            >
                Show Resume
            </a>

            <!-- Delete Resume Button -->
            <form
                id="deleteForm"
                action="{{ route('employee.application.destroy') }}"
                method="POST"
            >
                @csrf
                @method('DELETE')
                <button
                    type="button"
                    id="deleteResumeBtn"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition"
                >
                    Delete Resume
                </button>
            </form>
        </div>
    @endif

    <!-- Application Card (placeholder) -->
    <div class="border border-gray-300 rounded-md shadow-md p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-md font-semibold" style="color: #BD6F22;">Production Operator</h3>
            <p class="text-sm text-gray-700 mb-2">Yazaki - Torres Manufacturing, Inc.</p>
            <a href="#" class="inline-block bg-[#BD6F22] text-white text-sm px-4 py-2 rounded hover:bg-[#a75e1c] transition">View Resume</a>
            <p class="text-xs text-gray-500 mt-2">Applied on: April 20, 2025</p>
        </div>
        <div>
            <span class="inline-block bg-[#DD6161] text-white text-sm px-4 py-2 rounded">To Review</span>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Delete Resume with SweetAlert -->
<script>
document.getElementById('deleteResumeBtn')?.addEventListener('click', function (e) {
    e.preventDefault();

    Swal.fire({
        title: 'Are you sure?',
        text: "Delete your existing resume?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
        // No else needed – no action = no stuck loading screen
    });
});
</script>

<!-- Show SweetAlert after redirect (optional) -->
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        timer: 2500,
        showConfirmButton: false
    });
</script>
@endif

@endsection
