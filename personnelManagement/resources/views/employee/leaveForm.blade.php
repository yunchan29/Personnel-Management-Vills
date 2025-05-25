@extends('layouts.employeeHome')

@section('content')
<!-- Litepicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css">

<section class="p-6" x-data="leaveForm" x-init="init()">
    <h2 class="text-xl font-semibold text-[#BD6F22] mb-2">Leave Form</h2>
    <hr class="border-t border-gray-300 mb-6">

    <!-- Display Existing Leave Requests -->
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

        <div class="overflow-x-auto">
            <table class="w-full border rounded-lg text-sm">
                <thead class="bg-[#BD6F22] text-white">
                    <tr>
                        <th class="p-2 text-left">Type</th>
                        <th class="p-2 text-left">Date Range</th>
                        <th class="p-2 text-left">About</th>
                        <th class="p-2 text-left">File</th>
                        <th class="p-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveForms as $form)
                    <tr class="border-b">
                        <td class="p-2">{{ $form->leave_type }}</td>
                        <td class="p-2">{{ $form->date_range }}</td>
                        <td class="p-2">{{ $form->about ?? '—' }}</td>
                        <td class="p-2">
                            <a href="{{ asset('storage/' . $form->file_path) }}" target="_blank" class="text-blue-600 underline">
                                View File
                            </a>
                        </td>
                        <td class="p-2">
                            <form action="{{ route('employee.leaveForms.destroy', $form->id) }}" method="POST" onsubmit="return confirm('Delete this leave request?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Modal -->
    <div 
        x-show="open" 
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-2xl relative" @click.outside="open = false">
            <h3 class="text-lg font-semibold text-[#BD6F22] mb-4">Leave Request</h3>

            <form action="{{ route('employee.leaveForms.store') }}" method="POST" enctype="multipart/form-data">
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
                                <span class="text-gray-500 ml-2">📎</span>
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

<!-- Litepicker JS -->
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
            }
        }));
    });
</script>
@endsection
