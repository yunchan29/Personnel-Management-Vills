<!-- Status Modal -->
<div x-show="showStatusModal"
     x-transition
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     x-cloak>
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative">
        <!-- Close Button -->
        <button @click="showStatusModal = false"
                class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>

        <!-- Modal Header -->
        <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">
            <span x-text="pageContext === 'interview' ? 'Manage Interview Result' : 'Manage Application'"></span>
        </h2>

        <!-- Message -->
        <p class="mb-6 text-sm text-gray-700">
            What action would you like to take for 
            <span class="font-semibold text-[#BD6F22]" x-text="selectedApplicant?.name"></span>?
        </p>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 flex-wrap">
            <!-- Cancel Button -->
            <button @click="showStatusModal = false"
                    class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">
                Cancel
            </button>

            <!-- Already Passed -->
            <template x-if="selectedApplicant && selectedApplicant.status === 'interviewed' && pageContext === 'interview'">
                <button
                    class="px-4 py-2 text-sm rounded bg-green-600 text-white cursor-not-allowed opacity-70"
                    disabled>
                    Passed
                </button>
            </template>

            <!-- Dynamic Buttons -->
            <template x-if="selectedApplicant && selectedApplicant.status !== 'interviewed'">
                <div class="flex gap-3 flex-wrap">
                    <!-- Pass / Fail for Interview Page -->
                    <template x-if="pageContext === 'interview'">
                        <div class="flex gap-3">
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
                        </div>
                    </template>

                    <!-- Approve / Decline for Applicants Page -->
                    <template x-if="pageContext !== 'interview'">
                        <div class="flex gap-3">
                            <button
                                @click="statusAction = 'approved'; submitStatusChange()"
                                class="px-4 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700">
                                Approve
                            </button>
                            <button
                                @click="statusAction = 'declined'; submitStatusChange()"
                                class="px-4 py-2 text-sm rounded bg-red-600 text-white hover:bg-red-700">
                                Disapprove
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>
