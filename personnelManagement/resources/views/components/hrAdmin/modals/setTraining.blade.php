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
        >&times;</button>

        <!-- Modal Title -->
        <h2 class="text-xl font-bold text-[#BD6F22] mb-2"
            x-text="trainingMode === 'bulk' ? 'Set Training Schedule' : 'Set Training Schedule'"></h2>

        <!-- Applicant(s) Info -->
        <p class="text-sm text-gray-600 mb-5">
            <template x-if="trainingMode === 'single'">
                <span>Setting schedule for: <span class="font-medium text-gray-800" x-text="selectedApplicantName"></span></span>
            </template>
            <template x-if="trainingMode === 'bulk'">
                <span>Setting schedule for <span class="font-medium text-gray-800" x-text="selectedApplicants.length"></span> applicants</span>
            </template>
        </p>

        <!-- Date Range Input -->
        <div class="mb-5">
            <label class="block font-medium text-sm text-gray-700">Training Schedule:</label>
            <input
                type="text"
                x-ref="trainingDateRange"
                class="form-input rounded-md shadow-sm mt-1 block w-full"
                placeholder="MM/DD/YYYY - MM/DD/YYYY"
                autocomplete="off"
            />
        </div>

        <!-- Location Input -->
        <div class="mb-5">
            <label class="block font-medium text-sm text-gray-700">Location:</label>
            <input
                type="text"
                x-model="trainingLocation"
                class="form-input rounded-md shadow-sm mt-1 block w-full"
                placeholder="Enter training location"
            />
        </div>

      <!-- Time Range Input -->
<div class="mb-5">
    <label class="block font-medium text-sm text-gray-700">Training Time</label>

    <div class="flex items-center gap-2">

        <!-- Start Time (AM only) -->
        <div class="flex items-center gap-2 w-full">

            <!-- Hours 5–8 -->
            <select x-model="trainingStartHour" class="form-select rounded-md shadow-sm w-full">
                <option value="" disabled selected hidden>Hour</option>
                <template x-for="h in [5,6,7,8]" :key="h">
                    <option :value="String(h)" x-text="h"></option>
                </template>
            </select>

            <!-- Fixed AM -->
            <span class="font-semibold text-gray-700">AM</span>
        </div>

        <span class="text-gray-500">to</span>

        <!-- End Time (PM only) -->
        <div class="flex items-center gap-2 w-full">

            <!-- Hours 3–5 PM -->
            <select x-model="trainingEndHour" class="form-select rounded-md shadow-sm w-full">
                <option value="" disabled selected hidden>Hour</option>
                <template x-for="h in [1,3,4,5]" :key="h">
                    <option :value="String(h)" x-text="h"></option>
                </template>
            </select>

            <!-- Fixed PM -->
            <span class="font-semibold text-gray-700">PM</span>
        </div>
    </div>
</div>


        <!-- Buttons -->
        <div class="flex justify-end gap-2 mt-6">
            <button @click="showTrainingModal = false" class="px-4 py-2 text-sm rounded-lg border">Cancel</button>
            <button 
                @click="submitTrainingSchedule"
                :disabled="loading"
                class="px-4 py-2 text-sm rounded bg-[#BD6F22] text-white hover:bg-[#a95e1d] disabled:opacity-50 flex items-center gap-2"
            >
                <template x-if="loading">
                    <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </template>
                <span x-text="loading ? 'Processing...' : 'Confirm'"></span>
            </button>
        </div>
    </div>
</div>
