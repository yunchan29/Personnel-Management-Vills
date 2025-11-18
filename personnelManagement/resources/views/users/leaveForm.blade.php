@extends('layouts.employeeHome', ['title' => 'Leave Form'])

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8"
     x-data="leaveFormApp()"
     x-init="init()"
>
    <!-- Success/Error Toast Notification -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 max-w-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-full"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-x-full">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>{{ session('success') }}</span>
        <button @click="show = false" class="ml-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 max-w-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-full"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-x-full">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>{{ session('error') }}</span>
        <button @click="show = false" class="ml-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
         class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg max-w-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-full"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-x-full">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-semibold mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false" class="ml-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    @endif

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

            <form method="POST" action="{{ route('employee.leaveForms.store') }}" enctype="multipart/form-data"
                  @submit.prevent="submitForm($event)">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type" required :disabled="isSubmitting"
                            class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#BD6F22] disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <option value="">Select Leave Type</option>
                        <option value="Sick Leave" {{ old('leave_type') == 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
                        <option value="Vacation Leave" {{ old('leave_type') == 'Vacation Leave' ? 'selected' : '' }}>Vacation Leave</option>
                        <option value="Emergency Leave" {{ old('leave_type') == 'Emergency Leave' ? 'selected' : '' }}>Emergency Leave</option>
                        <option value="Maternity Leave" {{ old('leave_type') == 'Maternity Leave' ? 'selected' : '' }}>Maternity Leave</option>
                        <option value="Paternity Leave" {{ old('leave_type') == 'Paternity Leave' ? 'selected' : '' }}>Paternity Leave</option>
                        <option value="Special Leave" {{ old('leave_type') == 'Special Leave' ? 'selected' : '' }}>Special Leave</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range <span class="text-red-500">*</span></label>
                    <input type="text" name="date_range" x-ref="dateRange" required :disabled="isSubmitting"
                           value="{{ old('date_range') }}"
                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#BD6F22] disabled:bg-gray-100 disabled:cursor-not-allowed"
                           placeholder="Select date range">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">About (Optional)</label>
                    <textarea name="about" rows="3" :disabled="isSubmitting"
                              class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#BD6F22] disabled:bg-gray-100 disabled:cursor-not-allowed"
                              placeholder="Provide additional details about your leave request">{{ old('about') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Attachment <span class="text-red-500">*</span></label>
                    <input type="file" name="attachment" required accept=".pdf,.jpg,.jpeg,.png" :disabled="isSubmitting"
                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#BD6F22] disabled:bg-gray-100 disabled:cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Allowed formats: PDF, JPG, JPEG, PNG (Max: 2MB)</p>
                    @if($errors->any())
                        <p class="text-xs text-orange-600 mt-1 font-medium">Note: Please re-select your file after fixing errors.</p>
                    @endif
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeSubmitModal" :disabled="isSubmitting"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Cancel
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="bg-[#BD6F22] hover:bg-[#a05d1a] text-white px-4 py-2 rounded text-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="isSubmitting" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Submitting...' : 'Submit Request'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center"
         x-show="showSuccessModal"
         x-transition
         x-cloak>
        <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
            <div class="text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <!-- Success Message -->
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Leave Request Submitted!</h3>
                <p class="text-gray-600 mb-6">Your leave request has been successfully submitted and is pending approval.</p>

                <!-- OK Button -->
                <button @click="closeSuccessModal"
                        class="w-full bg-[#BD6F22] hover:bg-[#a05d1a] text-white px-6 py-3 rounded-md font-medium transition">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alpine Logic -->
<script>
function leaveFormApp() {
    return {
        leaveForms: @json($leaveForms->items()),
        selectedStatus: 'Pending',
        selectedForm: null,
        showSubmitModal: false,
        showViewModal: false,
        showSuccessModal: false,
        isSubmitting: false,
        litepickerInstance: null,

        init() {
            // Keep modal open if there are validation errors
            @if($errors->any())
                this.showSubmitModal = true;
                this.isSubmitting = false;
            @endif

            // Watch for modal opening to initialize date picker
            this.$watch('showSubmitModal', (value) => {
                if (value) {
                    this.$nextTick(() => {
                        this.initializeDatePicker();
                    });
                } else {
                    // Destroy picker when modal closes
                    if (this.litepickerInstance) {
                        this.litepickerInstance.destroy();
                        this.litepickerInstance = null;
                    }
                }
            });
        },

        initializeDatePicker() {
            // Destroy existing instance if any
            if (this.litepickerInstance) {
                this.litepickerInstance.destroy();
            }

            // Initialize Litepicker for date range
            if (this.$refs.dateRange) {
                this.litepickerInstance = new Litepicker({
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
            this.isSubmitting = false;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        },

        deleteForm(event) {
            if (confirm('Are you sure you want to delete this leave request?')) {
                event.target.submit();
            }
        },

        submitForm(event) {
            // Show loading spinner
            this.isSubmitting = true;

            const form = event.target;
            const formData = new FormData(form);

            // Submit form via AJAX to ensure data is sent before reload
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text().then(text => ({ status: response.status, body: text })))
            .then(({ status, body }) => {
                // Check if response contains validation errors
                const hasErrors = body.includes('Please fix the following errors:') ||
                                 body.includes('bg-red-500') ||
                                 status === 422;

                if (hasErrors) {
                    // Validation errors - reload page to show them
                    window.location.reload();
                } else {
                    // Success - show success modal
                    this.isSubmitting = false;
                    this.showSubmitModal = false;
                    this.showSuccessModal = true;
                }
            })
            .catch(error => {
                console.error('Submission error:', error);
                // Reload on error to show any messages
                window.location.reload();
            });
        },

        closeSuccessModal() {
            this.showSuccessModal = false;
            // Reload page to show the new leave request
            window.location.reload();
        }
    };
}
</script>
@endsection
