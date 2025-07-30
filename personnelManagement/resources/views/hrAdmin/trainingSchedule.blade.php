<div x-data="applicantsHandler()" x-init="init()" class="relative">

    <!-- Applicants Table -->
    <div class="overflow-x-auto relative bg-white p-6 rounded-lg shadow-lg">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="border-b font-semibold bg-gray-50">
                <tr>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Position</th>
                    <th class="py-3 px-4">Company</th>
                    <th class="py-3 px-4">Applied On</th>
                    <th class="py-3 px-4">Resume</th>
                    <th class="py-3 px-4">Profile</th>
                    <th class="py-3 px-4">Training Schedule</th>
                    <th class="py-3 px-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                        <tr x-data="{
                                status: '{{ $application->status }}',
                                trainingSchedule: '{{ $application->training_schedule }}',
                                id: {{ $application->id }}
                            }" 
                            x-init="console.log('Applicant ID:', id, 'Status:', status, 'Training Schedule:', trainingSchedule)"
                            x-show="(showAll || !trainingSchedule) && (['interviewed', 'scheduled_for_training'].includes(status)) && !removedApplicants.includes(id)"
                            class="border-b hover:bg-gray-50 transition-opacity duration-300 ease-in-out"
                        >

                        <td class="py-3 px-4 font-medium whitespace-nowrap flex items-center gap-2">
                            <span class="inline-block w-3 h-3 rounded-full {{ $application->user->active_status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $application->user->first_name }} {{ $application->user->last_name }}
                        </td>
                        <td class="py-3 px-4">{{ $application->job->job_title ?? 'N/A' }}</td>
                        <td class="py-3 px-4">{{ $application->job->company_name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 italic">{{ \Carbon\Carbon::parse($application->created_at)->format('F d, Y') }}</td>
                        <td class="py-3 px-4">
                            @if ($application->resume_snapshot)
                                <button @click="openResume('{{ asset('storage/' . $application->resume_snapshot) }}')"
                                        class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                    View
                                </button>
                            @else
                                <span class="text-gray-500 italic">None</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <button @click="openProfile({{ $application->id }})"
                                    class="border border-[#BD6F22] text-[#BD6F22] text-sm font-medium h-8 px-3 rounded hover:bg-[#BD6F22] hover:text-white whitespace-nowrap">
                                View
                            </button>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-700">
                            @if ($application->trainingSchedule?->start_date && $application->trainingSchedule?->end_date)
                                @php
                                    $start = \Carbon\Carbon::parse($application->trainingSchedule->start_date);
                                    $end = \Carbon\Carbon::parse($application->trainingSchedule->end_date);
                                    $display = $start->format('M d') .
                                        ($start->isSameMonth($end) ? '–' . $end->format('d') : ' – ' . $end->format('M d')) .
                                        ', ' . ($start->year === $end->year ? $start->year : $start->year . ' – ' . $end->year);
                                @endphp
                                <span>{{ $display }}</span>
                            @else
                                <span class="text-gray-400 italic">No Schedule Yet</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <button
                                @click="openSetTraining({{ $application->id }}, '{{ $application->user->first_name }} {{ $application->user->last_name }}')"
                                :class="`text-white text-sm font-medium h-8 px-3 rounded whitespace-nowrap ${
                                    '{{ $application->training_schedule }}'
                                        ? 'bg-yellow-500 hover:bg-yellow-600'
                                        : 'bg-blue-600 hover:bg-blue-700'
                                }`"
                                x-text="'{{ $application->training_schedule ? 'Reschedule' : 'Set Training' }}'">
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-gray-500">No applicants yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Resume Modal -->
    @include('components.hrAdmin.modals.resume')

    <!-- Profile Modals -->
    @foreach ($applications as $application)
        @include('components.hrAdmin.modals.profile', ['application' => $application])
    @endforeach

   <!-- ✅ Set Training Modal -->
<div 
    x-show="showTrainingModal" 
    x-transition.opacity 
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" 
    x-cloak
>
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl relative transition-all duration-300">
        <!-- Close Button -->
        <button 
            @click="showTrainingModal = false" 
            class="absolute top-3 right-4 text-gray-400 hover:text-red-500 text-2xl font-bold"
            aria-label="Close"
        >
            &times;
        </button>

        <!-- Modal Title -->
        <h2 class="text-xl font-bold text-[#BD6F22] mb-2">Set Training Schedule</h2>

        <!-- Applicant Name -->
        <p class="text-sm text-gray-600 mb-5">
            Setting schedule for: 
            <span class="font-medium text-gray-800" x-text="trainingApplicant?.name || 'Applicant'"></span>
        </p>

        <!-- Date Range Input -->
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Training Date Range <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                x-ref="trainingDateRange" 
                class="w-full border border-gray-300 focus:border-[#BD6F22] focus:ring-[#BD6F22] rounded-lg px-4 py-2 text-sm shadow-sm transition duration-150"
                placeholder="MM/DD/YYYY - MM/DD/YYYY"
            >
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-2 mt-6">
            <button 
                @click="showTrainingModal = false" 
                class="px-4 py-2 text-sm rounded-lg border border-gray-300 hover:bg-gray-100 transition"
            >
                Cancel
            </button>
            <button 
                @click="submitTrainingSchedule" 
                class="px-4 py-2 text-sm rounded-lg bg-[#BD6F22] text-white hover:bg-[#a95e1d] transition"
            >
                Confirm
            </button>
        </div>
    </div>
</div>


    <!-- ✅ Feedback Toast -->
    <div x-show="feedbackVisible"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-4 rounded-xl shadow-lg z-50 w-80 overflow-hidden"
         x-cloak>
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-white animate-checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="font-semibold text-sm" x-text="feedbackMessage"></span>
        </div>
        <div class="mt-3 h-1 w-full bg-white/20 rounded overflow-hidden">
            <div class="h-full bg-white animate-progress-bar"></div>
        </div>
    </div>

    <!-- ✅ Filter Toggle -->
    <div class="flex justify-center my-6">
        <button
            @click="showAll = !showAll"
            class="px-4 py-2 bg-[#ffffff] text-black text-sm font-medium hover:text-[#a95e1d]">
            <span x-text="showAll ? 'Show Only Pending Training' : 'Show All Applicants'"></span>
        </button>
    </div>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
<script src="{{ asset('js/applicantsHandler.js') }}"></script>

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
