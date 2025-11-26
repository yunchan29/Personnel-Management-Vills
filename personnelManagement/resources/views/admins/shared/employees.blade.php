@extends(auth()->user()->role === 'hrAdmin' ? 'layouts.hrAdmin' : 'layouts.hrStaff')

@section('content')
<section class="p-6 max-w-7xl mx-auto" x-data="{
    showProfile: false,
    selectedEmployee: null,
    loading: false,
    requirementsOpen: false,
    requirementsApplicantName: '',
    requirementsFile201: null,
    requirementsOtherFiles: [],
    requirementsApplicantId: null,
    requirementsContractStart: null,
    requirementsContractEnd: null,
    requirementsApplicationStatus: null,
    searchQuery: '',
    filterCompany: 'all',
    filterContractStatus: 'all',
    sendingEmail: false,
    statusModal: {
        show: false,
        type: '',
        message: ''
    },
    showStatusModal(type, message) {
        this.statusModal = { show: true, type, message };
        setTimeout(() => {
            this.statusModal.show = false;
        }, 3000);
    },
    async openProfile(employeeId) {
        this.loading = true;
        try {
            const response = await fetch(`/users/${employeeId}/details`);
            if (!response.ok) throw new Error('Failed to fetch employee details');
            const data = await response.json();
            this.selectedEmployee = data;
            this.showProfile = true;
        } catch (error) {
            console.error('Error fetching employee details:', error);
            alert('Failed to load employee details');
        } finally {
            this.loading = false;
        }
    },
    async openRequirements(employeeId, employeeName, contractStart = null, contractEnd = null, applicationStatus = null) {
        this.requirementsApplicantId = employeeId;
        this.requirementsApplicantName = employeeName;
        this.requirementsContractStart = contractStart;
        this.requirementsContractEnd = contractEnd;
        this.requirementsApplicationStatus = applicationStatus;
        try {
            const response = await fetch(`/file-201/${employeeId}`);
            if (!response.ok) throw new Error('Failed to fetch requirements');
            const data = await response.json();
            this.requirementsFile201 = data.file201;
            this.requirementsOtherFiles = data.otherFiles || [];
            this.requirementsOpen = true;
        } catch (error) {
            console.error('Error fetching requirements:', error);
            alert('Failed to load requirements');
        }
    },
    closeRequirements() {
        this.requirementsOpen = false;
        this.requirementsFile201 = null;
        this.requirementsOtherFiles = [];
        this.requirementsContractStart = null;
        this.requirementsContractEnd = null;
        this.requirementsApplicationStatus = null;
    },
    hasMissingRequirements() {
        const requiredDocs = ['Barangay Clearance', 'NBI Clearance', 'Police Clearance', 'Medical Certificate', 'Birth Certificate'];
        return requiredDocs.some(doc => !this.requirementsOtherFiles.some(f => f.type === doc));
    },
    async sendEmailRequirements() {
        if (!this.requirementsApplicantId || this.sendingEmail) return;
        this.sendingEmail = true;
        try {
            const userRole = '{{ auth()->user()->role }}';
            const response = await fetch(`/${userRole}/applicants/${this.requirementsApplicantId}/send-missing-requirements`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json'
                }
            });
            const result = await response.json();
            if (response.ok) {
                this.showStatusModal('success', result.message || 'Email sent successfully');
            } else {
                this.showStatusModal('error', result.message || 'Failed to send email');
            }
        } catch (error) {
            console.error('Error sending email:', error);
            this.showStatusModal('error', 'Failed to send email. Please try again.');
        } finally {
            this.sendingEmail = false;
        }
    }
}">
    <h1 class="{{ auth()->user()->role === 'hrAdmin' ? 'text-2xl' : 'text-xl' }} font-{{ auth()->user()->role === 'hrAdmin' ? 'semibold' : 'bold' }} text-[#BD6F22] mb-6">Employees</h1>

    {{-- Search and Filter Bar --}}
    <div class="bg-white p-4 rounded-lg shadow-lg mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Search Input --}}
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Search by name, job title, or company..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent text-sm"
                >
            </div>

            {{-- Company Filter --}}
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                <select
                    x-model="filterCompany"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent text-sm"
                >
                    <option value="all">All Companies</option>
                    @php
                        $companies = $employees->pluck('job.company_name')->filter()->unique()->sort()->values();
                    @endphp
                    @foreach($companies as $company)
                        <option value="{{ $company }}">{{ $company }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Contract Status Filter --}}
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contract Status</label>
                <select
                    x-model="filterContractStatus"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent text-sm"
                >
                    <option value="all">All Contracts</option>
                    <option value="pending">Pending</option>
                    <option value="active">Active</option>
                    <option value="expiring">Expiring</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Employees List --}}
    <div class="overflow-x-auto relative bg-white p-6 rounded-lg shadow-lg">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="border-b font-semibold bg-gray-50">
                <tr>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Job Title</th>
                    <th class="py-3 px-4">Company</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Contract Status</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    @php
                        // Calculate contract status for filtering
                        $contractStatusValue = 'n/a';
                        if ($employee->application && $employee->application->contract_end) {
                            $startDate = $employee->application->contract_start;
                            $endDate = $employee->application->contract_end;
                            $today = now();

                            if ($startDate && $startDate->isFuture()) {
                                $contractStatusValue = 'pending';
                            } elseif ($endDate->isPast()) {
                                $contractStatusValue = 'expired';
                            } else {
                                $daysUntilExpiry = $today->diffInDays($endDate, false);
                                $contractStatusValue = ($daysUntilExpiry <= 30) ? 'expiring' : 'active';
                            }
                        }
                    @endphp
                    <tr
                        class="border-b hover:bg-gray-50 cursor-pointer"
                        @click="openProfile({{ $employee->id }})"
                        x-show="
                            (searchQuery === '' ||
                             '{{ strtolower($employee->full_name) }}'.includes(searchQuery.toLowerCase()) ||
                             '{{ strtolower($employee->job->job_title ?? '') }}'.includes(searchQuery.toLowerCase()) ||
                             '{{ strtolower($employee->job->company_name ?? '') }}'.includes(searchQuery.toLowerCase())) &&
                            (filterCompany === 'all' || filterCompany === '{{ $employee->job->company_name ?? '' }}') &&
                            (filterContractStatus === 'all' || filterContractStatus === '{{ $contractStatusValue }}')
                        "
                        x-transition>
                        <!-- Name -->
                        <td class="py-3 px-4 font-medium whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-3 h-3 rounded-full {{ $employee->active_status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $employee->full_name }}
                            </div>
                        </td>

                        <!-- Job Title -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            {{ $employee->job->job_title ?? '—' }}
                        </td>

                        <!-- Company -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            {{ $employee->job->company_name ?? '—' }}
                        </td>

                        <!-- Status -->
                        <td class="py-3 px-4">
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded {{ $employee->active_status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $employee->active_status }}
                            </span>
                        </td>

                        <!-- Contract Status -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($employee->application && $employee->application->contract_end)
                                @php
                                    $startDate = $employee->application->contract_start;
                                    $endDate = $employee->application->contract_end;
                                    $today = now();

                                    // Check if contract hasn't started yet
                                    if ($startDate && $startDate->isFuture()) {
                                        $statusText = 'Pending';
                                        $statusClass = 'bg-blue-100 text-blue-800';
                                    } elseif ($endDate->isPast()) {
                                        $statusText = 'Expired';
                                        $statusClass = 'bg-red-100 text-red-800';
                                    } else {
                                        $daysUntilExpiry = $today->diffInDays($endDate, false);
                                        if ($daysUntilExpiry <= 30) {
                                            $statusText = 'Expiring';
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                        } else {
                                            $statusText = 'Active';
                                            $statusClass = 'bg-green-100 text-green-800';
                                        }
                                    }
                                @endphp
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-600">
                                    N/A
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4" @click.stop>
                            <button
                                @click="openProfile({{ $employee->id }})"
                                class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Employee Profile Modal --}}
    <div
        x-show="showProfile"
        x-transition
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg overflow-y-auto max-h-[90vh] w-[95%] max-w-4xl shadow-xl relative p-6">
            <!-- Close Button -->
            <button
                @click="showProfile = false; selectedEmployee = null"
                class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 text-2xl font-bold">
                &times;
            </button>

            <template x-if="selectedEmployee">
                <div class="space-y-6">
                    <!-- Header with Profile Picture -->
                    <div class="flex items-center gap-4 pb-4 border-b">
                        <img
                            :src="selectedEmployee.profile_picture ? '{{ asset('storage') }}/' + selectedEmployee.profile_picture : '{{ asset('images/default.png') }}'"
                            :alt="selectedEmployee.full_name"
                            class="rounded-full w-24 h-24 object-cover border-2 border-gray-300 shadow-md">
                        <div>
                            <h2 class="text-2xl font-semibold text-[#BD6F22]" x-text="selectedEmployee.full_name"></h2>
                            <p class="text-gray-600" x-text="selectedEmployee.job_title"></p>
                            <p class="text-sm text-gray-500" x-text="selectedEmployee.company_name"></p>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Email</p>
                                <p class="font-medium" x-text="selectedEmployee.email || '—'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Phone Number</p>
                                <p class="font-medium" x-text="selectedEmployee.phone_number || '—'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Address</p>
                                <p class="font-medium" x-text="selectedEmployee.address || '—'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <p>
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold rounded"
                                        :class="selectedEmployee.active_status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        x-text="selectedEmployee.active_status">
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Application Status -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Application Status</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <p class="font-medium" x-text="selectedEmployee.application_status"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Contract Status</p>
                                <template x-if="selectedEmployee.contract_start && selectedEmployee.contract_end">
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold rounded"
                                        x-data="{
                                            getContractStatus() {
                                                if (!selectedEmployee.contract_start || !selectedEmployee.contract_end) return { text: 'N/A', class: 'bg-gray-100 text-gray-600' };

                                                const today = new Date();
                                                const startDate = new Date(selectedEmployee.contract_start);
                                                const endDate = new Date(selectedEmployee.contract_end);

                                                if (startDate > today) {
                                                    return { text: 'Pending', class: 'bg-blue-100 text-blue-800' };
                                                } else if (endDate < today) {
                                                    return { text: 'Expired', class: 'bg-red-100 text-red-800' };
                                                } else {
                                                    const daysUntilExpiry = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));
                                                    if (daysUntilExpiry <= 30) {
                                                        return { text: 'Expiring', class: 'bg-yellow-100 text-yellow-800' };
                                                    } else {
                                                        return { text: 'Active', class: 'bg-green-100 text-green-800' };
                                                    }
                                                }
                                            }
                                        }"
                                        :class="getContractStatus().class"
                                        x-text="getContractStatus().text"
                                    ></span>
                                </template>
                                <template x-if="!selectedEmployee.contract_start || !selectedEmployee.contract_end">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-600">
                                        N/A
                                    </span>
                                </template>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Contract Start</p>
                                <p class="font-medium" x-text="selectedEmployee.contract_start || '—'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Contract End</p>
                                <p class="font-medium" x-text="selectedEmployee.contract_end || 'Ongoing'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4 border-t">
                        <button
                            @click="openRequirements(selectedEmployee.id, selectedEmployee.full_name, selectedEmployee.contract_start, selectedEmployee.contract_end, selectedEmployee.application_status)"
                            class="bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded shadow hover:bg-blue-700">
                            View Requirements
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Requirements Modal --}}
    <div
        x-show="requirementsOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
        @click.self="closeRequirements()"
    >
        <!-- Modal wrapper with transition -->
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="bg-white rounded-lg shadow-xl w-full max-w-3xl sm:max-w-lg md:max-w-2xl lg:max-w-3xl flex flex-col max-h-[90vh] relative"
        >
            <!-- Close button -->
            <button
                @click="closeRequirements()"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 z-10"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Scrollable content -->
            <div class="px-4 sm:px-6 py-6 overflow-y-auto">

                <!-- Header -->
                <h2 class="text-lg sm:text-xl font-bold text-[#BD6F22] mb-4">
                    Requirements for <span x-text="requirementsApplicantName"></span>
                </h2>

                <!-- Contract Status Section -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="text-md font-semibold text-gray-800 mb-3">Contract Status</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Application Status</label>
                            <p class="text-gray-800 font-medium" x-text="requirementsApplicationStatus || '—'"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Contract Start</label>
                            <p class="text-gray-800 font-medium" x-text="requirementsContractStart || '—'"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Contract End</label>
                            <p class="text-gray-800 font-medium" x-text="requirementsContractEnd || 'Ongoing'"></p>
                        </div>
                    </div>
                    <div class="mt-3" x-show="requirementsContractStart && requirementsContractEnd">
                        <label class="block text-sm font-medium text-gray-600">Status</label>
                        <span
                            class="inline-block px-2 py-1 text-xs font-semibold rounded mt-1"
                            x-data="{
                                getContractStatus() {
                                    if (!requirementsContractStart || !requirementsContractEnd) return { text: 'N/A', class: 'bg-gray-100 text-gray-600' };

                                    const today = new Date();
                                    const startDate = new Date(requirementsContractStart);
                                    const endDate = new Date(requirementsContractEnd);

                                    if (startDate > today) {
                                        return { text: 'Pending', class: 'bg-blue-100 text-blue-800' };
                                    } else if (endDate < today) {
                                        return { text: 'Expired', class: 'bg-red-100 text-red-800' };
                                    } else {
                                        const daysUntilExpiry = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));
                                        if (daysUntilExpiry <= 30) {
                                            return { text: 'Expiring', class: 'bg-yellow-100 text-yellow-800' };
                                        } else {
                                            return { text: 'Active', class: 'bg-green-100 text-green-800' };
                                        }
                                    }
                                }
                            }"
                            :class="getContractStatus().class"
                            x-text="getContractStatus().text"
                        ></span>
                    </div>
                </div>

                <!-- File201 details -->
                <div class="mb-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-3">Government IDs</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                        <template x-for="[label, value] in [
                            ['SSS Number', requirementsFile201?.sss_number ?? '—'],
                            ['PhilHealth Number', requirementsFile201?.philhealth_number ?? '—'],
                            ['Pag-IBIG Number', requirementsFile201?.pagibig_number ?? '—'],
                            ['TIN ID Number', requirementsFile201?.tin_id_number ?? '—']
                        ]" :key="label">
                            <div>
                                <label class="block text-sm font-medium text-gray-600" x-text="label"></label>
                                <p class="text-gray-800 font-medium break-words" x-text="value"></p>
                            </div>
                        </template>
                    </div>
                </div>
                <!-- Required Documents -->
                <div>
                    <h3 class="text-md font-semibold text-gray-800 mb-3">Required Documents</h3>
                    <ul class="space-y-3">
                        <template x-for="doc in ['Barangay Clearance', 'NBI Clearance', 'Police Clearance', 'Medical Certificate', 'Birth Certificate']" :key="doc">
                            <li
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 rounded-lg border transition text-sm sm:text-base"
                                :class="requirementsOtherFiles.some(f => f.type === doc)
                                    ? 'border-green-300 bg-green-50 hover:bg-green-100'
                                    : 'border-red-300 bg-red-50 hover:bg-red-100'"
                            >
                                <!-- Document name + status -->
                                <div class="flex flex-col mb-2 sm:mb-0">
                                    <span
                                        x-text="doc"
                                        class="font-medium"
                                        :class="requirementsOtherFiles.some(f => f.type === doc)
                                            ? 'text-green-700'
                                            : 'text-red-600'"
                                    ></span>
                                    <span
                                        class="text-xs mt-1"
                                        :class="requirementsOtherFiles.some(f => f.type === doc)
                                            ? 'text-green-600 font-semibold'
                                            : 'text-red-500 italic'"
                                        x-text="requirementsOtherFiles.some(f => f.type === doc) ? 'Submitted' : 'Missing'">
                                    </span>
                                </div>

                                <!-- Action -->
                                <template x-if="requirementsOtherFiles.some(f => f.type === doc)">
                                    <a
                                        :href="'/storage/' + (requirementsOtherFiles.find(f => f.type === doc)?.file_path)"
                                        target="_blank"
                                        class="text-sm font-medium text-blue-600 hover:underline"
                                    >
                                        View / Download
                                    </a>
                                </template>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Sticky Email Button -->
        <div
            class="border-t bg-white px-4 py-3 flex justify-end sticky bottom-0"
            x-show="hasMissingRequirements()"
        >
            <button
                type="button"
                @click="sendEmailRequirements()"
                :disabled="sendingEmail"
                class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
                <svg x-show="sendingEmail" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="sendingEmail ? 'Sending...' : 'Email Requirements'"></span>
            </button>
        </div>
        </div>
    </div>

    {{-- Status Modal --}}
    <div
        x-show="statusModal.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed top-4 right-4 z-[60] max-w-sm"
        x-cloak
    >
        <div
            class="rounded-lg shadow-lg p-4 flex items-start gap-3"
            :class="{
                'bg-green-50 border border-green-200': statusModal.type === 'success',
                'bg-red-50 border border-red-200': statusModal.type === 'error'
            }"
        >
            <!-- Icon -->
            <div class="flex-shrink-0">
                <svg x-show="statusModal.type === 'success'" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg x-show="statusModal.type === 'error'" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Message -->
            <div class="flex-1">
                <p
                    class="text-sm font-medium"
                    :class="{
                        'text-green-800': statusModal.type === 'success',
                        'text-red-800': statusModal.type === 'error'
                    }"
                    x-text="statusModal.message"
                ></p>
            </div>

            <!-- Close Button -->
            <button
                @click="statusModal.show = false"
                class="flex-shrink-0 text-gray-400 hover:text-gray-600"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

</section>

<!-- Optional Alpine Cloak -->
<style>[x-cloak] { display: none !important; }</style>
@endsection
