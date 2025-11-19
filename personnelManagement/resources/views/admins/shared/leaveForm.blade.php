@extends(auth()->user()->role === 'hrAdmin' ? 'layouts.hrAdmin' : 'layouts.hrStaff')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8"
     x-data="draggableModal()"
     x-init="initDrag(); checkFlashMessage(); updateFilteredCount();
             $watch('filterCompany', () => updateFilteredCount());
             $watch('filterYear', () => updateFilteredCount());
             $watch('filterMonth', () => updateFilteredCount());
             $watch('selectedStatus', () => updateFilteredCount());"
>
    <h1 class="text-2xl font-semibold text-[#BD6F22] mb-6">Leave Form</h1>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 mb-6">
        <div class="flex items-center gap-3 mb-3">
            <svg class="w-5 h-5 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            <h2 class="text-lg font-semibold text-gray-800">Filter Leave Forms</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Company Filter -->
            <div>
                <label for="filter-company" class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                <select id="filter-company" x-model="filterCompany"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                    <option value="">All Companies</option>
                    @php
                        $companies = $leaveForms->pluck('user.applications.*.job.company_name')->flatten()->unique()->filter()->sort()->values();
                    @endphp
                    @foreach($companies as $company)
                        <option value="{{ $company }}">{{ $company }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year Filter -->
            <div>
                <label for="filter-year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select id="filter-year" x-model="filterYear"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                    <option value="">All Years</option>
                    @php
                        $years = $leaveForms->pluck('created_at')->map(fn($date) => \Carbon\Carbon::parse($date)->year)->unique()->sort()->reverse()->values();
                    @endphp
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Month Filter -->
            <div>
                <label for="filter-month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                <select id="filter-month" x-model="filterMonth"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                    <option value="">All Months</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>

            <!-- Clear Filter Button -->
            <div class="flex items-end">
                <button @click="clearFilters"
                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md transition-colors duration-200">
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Filter Status Indicator -->
        <div x-show="filterCompany || filterYear || filterMonth" x-transition
             class="mt-3 p-2 bg-amber-50 border border-amber-200 rounded text-sm text-amber-800">
            <span class="font-medium">Active Filters:</span>
            <span x-show="filterCompany" class="ml-2">Company: <strong x-text="filterCompany"></strong></span>
            <span x-show="filterYear" class="ml-2">Year: <strong x-text="filterYear"></strong></span>
            <span x-show="filterMonth" class="ml-2">Month: <strong x-text="getMonthName(filterMonth)"></strong></span>
            <span class="ml-2">•</span>
            <span class="ml-1">Showing <strong x-text="filteredFormsCount"></strong> leave form(s)</span>
        </div>
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
                @click="selectedStatus = status; selectedForm = null; showModal = false"
                x-text="status"
            ></button>
        </template>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="md:col-span-2 space-y-4 max-h-[600px] overflow-y-auto pr-2">
            @foreach ($leaveForms as $form)
                <div
                    x-show="selectedStatus === '{{ $form->status }}' && shouldShowForm(@js($form))"
                    @click="selectForm($event)"
                    data-form='@json($form)'
                    data-created-at="{{ $form->created_at }}"
                    class="cursor-pointer bg-white shadow-sm rounded-md p-4 border relative hover:shadow-md transition"
                >
                    <div class="text-sm text-gray-500 absolute top-2 right-3">
                        Submitted: {{ \Carbon\Carbon::parse($form->created_at)->format('M d, Y') }}
                    </div>
                    <div class="text-[#BD6F22] font-semibold text-lg">
                        {{ $form->user->first_name }} {{ $form->user->last_name }}
                    </div>
                    <div class="text-gray-800 font-medium text-xl">{{ $form->date_range }}</div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="bg-[#BD6F22] text-white text-sm px-3 py-1 rounded">{{ $form->leave_type }}</span>
                        @if ($form->status !== 'Pending')
                            <span class="text-white text-sm px-3 py-1 rounded
                                {{ $form->status === 'Approved' ? 'bg-green-600' : 'bg-red-600' }}">
                                {{ $form->status }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- No Results Message -->
            <div x-show="filteredFormsCount === 0" x-transition
                 class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-300">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-600 font-medium text-lg mb-1">No leave forms found</p>
                <p class="text-gray-500 text-sm">Try adjusting your filters or clear them to see all leave forms.</p>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="bg-white shadow rounded-md border p-6 min-h-[300px]"
             x-show="selectedForm && !showModal" x-transition>
            <template x-if="selectedForm">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="text-[#BD6F22] font-semibold text-lg mb-1"
                                x-text="selectedForm.user.first_name + ' ' + selectedForm.user.last_name">
                            </div>
                            <p class="text-sm text-gray-800">Position: <span x-text="selectedForm.user.position || 'N/A'"></span></p>
                            <p class="text-sm text-gray-800">Company: <span x-text="selectedForm.user.company || 'N/A'"></span></p>
                        </div>
                        <button @click="openModal" title="Open as Modal">
                            <svg class="w-5 h-5 text-gray-500 hover:text-[#BD6F22]" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h6v6m0-6L10 14m-7 7h6v-6"></path>
                            </svg>
                        </button>
                    </div>

                    <h3 class="text-[#BD6F22] font-semibold text-lg mb-3">Request Details</h3>

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

                    <div class="mb-4">
                        <label class="block text-sm text-gray-700 mb-1">Attachment:</label>
                        @if(auth()->user()->role === 'hrAdmin')
                            <button type="button"
                                class="text-blue-600 hover:underline text-sm"
                                @click="openAttachmentModal('/storage/' + selectedForm.file_path)">
                                View Attachment
                            </button>
                        @else
                            <a class="block text-blue-600 hover:underline text-sm"
                               :href="'/storage/' + selectedForm.file_path"
                               target="_blank">
                                View Attachment
                            </a>
                        @endif
                    </div>

                    <div class="flex justify-end gap-2" x-show="selectedForm.status === 'Pending'">
                        <form method="POST" :action="`{{ auth()->user()->role === 'hrAdmin' ? '/hrAdmin' : '/hrStaff' }}/leave-forms/${selectedForm.id}/approve`" @submit.prevent="confirmApprove($event, selectedForm.id)">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Approve</button>
                        </form>
                        <form method="POST" :action="`{{ auth()->user()->role === 'hrAdmin' ? '/hrAdmin' : '/hrStaff' }}/leave-forms/${selectedForm.id}/decline`" @submit.prevent="confirmDecline($event, selectedForm.id)">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">Decline</button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Leave Form Modal -->
    <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center"
         x-show="showModal"
         x-transition
         x-cloak
         @click.self="closeModal">
        <div class="bg-white rounded-lg max-w-lg w-full p-6 absolute shadow-xl"
             :style="`transform: translate(${x}px, ${y}px)`">
            <div class="cursor-move mb-4 text-[#BD6F22] font-semibold text-lg flex justify-between items-center"
                 @mousedown.prevent="startDrag">
                Leave Form Details
                <button @click="closeModal" class="text-gray-500 hover:text-black text-xl">&times;</button>
            </div>

            <template x-if="selectedForm">
                <div>
                    <div class="text-[#BD6F22] font-semibold text-lg mb-2"
                         x-text="selectedForm.user.first_name + ' ' + selectedForm.user.last_name"></div>
                    <p class="text-sm text-gray-800">Position: <span x-text="selectedForm.user.position || 'N/A'"></span></p>
                    <p class="text-sm text-gray-800 mb-4">Company: <span x-text="selectedForm.user.company || 'N/A'"></span></p>

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

                    <div class="mb-4">
                        <label class="block text-sm text-gray-700 mb-1">Attachment:</label>
                        @if(auth()->user()->role === 'hrAdmin')
                            <button class="text-blue-600 hover:underline text-sm" @click="openAttachmentModal('/storage/' + selectedForm.file_path)">
                                View Attachment
                            </button>
                        @else
                            <a class="block text-blue-600 hover:underline text-sm"
                               :href="'/storage/' + selectedForm.file_path"
                               target="_blank">
                                View Attachment
                            </a>
                        @endif
                    </div>

                    <div class="flex justify-end gap-2" x-show="selectedForm.status === 'Pending'">
                        <form method="POST" :action="`{{ auth()->user()->role === 'hrAdmin' ? '/hrAdmin' : '/hrStaff' }}/leave-forms/${selectedForm.id}/approve`" @submit.prevent="confirmApprove($event, selectedForm.id)">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Approve</button>
                        </form>
                        <form method="POST" :action="`{{ auth()->user()->role === 'hrAdmin' ? '/hrAdmin' : '/hrStaff' }}/leave-forms/${selectedForm.id}/decline`" @submit.prevent="confirmDecline($event, selectedForm.id)">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">Decline</button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    @if(auth()->user()->role === 'hrAdmin')
    <!-- Attachment Modal (HR Admin only) -->
    <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center"
         x-show="showAttachmentModal"
         x-transition
         x-cloak
         @click.self="closeAttachmentModal">
        <div class="bg-white rounded-lg max-w-3xl w-full p-4 absolute shadow-xl"
             :style="`transform: translate(${x}px, ${y}px)`">
            <div class="cursor-move mb-4 text-[#BD6F22] font-semibold text-lg flex justify-between items-center"
                 @mousedown.prevent="startDrag">
                Attachment Viewer
                <button @click="closeAttachmentModal" class="text-gray-500 hover:text-black text-xl">&times;</button>
            </div>
            <div class="border rounded overflow-hidden">
                <iframe
                    :src="attachmentUrl"
                    class="w-full h-[500px]"
                    frameborder="0">
                </iframe>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Alpine Logic -->
<script>
function draggableModal() {
    return {
        selectedStatus: 'Pending',
        selectedForm: null,
        showModal: false,
        showAttachmentModal: false,
        attachmentUrl: '',
        x: 0,
        y: 0,
        dragging: false,
        offsetX: 0,
        offsetY: 0,
        filterCompany: '',
        filterYear: '',
        filterMonth: '',
        filteredFormsCount: 0,

        selectForm(event) {
            const formData = event.currentTarget.dataset.form;
            if (formData) {
                try {
                    this.selectedForm = JSON.parse(formData);

                    // Compute position and company from user's applications
                    if (this.selectedForm.user && this.selectedForm.user.applications) {
                        // Get the most recent application with a job
                        const latestApp = this.selectedForm.user.applications
                            .filter(app => app.job)
                            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];

                        if (latestApp && latestApp.job) {
                            this.selectedForm.user.position = latestApp.job.job_title || 'N/A';
                            this.selectedForm.user.company = latestApp.job.company_name || 'N/A';
                        } else {
                            this.selectedForm.user.position = 'N/A';
                            this.selectedForm.user.company = 'N/A';
                        }
                    }

                    this.showModal = false;
                    this.showAttachmentModal = false;
                } catch (e) {
                    console.error('Invalid form data:', formData);
                }
            }
        },
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        openAttachmentModal(url) {
            this.attachmentUrl = url;
            this.showAttachmentModal = true;
        },
        closeAttachmentModal() {
            this.showAttachmentModal = false;
            this.attachmentUrl = '';
        },
        initDrag() {
            this.doDrag = this.doDrag.bind(this);
            this.stopDrag = this.stopDrag.bind(this);
        },
        checkFlashMessage() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#BD6F22',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#BD6F22'
                });
            @endif
        },
        startDrag(event) {
            this.dragging = true;
            this.offsetX = event.clientX - this.x;
            this.offsetY = event.clientY - this.y;
            document.addEventListener('mousemove', this.doDrag);
            document.addEventListener('mouseup', this.stopDrag);
        },
        doDrag(event) {
            if (!this.dragging) return;
            this.x = event.clientX - this.offsetX;
            this.y = event.clientY - this.offsetY;
        },
        stopDrag() {
            this.dragging = false;
            document.removeEventListener('mousemove', this.doDrag);
            document.removeEventListener('mouseup', this.stopDrag);
        },
        shouldShowForm(form) {
            // Extract company from user's applications
            let formCompany = 'N/A';
            if (form.user && form.user.applications) {
                const latestApp = form.user.applications
                    .filter(app => app.job)
                    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];

                if (latestApp && latestApp.job) {
                    formCompany = latestApp.job.company_name || 'N/A';
                }
            }

            // Check company filter
            if (this.filterCompany && formCompany !== this.filterCompany) {
                return false;
            }

            // Check year and month filter
            const createdAt = new Date(form.created_at);
            const formYear = createdAt.getFullYear();
            const formMonth = createdAt.getMonth() + 1; // JavaScript months are 0-indexed

            if (this.filterYear && formYear !== parseInt(this.filterYear)) {
                return false;
            }

            if (this.filterMonth && formMonth !== parseInt(this.filterMonth)) {
                return false;
            }

            return true;
        },
        clearFilters() {
            this.filterCompany = '';
            this.filterYear = '';
            this.filterMonth = '';
            this.updateFilteredCount();
        },
        getMonthName(monthNum) {
            const months = ['January', 'February', 'March', 'April', 'May', 'June',
                          'July', 'August', 'September', 'October', 'November', 'December'];
            return monthNum ? months[parseInt(monthNum) - 1] : '';
        },
        updateFilteredCount() {
            // Count visible forms
            const allForms = @json($leaveForms);
            this.filteredFormsCount = allForms.filter(form =>
                form.status === this.selectedStatus && this.shouldShowForm(form)
            ).length;
        },
        async confirmApprove(event, formId) {
            const result = await Swal.fire({
                title: 'Approve Leave Request?',
                text: 'Are you sure you want to approve this leave request?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we approve the leave request.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                event.target.submit();
            }
        },
        async confirmDecline(event, formId) {
            const result = await Swal.fire({
                title: 'Decline Leave Request?',
                text: 'Are you sure you want to decline this leave request?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Decline',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we decline the leave request.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                event.target.submit();
            }
        }
    };
}
</script>
@endsection
