<div x-show="showInterviewModal" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative">
        <!-- Close button -->
        <button @click="showInterviewModal = false"
            class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>

        <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">
            Set Interview for <span x-text="interviewApplicant?.name"></span>
        </h2>

        <!-- Date -->
        <label class="block text-sm font-medium mb-1">Interview Date</label>
        <input
            type="date"
            x-model="interviewDate"
            :min="new Date().toISOString().split('T')[0]"
            class="w-full mb-4 p-2 border rounded"
        />

         <!-- Time -->
         <label class="block text-sm font-medium mb-1">Interview Time</label>
         <div class="flex gap-2 mb-4">

         <!-- Hours -->
         <select x-model.number="interviewTime" class="flex-1 p-2 border rounded">
         <!-- Show 8–11 if AM, show 1–5 if PM -->
         <template x-for="h in (interviewPeriod === 'AM' ? [8,9,10,11] : [1,2,3,4,5])" :key="h">
         <option :value="h" x-text="h"></option>
         </template>
        </select>

        <!-- AM/PM -->

         <select x-model="interviewPeriod" class="w-24 p-2 border rounded">
          <option value="AM">AM</option>
          <option value="PM">PM</option>
         </select>


</div>

        <!-- Confirm button -->
        <div class="flex justify-end gap-3">
            <button @click="submitInterviewDate"
                :disabled="loading"
                class="px-4 py-2 text-sm rounded bg-[#BD6F22] text-white hover:bg-[#a95e1d] disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
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
