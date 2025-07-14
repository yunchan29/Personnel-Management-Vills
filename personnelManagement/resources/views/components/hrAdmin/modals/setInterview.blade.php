<div x-show="showInterviewModal" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative">
        <button @click="showInterviewModal = false"
            class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>

        <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">
            Set Interview for <span x-text="interviewApplicant?.name"></span>
        </h2>

        <label class="block text-sm font-medium mb-1">Interview Date</label>
        <input type="date" x-model="interviewDate" class="w-full mb-4 p-2 border rounded">

        <label class="block text-sm font-medium mb-1">Interview Time</label>
        <input type="time" x-model="interviewTime" class="w-full mb-4 p-2 border rounded">

        <div class="flex justify-end gap-3">
            <button @click="showInterviewModal = false"
                class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">Cancel</button>
            <button @click="submitInterviewDate"
                class="px-4 py-2 text-sm rounded bg-[#BD6F22] text-white hover:bg-[#a95e1d]">Confirm</button>
        </div>
    </div>
</div>
