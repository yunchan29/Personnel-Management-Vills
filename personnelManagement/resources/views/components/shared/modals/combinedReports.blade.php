@props(['companies'])

<div x-data="{
    openReportModal: false,
    activeTab: 'applicants',

    applicant: {
        reportType: '{{ request('status', 'all') }}',
        company: '{{ request('company', 'all') }}',
        dateRange: '{{ request('range', 'monthly') }}',
        startDate: '',
        endDate: ''
    },

    employee: {
        company: '{{ request('company', 'all') }}',
        dateRange: '{{ request('range', 'all') }}',
        startDate: '',
        endDate: ''
    }
}">
    <!-- Floating Button -->
    <button @click="openReportModal = true"
        class="fixed bottom-6 right-6 bg-[#BD6F22] hover:bg-[#a95e1d] text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium z-40">
        Generate Report
    </button>

    <!-- Modal Overlay -->
    <div x-show="openReportModal" x-transition.opacity x-cloak
        class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">

        <!-- Modal Card -->
        <div @click.away="openReportModal = false"
            class="bg-white w-full max-w-2xl rounded-2xl shadow-xl p-6 relative">

            <!-- Close Button -->
            <button @click="openReportModal = false"
                class="absolute top-4 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
                &times;
            </button>

            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                Generate Reports
            </h2>

            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 mb-4">
                <button @click="activeTab = 'applicants'"
                    :class="activeTab === 'applicants' ? 'border-b-2 border-[#BD6F22] bg-[#FEF3E2] text-[#BD6F22] font-semibold' : 'text-gray-600 hover:text-[#BD6F22] hover:bg-gray-50'"
                    class="flex-1 py-3 px-4 text-sm font-medium transition-all duration-200 focus:outline-none">
                    Applicant Report
                </button>
                <button @click="activeTab = 'employees'"
                    :class="activeTab === 'employees' ? 'border-b-2 border-[#BD6F22] bg-[#FEF3E2] text-[#BD6F22] font-semibold' : 'text-gray-600 hover:text-[#BD6F22] hover:bg-gray-50'"
                    class="flex-1 py-3 px-4 text-sm font-medium transition-all duration-200 focus:outline-none">
                    Employee Report
                </button>
            </div>

            <!-- Applicant Report Tab Content -->
            <div x-show="activeTab === 'applicants'">
                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Report Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Report Type</label>
                        <select x-model="applicant.reportType"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                            <option value="all">All Applicants</option>
                            <option value="approved">Approved Applicants</option>
                            <option value="declined">Declined Applicants</option>
                        </select>
                    </div>

                    <!-- Company -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Company</label>
                        <select x-model="applicant.company"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                            <option value="all">All Companies</option>
                            @foreach ($companies as $company_name)
                                <option value="{{ $company_name }}">{{ $company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Date Range</label>
                        <select x-model="applicant.dateRange"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                            <option value="monthly">This Month</option>
                            <option value="quarterly">This Quarter</option>
                            <option value="yearly">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Range for Applicants -->
                <template x-if="applicant.dateRange === 'custom'">
                    <div class="mt-4 flex items-center gap-2">
                        <input type="date" x-model="applicant.startDate"
                            class="w-1/2 border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                        <span class="text-gray-500">to</span>
                        <input type="date" x-model="applicant.endDate"
                            class="w-1/2 border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                    </div>
                </template>

                <!-- Applicant Report Submit Button -->
                <div class="mt-6 flex justify-end gap-3">
                    <form method="GET" action="{{ route('hrAdmin.userReports.applicants', 'pdf') }}">
                        <input type="hidden" name="company" x-bind:value="applicant.company">
                        <input type="hidden" name="status" x-bind:value="applicant.reportType">
                        <input type="hidden" name="range" x-bind:value="applicant.dateRange">
                        <input type="hidden" name="start" x-bind:value="applicant.startDate">
                        <input type="hidden" name="end" x-bind:value="applicant.endDate">

                        <button type="submit"
                            class="bg-[#BD6F22] hover:bg-[#a95e1d] text-white px-5 py-2 rounded-lg shadow-md text-sm font-medium transition">
                            Download PDF
                        </button>
                    </form>
                </div>
            </div>

            <!-- Employee Report Tab Content -->
            <div x-show="activeTab === 'employees'">
                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Company -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Company</label>
                        <select x-model="employee.company"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                            <option value="all">All Companies</option>
                            @foreach ($companies as $company_name)
                                <option value="{{ $company_name }}">{{ $company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Date Range</label>
                        <select x-model="employee.dateRange"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                            <option value="all">All Time</option>
                            <option value="monthly">This Month</option>
                            <option value="quarterly">This Quarter</option>
                            <option value="yearly">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                </div>

                <!-- Custom Range for Employees -->
                <template x-if="employee.dateRange === 'custom'">
                    <div class="mt-4 flex items-center gap-2">
                        <input type="date" x-model="employee.startDate"
                            class="w-1/2 border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                        <span class="text-gray-500">to</span>
                        <input type="date" x-model="employee.endDate"
                            class="w-1/2 border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                    </div>
                </template>

                <!-- Employee Report Submit Button -->
                <div class="mt-6 flex justify-end gap-3">
                    <form method="GET" action="{{ route('hrAdmin.userReports.employees', 'pdf') }}">
                        <input type="hidden" name="company" x-bind:value="employee.company">
                        <input type="hidden" name="range" x-bind:value="employee.dateRange">
                        <input type="hidden" name="start" x-bind:value="employee.startDate">
                        <input type="hidden" name="end" x-bind:value="employee.endDate">

                        <button type="submit"
                            class="bg-[#BD6F22] hover:bg-[#a95e1d] text-white px-5 py-2 rounded-lg shadow-md text-sm font-medium transition">
                            Download PDF
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
