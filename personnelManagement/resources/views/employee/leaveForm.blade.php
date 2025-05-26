@extends('layouts.employeeHome')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<section class="p-6" x-data="leaveForm" x-init="init()">
    <h2 class="text-xl font-semibold text-[#BD6F22] mb-2">Leave Form</h2>
    <hr class="border-t border-gray-300 mb-6">

    @if($leaveForms->isEmpty())
        <div class="flex flex-col items-center justify-center h-64">
            <p class="text-center text-gray-800 mb-4">No leave requests submitted yet.</p>
            <button 
                @click="open = true" 
                class="bg-[#BD6F22] text-white px-4 py-2 rounded hover:bg-[#a75d1c] transition duration-200"
            >
                Add Leave
            </button>
        </div>
    @else
        <div class="flex justify-end mb-4">
            <button 
                @click="open = true" 
                class="bg-[#BD6F22] text-white px-4 py-2 rounded hover:bg-[#a75d1c] transition duration-200"
            >
                Add Leave
            </button>
        </div>

        <div class="flex flex-col gap-4">
            @foreach($leaveForms as $form)
                <div class="bg-white border rounded-lg shadow px-6 py-4 w-full relative">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-[#BD6F22] font-semibold text-lg mb-1">{{ $form->leave_type }}</h3>
                            <p class="text-gray-800 text-md font-medium">{{ $form->date_range }}</p>
                            <p class="text-sm text-gray-700 mt-1">{{ $form->email }}</p>
                        </div>
                        <div class="text-right text-sm text-gray-500">
                            Submitted: {{ \Carbon\Carbon::parse($form->created_at)->format('F d, Y') }}
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <form action="{{ route('employee.leaveForms.destroy', $form->id) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm delete-btn">Delete</button>
                        </form>

                        <div class="flex items-center gap-4">
                            <a href="{{ asset('storage/' . $form->file_path) }}" target="_blank" class="text-blue-600 text-xl hover:text-blue-800" title="Open file in new tab">
                                <i class="fas fa-arrow-up-right-from-square"></i>
                            </a>
                            <span class="bg-red-500 text-white text-xs px-3 py-1 rounded-full">To Review</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Modal -->
    <div 
        x-show="open" 
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-2xl relative" @click.outside="open = false">
            <h3 class="text-lg font-semibold text-[#BD6F22] mb-4">Leave Request</h3>

            <form id="leaveForm" action="{{ route('employee.leaveForms.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Leave Type -->
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">
                            Leave Type <span class="text-red-500">*</span>
                        </label>
                        <select name="leave_type" required class="w-full border px-3 py-2 rounded">
                            <option value="">-- Select --</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Vacation Leave">Vacation Leave</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <!-- File Attachment -->
                    <div x-data="{ fileName: '', fileSize: '' }">
                        <label class="block font-medium text-gray-700 mb-1">
                            Attachments <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="file" 
                                name="attachment" 
                                id="attachment" 
                                accept="application/pdf"
                                class="hidden" 
                                required
                                @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        fileName = file.name;
                                        fileSize = (file.size / 1024).toFixed(2) + ' KB';
                                    } else {
                                        fileName = '';
                                        fileSize = '';
                                    }
                                "
                            >
                            <label for="attachment" class="flex items-center justify-between w-full border rounded px-3 py-2 cursor-pointer hover:bg-gray-50">
                                <div class="truncate">
                                    <span class="block text-gray-700" x-text="fileName || 'Choose PDF file…'"></span>
                                    <span class="text-xs text-gray-500" x-show="fileSize" x-text="fileSize"></span>
                                </div>
                                <span class="text-gray-500 ml-2"><i class="fas fa-paperclip"></i></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="mb-4">
                    <label class="block font-medium text-gray-700 mb-1">
                        Dates <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-ref="dateRange" name="date_range" placeholder="MM/DD/YYYY - MM/DD/YYYY" required class="w-full border px-3 py-2 rounded">
                </div>

                <!-- About -->
                <div class="mb-4">
                    <label class="block font-medium text-gray-700 mb-1">
                        About <span class="text-sm text-gray-500">(if others, please explain)</span>
                    </label>
                    <textarea name="about" rows="4" class="w-full border rounded px-3 py-2 resize-none"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#BD6F22] text-white px-6 py-2 rounded hover:bg-[#a75d1c] transition duration-200">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
<script>
    document.addEventListener("alpine:init", () => {
        Alpine.data('leaveForm', () => ({
            open: false,
            init() {
                new Litepicker({
                    element: this.$refs.dateRange,
                    singleMode: false,
                    format: 'MM/DD/YYYY',
                    numberOfMonths: 2,
                    numberOfColumns: 2
                });

                // Submit confirmation with file size validation
                const leaveForm = document.getElementById('leaveForm');
                leaveForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const fileInput = document.getElementById('attachment');
                    const file = fileInput.files[0];

                    if (file && file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'File too large',
                            text: 'The attachment must not exceed 2MB.',
                            confirmButtonColor: '#BD6F22'
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'question',
                        title: 'Submit Leave Request?',
                        text: 'Are you sure you want to submit this leave form?',
                        showCancelButton: true,
                        confirmButtonColor: '#BD6F22',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: 'Yes, Submit'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            leaveForm.submit();
                        }
                    });
                });
            }
        }));
    });

    // 🔥 Delete confirmation - moved outside Alpine
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                window.allowSubmit = false; // Prevent loading overlay
                Swal.fire({
                    icon: 'warning',
                    title: 'Delete this leave request?',
                    text: 'This action cannot be undone.',
                    showCancelButton: true,
                    confirmButtonColor: '#BD6F22',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Yes, Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
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
