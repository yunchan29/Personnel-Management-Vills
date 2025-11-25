<div x-data="applicantsHandler()" x-init="init()" class="relative">
    <div x-data="trainingHandler($data)">

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
                        @input="filterTraining()"
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
                                @change="sortTraining()"
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
                                <option value="training_newest">Training Date (Newest)</option>
                                <option value="training_oldest">Training Date (Oldest)</option>
                            </select>
                        </div>

                        <!-- Clear Button -->
                        <div class="flex items-end">
                            <button
                                type="button"
                                @click="searchTerm = ''; sortBy = 'name_asc'; resetTraining();"
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
        <div class="overflow-x-auto relative bg-white p-6 rounded-lg shadow-lg w-full">
          <div x-show="selectedApplicants.length > 0"
               x-transition
               class="flex flex-wrap gap-2 mb-4">

             <!-- Master Checkbox -->
        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input
            type="checkbox"
            x-ref="masterCheckbox"
            @change="toggleSelectAll($event)"
            class="rounded border-gray-300"
          >
          <span>Select All</span>
        </label>

    <!-- Set Training (Primary Solid) -->
    <button
        @click="bulkSetTraining"
        class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2
               hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
        <!-- Lucide: Graduation Cap -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <path d="M22 10v6M2 10l10-5 10 5-10 5L2 10z"></path>
          <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
        </svg>
        <span class="text-sm" x-text="`Set Training (${selectedApplicants.length})`"></span>
    </button>

    <!-- Resched Training (Accent Solid) -->
    <button
        @click="bulkReschedTraining"
        class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2
               hover:text-[#8B4513] transition-colors duration-150 focus:outline-none">
        <!-- Lucide: Refresh-CCW -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <path d="M3 2v6h6"></path>
          <path d="M21 12a9 9 0 0 0-9-9H9"></path>
          <path d="M21 22v-6h-6"></path>
          <path d="M3 12a9 9 0 0 0 9 9h3"></path>
        </svg>
        <span class="text-sm" x-text="`Resched Training (${selectedApplicants.length})`"></span>
    </button>
</div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="border-b font-semibold bg-gray-50">
                    <tr>
                        <th class="py-3 px-4"></th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Position</th>
                        <th class="py-3 px-4">Company</th>
                        <th class="py-3 px-4">Applied On</th>
                        <th class="py-3 px-4">Training Schedule</th>
                        <th class="py-3 px-4">Training Time</th>
                        <th class="py-3 px-4">Location</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        {{-- Controller already filters by interviewed/scheduled_for_training statuses --}}
                        @php
                            $fullName = $application->user->first_name . ' ' . $application->user->last_name;
                            $training = $application->trainingSchedule;
                            $trainingRange = '';
                            if ($training && $training->start_date && $training->end_date) {
                                $start = \Carbon\Carbon::parse($training->start_date)->format('m/d/Y');
                                $end = \Carbon\Carbon::parse($training->end_date)->format('m/d/Y');
                                $trainingRange = "$start - $end";
                            }
                        @endphp

                            <tr
                                data-applicant-id="{{ $application->id }}"
                                data-name="{{ $application->user->first_name }} {{ $application->user->last_name }}"
                                data-position="{{ $application->job->job_title ?? 'N/A' }}"
                                data-company="{{ $application->job->company_name ?? 'N/A' }}"
                                data-date="{{ \Carbon\Carbon::parse($application->created_at)->format('Y-m-d') }}"
                                data-training-date="{{ optional($application->trainingSchedule)?->start_date ? \Carbon\Carbon::parse($application->trainingSchedule->start_date)->format('Y-m-d') : '' }}"
                                data-status="{{ $application->status->value }}"
                                data-training-range="{{ $trainingRange }}"
                                x-cloak
                                x-show="(showAll || {{ $application->trainingSchedule ? 'false' : 'true' }})
                                        && !removedApplicants.includes({{ $application->id }})"
                                class="border-b hover:bg-gray-50 transition-opacity duration-300 ease-in-out training-row"
                            >

                           <td class="py-3 px-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input
                                type="checkbox"
                                class="applicant-checkbox"
                                :value="JSON.stringify({
                                    application_id: {{ $application->id }},
                                    user_id: {{ $application->user_id }},
                                    name: '{{ $application->user->first_name }} {{ $application->user->last_name }}',
                                    has_training: {{ $application->trainingSchedule ? 'true' : 'false' }},
                                    training_start_date: '{{ optional($application->trainingSchedule)?->start_date ?? '' }}',
                                    training_end_date: '{{ optional($application->trainingSchedule)?->end_date ?? '' }}',
                                    training_start_time: '{{ optional($application->trainingSchedule)?->start_time ?? '' }}',
                                    training_end_time: '{{ optional($application->trainingSchedule)?->end_time ?? '' }}',
                                    training_location: '{{ optional($application->trainingSchedule)?->location ?? '' }}',
                                })"
                                :checked="selectedApplicants.some(a => a.application_id === {{ $application->id }})"
                                @change="toggleItem($event, {{ $application->id }}); updateMasterCheckbox()"
                                />
                                <!-- Custom checkmark -->
                                <svg class="absolute left-0.5 top-0.5 hidden peer-checked:block w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" />
                                </svg>
                            </label>
                            </td>


                            <!-- Name -->
                            <td class="py-3 px-4 font-medium whitespace-nowrap">
                                {{ $application->user->first_name }} {{ $application->user->last_name }}
                            </td>

                            <!-- Position + Company -->
                            <td class="py-3 px-4">{{ $application->job->job_title ?? 'N/A' }}</td>
                            <td class="py-3 px-4">{{ $application->job->company_name ?? 'N/A' }}</td>

                            <!-- Applied On -->
                            <td class="py-3 px-4 italic">
                                {{ \Carbon\Carbon::parse($application->created_at)->format('F d, Y') }}
                            </td>

                            <!-- Training Schedule -->
                            <td class="py-3 px-4 text-sm text-gray-700">
                                @if ($application->trainingSchedule)
                                    <span>
                                        {{ \Carbon\Carbon::parse($application->trainingSchedule->start_date)->format('m/d/Y') }}
                                        -
                                        {{ \Carbon\Carbon::parse($application->trainingSchedule->end_date)->format('m/d/Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">None</span>
                                @endif
                            </td>

                            <!-- Training Time -->
                            <td class="py-3 px-4 text-sm text-gray-700">
                                @if ($application->trainingSchedule && $application->trainingSchedule->start_time && $application->trainingSchedule->end_time)
                                    {{ \Carbon\Carbon::parse($application->trainingSchedule->start_time)->format('h:i A') }}
                                    -
                                    {{ \Carbon\Carbon::parse($application->trainingSchedule->end_time)->format('h:i A') }}
                                @else
                                    <span class="text-gray-400 italic">None</span>
                                @endif
                            </td>

                            <!-- Location -->
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ $application->trainingSchedule->location ?? 'N/A' }}
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4 text-sm">
                                @if ($application->trainingSchedule && $application->trainingSchedule->status === 'rescheduled')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 whitespace-nowrap">
                                        <!-- Lucide: Clock icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        Rescheduled Training
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $application->status_badge_class }}">
                                        {{ $application->status_label }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-gray-500">No applicants yet.</td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>

        <!-- Set Training Modal -->
        @include('components.hrAdmin.modals.setTraining')

        <!-- Resume Modal -->
        @include('components.hrAdmin.modals.resume')

        @foreach ($applications as $application)
            @include('components.hrAdmin.modals.profile', ['user' => $application->user])
        @endforeach



        <!-- ✅ Feedback Toast -->
        <x-shared.feedbackToast />

        <!-- ✅ Filter Toggle -->
        <div class="flex justify-center my-6">
            <button
                @click="showAll = !showAll"
                class="px-4 py-2 bg-[#ffffff] text-black text-sm font-medium hover:text-[#a95e1d]">
                <span x-text="showAll ? 'Show Only Pending Training' : 'Show All Applicants'"></span>
            </button>
        </div>



    </div>
</div>

<script>
    // Search and filter functions for training
    function filterTraining() {
        const searchTerm = document.querySelector('[x-model="searchTerm"]').value.toLowerCase();
        const rows = document.querySelectorAll('.training-row');

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

    function sortTraining() {
        const sortBy = document.querySelector('[x-model="sortBy"]').value;
        const tbody = document.querySelector('.training-row')?.parentElement;
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('.training-row'));

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
                case 'training_newest':
                    aVal = new Date(a.dataset.trainingDate || 0);
                    bVal = new Date(b.dataset.trainingDate || 0);
                    return bVal - aVal;
                case 'training_oldest':
                    aVal = new Date(a.dataset.trainingDate || 0);
                    bVal = new Date(b.dataset.trainingDate || 0);
                    return aVal - bVal;
                default:
                    return 0;
            }
        });

        rows.forEach(row => tbody.appendChild(row));
    }

    function resetTraining() {
        const rows = document.querySelectorAll('.training-row');
        rows.forEach(row => row.style.display = '');
    }
</script>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
<script src="{{ asset('js/utils/timeUtils.js') }}"></script>
<script src="{{ asset('js/applicantsHandler.js') }}"></script>
<script src="{{ asset('js/trainingHandler.js') }}"></script>

<!-- Styles -->
<style>
[x-cloak] { display: none !important; }
.animate-checkmark {
    animation: checkmark 0.3s ease-in-out;
}
@keyframes checkmark {
    from { transform: scale(0.8) rotate(-20deg); opacity: 0; }
    to { transform: scale(1) rotate(0); opacity: 1; }
}
.animate-progress-bar {
    animation: progress 3s linear forwards;
}
@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>
