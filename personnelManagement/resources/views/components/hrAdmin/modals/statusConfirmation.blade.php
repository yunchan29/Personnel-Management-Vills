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
                                :disabled="loading" 
                                class="px-4 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <template x-if="loading && statusAction === 'interviewed'">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </template>
                                <span x-text="loading && statusAction === 'interviewed' ? 'Processing...' : 'Pass'"></span>
                            </button>

                            <button 
                                @click="statusAction = 'fail_interview'; submitStatusChange()" 
                                :disabled="loading" 
                                class="px-4 py-2 text-sm rounded bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <template x-if="loading && statusAction === 'fail_interview'">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </template>
                                <span x-text="loading && statusAction === 'fail_interview' ? 'Processing...' : 'Fail'"></span>
                            </button>
                        </div>
                    </template>

                    <!-- Approve / Decline for Applicants Page -->
                    <template x-if="pageContext !== 'interview'">
                        <div class="flex gap-3">
                            <button 
                                @click="statusAction = 'approved'; submitStatusChange()" 
                                :disabled="loading" 
                                class="px-4 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <template x-if="loading && statusAction === 'approved'">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </template>
                                <span x-text="loading && statusAction === 'approved' ? 'Processing...' : 'Approve'"></span>
                            </button>

                            <button 
                                @click="statusAction = 'declined'; submitStatusChange()" 
                                :disabled="loading" 
                                class="px-4 py-2 text-sm rounded bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <template x-if="loading && statusAction === 'declined'">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </template>
                                <span x-text="loading && statusAction === 'declined' ? 'Processing...' : 'Disapprove'"></span>
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<!-- Mass Status Modal -->
<div x-show="showBulkStatusModal"
     x-transition
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     x-cloak>
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative">
        <!-- Close Button -->
        <button @click="showBulkStatusModal = false"
                class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>

        <!-- Header -->
        <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">Mass Manage Applicants</h2>

        <!-- Message -->
        <p class="mb-6 text-sm text-gray-700">
            You are about to update the status of 
            <span class="font-semibold text-[#BD6F22]" x-text="selectedApplicants.length"></span> applicants.
        </p>

        <!-- Buttons -->
        <div class="flex justify-end gap-3">
            <!-- Cancel -->
            <button @click="showBulkStatusModal = false"
                    class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">
                Cancel
            </button>

            <!-- Pass -->
            <button 
                @click="bulkStatusAction = 'interviewed'; submitBulkStatusChange()" 
                :disabled="loading" 
                class="px-4 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <svg x-show="loading && bulkStatusAction === 'interviewed'" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span x-text="loading && bulkStatusAction === 'interviewed' ? 'Processing...' : 'Pass All'"></span>
            </button>

            <!-- Fail -->
            <button 
                @click="bulkStatusAction = 'declined'; submitBulkStatusChange()" 
                :disabled="loading" 
                class="px-4 py-2 text-sm rounded bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <svg x-show="loading && bulkStatusAction === 'declined'" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span x-text="loading && bulkStatusAction === 'declined' ? 'Processing...' : 'Fail All'"></span>
            </button>

        </div>
    </div>
</div>
