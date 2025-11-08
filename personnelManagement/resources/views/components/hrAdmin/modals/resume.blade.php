<!-- Resume Modal -->
<div x-show="showModal"
     x-transition
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     x-cloak>
    <div class="bg-white rounded-lg overflow-hidden w-full max-w-5xl h-[95vh] shadow-xl relative flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-[#8B4513]">Resume Preview</h2>
            <div class="flex items-center gap-2">
                <a :href="resumeUrl"
                   download
                   class="text-gray-700 hover:text-[#8B4513] text-sm px-3 py-1.5 transition-colors duration-150">
                    Download
                </a>
                <a :href="resumeUrl"
                   target="_blank"
                   class="text-gray-700 hover:text-[#8B4513] text-sm px-3 py-1.5 transition-colors duration-150">
                    Open in New Tab
                </a>
                <button @click="showModal = false"
                        class="text-gray-500 hover:text-red-500 text-2xl font-bold w-8 h-8 flex items-center justify-center">
                    &times;
                </button>
            </div>
        </div>

        <!-- PDF Viewer Container -->
        <div class="flex-1 overflow-hidden bg-gray-100">
            <object :data="resumeUrl"
                    type="application/pdf"
                    class="w-full h-full">
                <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <p class="text-gray-600 mb-4">Unable to display PDF in browser</p>
                    <div class="flex gap-3">
                        <a :href="resumeUrl"
                           download
                           class="bg-[#8B4513] text-white px-4 py-2 rounded hover:bg-[#6F3610] transition-colors duration-150">
                            Download PDF
                        </a>
                        <a :href="resumeUrl"
                           target="_blank"
                           class="border border-gray-300 text-gray-700 px-4 py-2 rounded hover:border-gray-900 hover:text-gray-900 transition-colors duration-150">
                            Open in New Tab
                        </a>
                    </div>
                </div>
            </object>
        </div>
    </div>
</div>
