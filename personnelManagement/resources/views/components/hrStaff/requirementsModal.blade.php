<!-- Requirements Modal -->
    <div 
        x-show="requirementsOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
        @click.self="closeRequirements()"
        x-data="{
            requiredDocs: [
                'Barangay Clearance',
                'NBI Clearance',
                'Police Clearance',
                'Medical Certificate',
                'Birth Certificate'
            ],
            isSubmitted(doc) {
                return this.requirementsOtherFiles.some(f => f.type === doc);
            }
        }"
    >
        <!-- Modal wrapper with transition -->
        <div 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="bg-white rounded-lg shadow-xl w-full max-w-3xl sm:max-w-lg md:max-w-2xl lg:max-w-3xl flex flex-col max-h-[90vh]"
        >
            <!-- Scrollable content -->
            <div class="px-4 sm:px-6 py-6 overflow-y-auto">
                <!-- Close button -->
                <button 
                    @click="closeRequirements()" 
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
                >
                    ✕
                </button>

                <!-- Header -->
                <h2 class="text-lg sm:text-xl font-bold text-[#BD6F22] mb-4">
                    Requirements for <span x-text="requirementsApplicantName"></span>
                </h2>

                <!-- File201 details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg">
                    <template x-for="[label, value] in [
                        ['SSS Number', requirementsFile201?.sss_number ?? '—'],
                        ['PhilHealth Number', requirementsFile201?.philhealth_number ?? '—'],
                        ['Pag-IBIG Number', requirementsFile201?.pagibig_number ?? '—'],
                        ['TIN ID Number', requirementsFile201?.tin_id_number ?? '—']
                    ]" :key="label">
                        <div>
                            <label class="block text-sm font-medium text-gray-600" x-text="label"></label>
                            <p class="text-gray-800 font-medium break-words" x-text="value"></p>
                        </div>
                    </template>
                </div>
                <!-- Required Documents -->
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-[#BD6F22] mb-4">Required Documents</h3>
                    <ul class="space-y-3">
                        <template x-for="doc in requiredDocs" :key="doc">
                            <li 
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 rounded-lg border transition text-sm sm:text-base"
                                :class="isSubmitted(doc) 
                                    ? 'border-green-300 bg-green-50 hover:bg-green-100' 
                                    : 'border-red-300 bg-red-50 hover:bg-red-100'"
                            >
                                <!-- Document name + status -->
                                <div class="flex flex-col mb-2 sm:mb-0">
                                    <span 
                                        x-text="doc"
                                        class="font-medium"
                                        :class="isSubmitted(doc) 
                                            ? 'text-green-700' 
                                            : 'text-red-600'"
                                    ></span>
                                    <span 
                                        class="text-xs mt-1"
                                        :class="isSubmitted(doc) 
                                            ? 'text-green-600 font-semibold' 
                                            : 'text-red-500 italic'"
                                        x-text="isSubmitted(doc) ? 'Submitted' : 'Missing'">
                                    </span>
                                </div>

                                <!-- Action -->
                                <template x-if="isSubmitted(doc)">
                                    <a 
                                        :href="'/storage/' + (requirementsOtherFiles.find(f => f.type === doc)?.file_path)" 
                                        target="_blank"
                                        class="text-sm font-medium text-blue-600 hover:underline"
                                    >
                                        View / Download
                                    </a>
                                </template>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Sticky Email Button -->
        <div 
            class="border-t bg-white px-4 py-3 flex justify-end sticky bottom-0"
            x-show="hasMissingRequirements()"
        >
            <button 
                type="button" 
                @click="sendEmailRequirements()"
                class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition"
            >
                Email Requirements
            </button>
        </div>
        </div>
    </div>

