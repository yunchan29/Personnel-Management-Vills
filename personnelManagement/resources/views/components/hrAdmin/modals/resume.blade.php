<!-- Resume Modal -->
<div x-show="showModal"
     x-transition
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     x-cloak>
    <div class="bg-white rounded-lg overflow-hidden w-[90%] max-w-3xl shadow-xl relative">
        <button @click="showModal = false"
                class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>
        <div class="p-6">
            <h2 class="text-lg font-semibold mb-4 text-[#BD6F22]">Resume Preview</h2>
            <iframe :src="resumeUrl" class="w-full h-[70vh] border rounded" frameborder="0"></iframe>
        </div>
    </div>
</div>
