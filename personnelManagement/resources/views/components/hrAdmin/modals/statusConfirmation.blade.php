<!-- Status Modal -->
<div x-show="showStatusModal"
     x-transition
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     x-cloak>
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative">
        <button @click="showStatusModal = false"
                class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>
        <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">
            @if (Request::is('hrAdmin/interviewSchedule*'))
                Manage Interview Result
            @else
                Manage Application
            @endif
        </h2>
        <p class="mb-6 text-sm text-gray-700">
            What action would you like to take for 
            <span class="font-semibold text-[#BD6F22]" x-text="selectedApplicant?.name"></span>?
        </p>

        <div class="flex justify-end gap-3 flex-wrap">
            <button @click="showStatusModal = false"
                    class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">
                Cancel
            </button>

            <!-- Already interviewed -->
            <template x-if="selectedApplicant && selectedApplicant.status === 'interviewed'">
                <button
                    class="px-4 py-2 text-sm rounded bg-green-600 text-white cursor-not-allowed opacity-70"
                    disabled>
                    Passed
                </button>
            </template>

            <!-- Dynamic action buttons -->
            <template x-if="selectedApplicant && selectedApplicant.status !== 'interviewed'">
                <div class="flex gap-3">
                    @if (Request::is('hrAdmin/interviewSchedule*'))
                        <button
                            @click="statusAction = 'interviewed'; submitStatusChange()"
                            class="px-4 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700">
                            Pass
                        </button>
                        <button
                            @click="statusAction = 'declined'; submitStatusChange()"
                            class="px-4 py-2 text-sm rounded bg-red-600 text-white hover:bg-red-700">
                            Fail
                        </button>
                    @else
                        <button
                            @click="statusAction = 'interviewed'; submitStatusChange()"
                            class="px-4 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700">
                            Approve
                        </button>
                        <button
                            @click="statusAction = 'declined'; submitStatusChange()"
                            class="px-4 py-2 text-sm rounded bg-red-600 text-white hover:bg-red-700">
                            Decline
                        </button>
                    @endif
                </div>
            </template>
        </div>
    </div>
</div>
