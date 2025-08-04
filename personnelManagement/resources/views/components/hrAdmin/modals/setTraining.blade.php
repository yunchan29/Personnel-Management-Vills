
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
            <span class="font-medium text-gray-800" x-text="selectedApplicantName || 'Applicant'"></span>
        </p>

        <!-- Date Range Input -->
        <div class="mb-5">
            <label for="training_schedule" class="block font-medium text-sm text-gray-700">Training Schedule</label>
            <input
            type="text"
            x-ref="trainingDateRange"
            id="training_schedule"
            name="training_schedule"
            class="form-input rounded-md shadow-sm mt-1 block w-full"
            placeholder="MM/DD/YYYY - MM/DD/YYYY"
            value="{{ old('training_schedule', optional($application->trainingSchedule)->start_date && optional($application->trainingSchedule)->end_date ? \Carbon\Carbon::parse($application->trainingSchedule->start_date)->format('m/d/Y') . ' - ' . \Carbon\Carbon::parse($application->trainingSchedule->end_date)->format('m/d/Y') : '') }}"
            autocomplete="off"
            />
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
                :disabled="loading"
                class="px-4 py-2 text-sm rounded bg-[#BD6F22] text-white hover:bg-[#a95e1d] disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
                <template x-if="loading">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </template>
                <span x-text="loading ? 'Processing...' : 'Confirm'"></span>
            </button>
        </div>
    </div>
</div>