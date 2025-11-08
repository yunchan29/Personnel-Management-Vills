@props(['modalId' => 'resumeModal', 'frameId' => 'resumeFrame'])

<!-- Resume Preview Modal -->
<div id="{{ $modalId }}" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg overflow-hidden w-full max-w-4xl h-[90%] flex flex-col">
        <div class="flex justify-between items-center px-4 py-2 border-b">
            <h2 class="text-lg font-semibold text-[#BD6F22]">Resume Preview</h2>
            <button onclick="closeResumeModal('{{ $modalId }}', '{{ $frameId }}')" class="text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
        </div>
        <iframe id="{{ $frameId }}" class="flex-1 w-full" style="border: none;"></iframe>
    </div>
</div>

<!-- Resume Modal Script -->
<script>
    function openResumeModal(fileUrl, modalId = '{{ $modalId }}', frameId = '{{ $frameId }}') {
        document.getElementById(frameId).src = fileUrl;
        document.getElementById(modalId).classList.remove('hidden');
        document.getElementById(modalId).classList.add('flex');
    }

    function closeResumeModal(modalId = '{{ $modalId }}', frameId = '{{ $frameId }}') {
        document.getElementById(modalId).classList.remove('flex');
        document.getElementById(modalId).classList.add('hidden');
        document.getElementById(frameId).src = '';
    }
</script>
