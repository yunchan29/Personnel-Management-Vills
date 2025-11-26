<div x-data="applicantsHandler()" x-init="init(); pageContext = 'applicants'" class="relative">

    <!-- Search and Filter Section -->
    <div class="mb-4 bg-white border border-gray-200 rounded-lg shadow-sm p-4" x-data="{ showFilters: false, searchTerm: '', sortBy: 'name_asc' }">
        <div class="space-y-4">
            <!-- Search Bar with Filter Toggle -->
            <div class="flex items-center gap-2 border border-gray-300 rounded-md px-3 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    x-model="searchTerm"
                    class="flex-1 border-0 focus:ring-0 text-sm p-0"
                    placeholder="Search by name, position, or company..."
                    @input="filterApplicants()"
                >

                <!-- Filter Toggle Button -->
                <button
                    type="button"
                    @click="showFilters = !showFilters"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm font-medium transition whitespace-nowrap"
                    :class="showFilters ? 'bg-[#BD6F22] text-white' : 'text-gray-500 hover:text-[#BD6F22] hover:bg-gray-50'"
                    title="Toggle sort options"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Sort</span>
                </button>
            </div>

            <!-- Filter Options (Collapsible) -->
            <div
                x-show="showFilters"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="pt-3 border-t border-gray-200"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Sort By -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                        <select
                            x-model="sortBy"
                            @change="sortApplicants()"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-[#BD6F22] focus:border-[#BD6F22]"
                        >
                            <option value="name_asc">Name (A-Z)</option>
                            <option value="name_desc">Name (Z-A)</option>
                            <option value="position_asc">Position (A-Z)</option>
                            <option value="position_desc">Position (Z-A)</option>
                            <option value="company_asc">Company (A-Z)</option>
                            <option value="company_desc">Company (Z-A)</option>
                            <option value="date_newest">Applied Date (Newest)</option>
                            <option value="date_oldest">Applied Date (Oldest)</option>
                        </select>
                    </div>

                    <!-- Clear Button -->
                    <div class="flex items-end">
                        <button
                            type="button"
                            @click="searchTerm = ''; sortBy = 'name_asc'; resetApplicants();"
                            class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Applicants Table -->
    <div class="overflow-x-auto relative bg-white p-4 rounded-lg shadow-lg w-full">
        <!-- Bulk Approve Button -->
        <div x-show="selectedApplicants.length > 0"
             x-transition
             class="flex flex-wrap gap-2 mb-4">
                 <!-- Left side: Master Checkbox -->
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input
                        type="checkbox"
                        x-ref="masterCheckbox"
                        @change="toggleSelectAll($event)"
                        class="rounded border-gray-300"
                    >
                    <span>Select All</span>
                </label>
                <!-- Bulk Approve -->
                <div class="relative">
                    <button
                        @click="bulkAction('approved')"
                        class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2
                                hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                    <!-- Lucide: Graduation Cap -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5L2 10z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    <span class="text-sm" x-text="`Approve (${selectedApplicants.length})`"></span>
                </button>
                </div>

                <!-- Bulk Decline -->
                <div class="relative">
                    <button
                        @click="bulkAction('declined')"
                         class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2
                                hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
                    <!-- Lucide: Graduation Cap -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5L2 10z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    <span class="text-sm" x-text="`Decline (${selectedApplicants.length})`"></span>
                </button>
                </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
            <thead class="border-b font-semibold bg-gray-50">
                <tr>
                    <th class="py-3 px-4"></th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Position</th>
                    <th class="py-3 px-4">Company</th>
                    <th class="py-3 px-4">Applied On</th>
                    <th class="py-3 px-4">Resume</th>
                    <th class="py-3 px-4">Profile</th>
                    <th class="py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                    @if($application->user->active_status === 'Active')
                    {{-- Controller already filters by pending status --}}
                    <tr
                        data-applicant-id="{{ $application->id }}"
                        data-status="{{ $application->status->value }}"
                        data-name="{{ $application->user->first_name }} {{ $application->user->last_name }}"
                        data-position="{{ $application->job->job_title ?? 'N/A' }}"
                        data-company="{{ $application->job->company_name ?? 'N/A' }}"
                        data-date="{{ \Carbon\Carbon::parse($application->created_at)->format('Y-m-d') }}"
                        x-show="!removedApplicants.includes({{ $application->id }})"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        @applicant-approved.window="if ($event.detail.id === {{ $application->id }}) removedApplicants.push({{ $application->id }})"
                        class="border-b hover:bg-gray-50 applicant-row"
                    >
                        <td class="py-3 px-4">
                            <input
                                type="checkbox"
                                class="applicant-checkbox rounded border-gray-300"
                                :value="JSON.stringify({
                                    application_id: {{ $application->id }},
                                    user_id: {{ $application->user_id }},
                                    name: '{{ $application->user->first_name }} {{ $application->user->last_name }}',
                                })"
                                :checked="selectedApplicants.some(a => a.application_id === {{ $application->id }})"
                                @change="toggleItem($event, {{ $application->id }}); updateMasterCheckbox()"
                            />
                        </td>
                        <td class="py-3 px-4 font-medium whitespace-nowrap">
                            {{ $application->user->first_name . ' ' . $application->user->last_name }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            {{ $application->job->job_title ?? 'N/A' }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            {{ $application->job->company_name ?? 'N/A' }}
                        </td>
                        <td class="py-3 px-4 italic whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($application->created_at)->format('F d, Y') }}
                        </td>

                          <!-- Resume -->

                        <td class="py-3 px-4">
                            @if($application->resume_snapshot)
                                <button
                                    @click="openResume('{{ asset('storage/' . $application->resume_snapshot) }}')"
                                    class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                    View
                                </button>
                            @elseif($application->user->resume && $application->user->resume->resume)
                                <button
                                    @click="openResume('{{ asset('storage/' . $application->user->resume->resume) }}')"
                                    class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                    View
                                </button>
                            @else
                                <span class="text-gray-500 italic">None</span>
                            @endif
                        </td>

                           <!-- Profile -->

                        <td class="py-3 px-4">
                            @if($application->user->active_status === 'Active')
                                <button
                                    @click="openProfile({{ $application->user->id }})"
                                    class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                    View
                                </button>
                            @else
                                <span class="text-gray-400 italic">Inactive</span>
                            @endif
                        </td>

                        <!-- Status -->

                        <td class="py-3 px-4">
                            <span class="text-xs px-2 py-1 rounded-full transition-colors duration-300 whitespace-nowrap {{ $application->status_badge_class }}">
                                {{ $application->status_label }}
                            </span>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-gray-500">No applicants pending approval.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>

    <!-- Feedback Toast -->
    <x-shared.feedbackToast />

    <!-- ✅ INCLUDE MODALS INSIDE THE ROOT x-data DIV -->
    @include('components.hrAdmin.modals.resume')
    @include('components.hrAdmin.modals.statusConfirmation')

    @foreach ($applications as $application)
        @include('components.hrAdmin.modals.profile', ['user' => $application->user])
    @endforeach
</div>

<script>
    window.bulkApproveUrl = "{{ route('hrAdmin.applications.bulkUpdateStatus') }}";

    // Search and filter functions
    function filterApplicants() {
        const searchTerm = document.querySelector('[x-model="searchTerm"]').value.toLowerCase();
        const rows = document.querySelectorAll('.applicant-row');

        rows.forEach(row => {
            const name = row.dataset.name?.toLowerCase() || '';
            const position = row.dataset.position?.toLowerCase() || '';
            const company = row.dataset.company?.toLowerCase() || '';

            const matches = name.includes(searchTerm) ||
                          position.includes(searchTerm) ||
                          company.includes(searchTerm);

            row.style.display = matches ? '' : 'none';
        });
    }

    function sortApplicants() {
        const sortBy = document.querySelector('[x-model="sortBy"]').value;
        const tbody = document.querySelector('.applicant-row')?.parentElement;
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('.applicant-row'));

        rows.sort((a, b) => {
            let aVal, bVal;

            switch(sortBy) {
                case 'name_asc':
                    aVal = a.dataset.name || '';
                    bVal = b.dataset.name || '';
                    return aVal.localeCompare(bVal);
                case 'name_desc':
                    aVal = a.dataset.name || '';
                    bVal = b.dataset.name || '';
                    return bVal.localeCompare(aVal);
                case 'position_asc':
                    aVal = a.dataset.position || '';
                    bVal = b.dataset.position || '';
                    return aVal.localeCompare(bVal);
                case 'position_desc':
                    aVal = a.dataset.position || '';
                    bVal = b.dataset.position || '';
                    return bVal.localeCompare(aVal);
                case 'company_asc':
                    aVal = a.dataset.company || '';
                    bVal = b.dataset.company || '';
                    return aVal.localeCompare(bVal);
                case 'company_desc':
                    aVal = a.dataset.company || '';
                    bVal = b.dataset.company || '';
                    return bVal.localeCompare(aVal);
                case 'date_newest':
                    aVal = new Date(a.dataset.date || 0);
                    bVal = new Date(b.dataset.date || 0);
                    return bVal - aVal;
                case 'date_oldest':
                    aVal = new Date(a.dataset.date || 0);
                    bVal = new Date(b.dataset.date || 0);
                    return aVal - bVal;
                default:
                    return 0;
            }
        });

        rows.forEach(row => tbody.appendChild(row));
    }

    function resetApplicants() {
        const rows = document.querySelectorAll('.applicant-row');
        rows.forEach(row => row.style.display = '');
    }
</script>

<!-- Scripts -->
<script src="{{ asset('js/utils/checkboxUtils.js') }}"></script>
<script src="{{ asset('js/applicantsHandler.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Alpine Cloak -->
<style>[x-cloak] { display: none !important; }</style>
