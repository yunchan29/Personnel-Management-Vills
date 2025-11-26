@props(['companies', 'selectedJob'])

<div x-data="{
    openReportModal: false,
    reportType: '{{ request('status', 'all') }}',
    dateRange: '{{ request('range', 'monthly') }}',
    company: '{{ request('company', 'all') }}',
    startDate: '',
    endDate: ''
}"
    >
    <button @click="openReportModal = true"
        class="fixed bottom-6 right-6 bg-[#BD6F22] hover:bg-[#a95e1d] text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium z-40">
        Generate Applicants Report
    </button>

    <!-- Modal Overlay -->
    <div x-show="openReportModal" x-transition.opacity x-cloak
        class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">

        <!-- Modal Card -->
        <div @click.away="openReportModal = false"
            class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 relative">

            <!-- Close Button -->
            <button @click="openReportModal = false"
                class="absolute top-4 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
                &times;
            </button>

            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                Generate Applicants Report
            </h2>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Report Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Report Type</label>
                    <select x-model="reportType"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                        <option value="all">All Applicants</option>
                        <option value="approved">Approved Applicants</option>
                        <option value="declined">Declined Applicants</option>
                    </select>
                </div>

                <!-- Company -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Company</label>
                   <select name="company" x-model="company"
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
                    <select x-model="dateRange"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                        <option value="monthly">This Month</option>
                        <option value="quarterly">This Quarter</option>
                        <option value="yearly">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
            </div>

            <!-- Custom Range -->
            <template x-if="dateRange === 'custom'">
                <div class="mt-4 flex items-center gap-2">
                    <input type="date" name="start" x-model="startDate"
                        class="w-1/2 border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                    <span class="text-gray-500">to</span>
                    <input type="date" name="end" x-model="endDate"
                        class="w-1/2 border-gray-300 rounded-lg shadow-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]">
                </div>
            </template>

            <!-- Buttons -->
            <div class="mt-6 flex justify-end gap-3">

                <!-- PDF -->
                <form method="GET" action="{{ route('hrAdmin.userReports.applicants', 'pdf') }}">
                    <!-- Hidden inputs to pass filter values -->
                    <input type="hidden" name="company" x-bind:value="company">
                    <input type="hidden" name="status" x-bind:value="reportType">
                    <input type="hidden" name="range" x-bind:value="dateRange">
                    <input type="hidden" name="start" x-bind:value="startDate">
                    <input type="hidden" name="end" x-bind:value="endDate">

                    <button type="submit"
                        class="bg-[#BD6F22] hover:bg-[#a95e1d] text-white px-5 py-2 rounded-lg shadow-md text-sm font-medium transition">
                        Download PDF
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
