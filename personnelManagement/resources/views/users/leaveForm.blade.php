@extends('layouts.employeeHome', ['title' => 'Leave Form'])

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8"
     x-data="leaveFormApp()"
     x-init="init()"
>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-[#BD6F22]">Leave Form</h1>
        <button @click="showSubmitModal = true"
                class="bg-[#BD6F22] hover:bg-[#a05d1a] text-white px-4 py-2 rounded-md flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Submit New Leave Request
        </button>
    </div>

    <!-- Tabs -->
    <div class="flex border-b mb-6">
        <template x-for="status in ['Pending', 'Approved', 'Declined']" :key="status">
            <button
                class="px-4 py-2 font-medium transition"
                :class="{
                    'border-b-2 border-[#BD6F22] text-[#BD6F22]': selectedStatus === status,
                    'text-gray-500': selectedStatus !== status
                }"
                @click="selectedStatus = status; selectedForm = null; showViewModal = false"
                x-text="status"
            ></button>
        </template>
    </div>

    <!-- Leave Forms List -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column - List of Leave Forms -->
        <div class="md:col-span-2 space-y-4 max-h-[600px] overflow-y-auto pr-2">
            <template x-if="filteredForms.length === 0">
                <div class="bg-white shadow-sm rounded-md p-8 border text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-lg">No <span x-text="selectedStatus.toLowerCase()"></span> leave requests</p>
                </div>
            </template>

            <template x-for="form in filteredForms" :key="form.id">
                <div
                    @click="selectForm(form)"
                    class="cursor-pointer bg-white shadow-sm rounded-md p-4 border relative hover:shadow-md transition"
                    :class="{ 'border-[#BD6F22]': selectedForm && selectedForm.id === form.id }"
                >
                    <div class="text-sm text-gray-500 absolute top-2 right-3">
                        Submitted: <span x-text="formatDate(form.created_at)"></span>
                    </div>
                    <div class="text-gray-800 font-medium text-xl" x-text="form.date_range"></div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="bg-[#BD6F22] text-white text-sm px-3 py-1 rounded" x-text="form.leave_type"></span>
                        <template x-if="form.status !== 'Pending'">
                            <span class="text-white text-sm px-3 py-1 rounded"
                                  :class="{
                                      'bg-green-600': form.status === 'Approved',
                                      'bg-red-600': form.status === 'Declined'
                                  }"
                                  x-text="form.status">
                            </span>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Right Panel - Form Details -->
        <div class="bg-white shadow rounded-md border p-6 min-h-[300px]"
             x-show="selectedForm" x-transition>
            <template x-if="selectedForm">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-[#BD6F22] font-semibold text-lg">Request Details</h3>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm text-gray-700 mb-1">Date Range:</label>
                        <input type="text" class="border px-2 py-1 rounded text-sm w-full" readonly x-bind:value="selectedForm.date_range">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm text-gray-700 mb-1">Leave Type:</label>
                        <input type="text" class="border px-2 py-1 rounded text-sm w-full" readonly x-bind:value="selectedForm.leave_type">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm text-gray-700 mb-1">About:</label>
                        <textarea class="w-full border rounded p-2 text-sm" rows="4" readonly x-text="selectedForm.about || 'N/A'"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm text-gray-700 mb-1">Status:</label>
                        <span class="inline-block text-white text-sm px-3 py-1 rounded"
                              :class="{
                                  'bg-yellow-500': selectedForm.status === 'Pending',
                                  'bg-green-600': selectedForm.status === 'Approved',
                                  'bg-red-600': selectedForm.status === 'Declined'
                              }"
                              x-text="selectedForm.status">
                        </span>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-700 mb-1">Attachment:</label>
                        <a class="text-blue-600 hover:underline text-sm"
                           :href="'/storage/' + selectedForm.file_path"
                           target="_blank">
                            View Attachment
                        </a>
                    </div>

                    <div class="flex justify-end gap-2" x-show="selectedForm.status === 'Pending'">
                        <form method="POST" :action="`/employee/leave-forms/${selectedForm.id}`"
                              @submit.prevent="deleteForm($event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">
                                Delete Request
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Submit Leave Form Modal -->
    <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center"
         x-show="showSubmitModal"
         x-transition
         x-cloak
         @click.self="closeSubmitModal">
        <div class="bg-white rounded-lg max-w-lg w-full p-6 shadow-xl">
            <div class="mb-4 text-[#BD6F22] font-semibold text-lg flex justify-between items-center">
                Submit Leave Request
                <button @click="closeSubmitModal" class="text-gray-500 hover:text-black text-xl">&times;</button>
            </div>

            <form method="POST" action="{{ route('employee.leaveForms.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type" required
                            class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                        <option value="">Select Leave Type</option>
                        <option value="Sick Leave">Sick Leave</option>
                        <option value="Vacation Leave">Vacation Leave</option>
                        <option value="Emergency Leave">Emergency Leave</option>
                        <option value="Maternity Leave">Maternity Leave</option>
                        <option value="Paternity Leave">Paternity Leave</option>
                        <option value="Special Leave">Special Leave</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range <span class="text-red-500">*</span></label>
                    <input type="text" name="date_range" x-ref="dateRange" required
                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#BD6F22]"
                           placeholder="Select date range">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">About (Optional)</label>
                    <textarea name="about" rows="3"
                              class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#BD6F22]"
                              placeholder="Provide additional details about your leave request"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Attachment <span class="text-red-500">*</span></label>
                    <input type="file" name="attachment" required accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                    <p class="text-xs text-gray-500 mt-1">Allowed formats: PDF, JPG, JPEG, PNG (Max: 2MB)</p>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeSubmitModal"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-[#BD6F22] hover:bg-[#a05d1a] text-white px-4 py-2 rounded text-sm">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alpine Logic -->
<script>
function leaveFormApp() {
    return {
        leaveForms: @json($leaveForms),
        selectedStatus: 'Pending',
        selectedForm: null,
        showSubmitModal: false,
        showViewModal: false,

        init() {
            // Initialize Litepicker for date range
            if (this.$refs.dateRange) {
                new Litepicker({
                    element: this.$refs.dateRange,
                    singleMode: false,
                    format: 'MM/DD/YYYY',
                    delimiter: ' - ',
                    minDate: new Date(),
                    numberOfMonths: 2,
                    numberOfColumns: 2,
                    autoApply: true,
                    allowRepick: true
                });
            }
        },

        get filteredForms() {
            return this.leaveForms.filter(form => form.status === this.selectedStatus);
        },

        selectForm(form) {
            this.selectedForm = form;
            this.showViewModal = false;
        },

        closeSubmitModal() {
            this.showSubmitModal = false;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        },

        deleteForm(event) {
            if (confirm('Are you sure you want to delete this leave request?')) {
                event.target.submit();
            }
        }
    };
}
</script>
@endsection
