<div x-data="applicantsHandler()" x-init="init()" class="relative">
    <div x-data="trainingHandler($data)">

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
                                data-status="{{ $application->status->value }}"
                                data-training-range="{{ $trainingRange }}"
                                x-cloak
                                x-show="(showAll || '{{ $application->training_schedule }}' === '')
                                        && !removedApplicants.includes({{ $application->id }})"
                                class="border-b hover:bg-gray-50 transition-opacity duration-300 ease-in-out"
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
                                    has_training: {{ $application->trainingSchedule ? 'true' : 'false' }}
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
                                <span class="px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $application->status_badge_class }}">
                                    {{ $application->status_label }}
                                </span>
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
