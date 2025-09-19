<!-- Requirements Modal -->
<div>
    <div 
        x-show="requirementsOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        @click.self="closeRequirements()"
    >
        <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6 relative">
            <!-- Close button -->
            <button 
                @click="closeRequirements()" 
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
            >
                ✕
            </button>

            <!-- Header -->
            <h2 class="text-xl font-bold text-gray-700 mb-4">
                Requirements for <span x-text="requirementsApplicantName"></span>
            </h2>

            <!-- File201 details -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600">SSS Number</label>
                    <p class="text-gray-800" x-text="requirementsFile201?.sss_number ?? '—'"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">PhilHealth Number</label>
                    <p class="text-gray-800" x-text="requirementsFile201?.philhealth_number ?? '—'"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Pag-IBIG Number</label>
                    <p class="text-gray-800" x-text="requirementsFile201?.pagibig_number ?? '—'"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">TIN ID Number</label>
                    <p class="text-gray-800" x-text="requirementsFile201?.tin_id_number ?? '—'"></p>
                </div>
            </div>

            <!-- Licenses -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-2">Licenses</h3>
                <template x-if="requirementsFile201?.licenses?.length > 0">
                    <ul class="list-disc pl-5 space-y-1 text-gray-800">
                        <template x-for="license in requirementsFile201.licenses" :key="license.number">
                            <li>
                                <span class="font-medium" x-text="license.name"></span>
                                - <span x-text="license.number"></span>
                                (<span x-text="license.date"></span>)
                            </li>
                        </template>
                    </ul>
                </template>
                <p 
                    x-show="!requirementsFile201?.licenses || requirementsFile201.licenses.length === 0" 
                    class="text-gray-500 italic"
                >
                    No licenses uploaded
                </p>
            </div>

            <!-- Additional Documents -->
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Additional Documents</h3>
                <template x-if="requirementsOtherFiles.length > 0">
                    <ul class="divide-y divide-gray-200">
                        <template x-for="file in requirementsOtherFiles" :key="file.id">
                            <li class="py-2 flex justify-between items-center">
                                <span class="text-gray-700" x-text="file.type"></span>
                                <a 
                                    :href="'/storage/' + file.file_path" 
                                    target="_blank"
                                    class="text-blue-600 hover:underline text-sm"
                                >
                                    View / Download
                                </a>
                            </li>
                        </template>
                    </ul>
                </template>
                <p 
                    x-show="requirementsOtherFiles.length === 0" 
                    class="text-gray-500 italic"
                >
                    No additional documents uploaded
                </p>
            </div>
        </div>
    </div>
</div>
