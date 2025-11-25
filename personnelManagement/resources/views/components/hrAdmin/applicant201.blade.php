<div class="border-t border-gray-300 pt-4">

    <!-- Government Documents -->
    <h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Government Documents</h3>

    @php
        $file201 = $user->file201;

        $sss = $file201->sss_number ?? '-';
        $philhealth = $file201->philhealth_number ?? '-';
        $pagibig = $file201->pagibig_number ?? '-';
        $tin = $file201->tin_id_number ?? '-';

        // File paths
        $sssFile = $file201->sss_file_path ?? null;
        $philhealthFile = $file201->philhealth_file_path ?? null;
        $pagibigFile = $file201->pagibig_file_path ?? null;
        $tinFile = $file201->tin_file_path ?? null;
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        
        <!-- SSS -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SSS number:</label>
                <input type="text" readonly value="{{ $sss }}"
                    class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />

                @if($sssFile)
                    <button 
                        onclick="openPreviewModal('{{ asset('storage/' . $sssFile) }}')"
                        class="text-green-600 text-sm underline hover:text-green-800">
                        View File
                    </button>
                @endif
            </div>
    

        <!-- PhilHealth -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">PhilHealth number:</label>
                <input type="text" readonly value="{{ $philhealth }}"
                    class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />

                @if($philhealthFile)
                    <button 
                        onclick="openPreviewModal('{{ asset('storage/' . $philhealthFile) }}')"
                        class="text-green-600 text-sm underline hover:text-green-800">
                        View File
                    </button>
                @endif    
            </div>

        <!-- Pag-Ibig -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pag-Ibig number:</label>
                <input type="text" readonly value="{{ $pagibig }}"
                    class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />

                @if($pagibigFile)
                    <button 
                        onclick="openPreviewModal('{{ asset('storage/' . $pagibigFile) }}')"
                        class="text-green-600 text-sm underline hover:text-green-800">
                        View File
                    </button>
                @endif
            </div>


        <!-- TIN -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">TIN ID number:</label>
                <input type="text" readonly value="{{ $tin }}"
                    class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />

                @if($tinFile)
                    <button 
                        onclick="openPreviewModal('{{ asset('storage/' . $tinFile) }}')"
                        class="text-green-600 text-sm underline hover:text-green-800">
                        View File
                    </button>
                @endif  
            </div>
        </div>

</div> <!-- FIXED: properly closed Government Documents container -->



<!-- File Preview Modal -->
<div id="filePreviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-3xl p-4 relative">

        <button onclick="closePreviewModal()" 
                class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl">
            &times;
        </button>

        <div id="filePreviewContent" class="w-full h-[70vh] flex justify-center items-center">
        </div>
    </div>
</div>



<!-- Licenses / Certifications -->
<h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Licenses / Certifications</h3>

@php
    $licenses = $file201->licenses ?? [];
@endphp

@forelse($licenses as $index => $license)
    <div class="border-t border-gray-200 py-4">
        <h4 class="text-md font-semibold text-gray-700 mb-2">
            License / Certification #{{ $index + 1 }}
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">License / Certification</label>
                <input type="text" readonly
                       value="{{ $license['name'] ?? '-' }}"
                       class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">License Number</label>
                <input type="text" readonly
                       value="{{ $license['number'] ?? '-' }}"
                       class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Taken</label>
                <input type="text" readonly
                       value="{{ !empty($license['date']) ? \Carbon\Carbon::parse($license['date'])->format('F d, Y') : '-' }}"
                       class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />
            </div>
        </div>
    </div>
@empty
    <p class="text-sm text-gray-500 italic">No licenses/certifications recorded.</p>
@endforelse


@if(count($licenses))
    <div class="mt-6">
        <h4 class="text-md font-semibold text-[#BD6F22] mb-2">Summary of License / Certification Names</h4>
        <ul class="list-disc list-inside text-sm text-gray-800">
            @foreach ($licenses as $license)
                @if(!empty($license['name']))
                    <li>{{ $license['name'] }}</li>
                @endif
            @endforeach
        </ul>
    </div>
@endif



<script>
function openPreviewModal(fileUrl) {
    const modal = document.getElementById('filePreviewModal');
    const content = document.getElementById('filePreviewContent');

    let previewHTML = '';

    if (fileUrl.endsWith('.pdf')) {
        previewHTML = `
            <iframe src="${fileUrl}" class="w-full h-full" frameborder="0"></iframe>
        `;
    } else {
        previewHTML = `
            <img src="${fileUrl}" class="max-h-full max-w-full rounded shadow">
        `;
    }

    content.innerHTML = previewHTML;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePreviewModal() {
    const modal = document.getElementById('filePreviewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
