@props(['otherFiles' => collect()])

<div x-show="activeTab === 'files'" x-cloak>

    @if($otherFiles->isNotEmpty())
        <div class="mt-6">
            <h4 class="text-md font-semibold text-[#BD6F22] mb-3">Previously Uploaded Files</h4>
            
            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-md shadow-sm">
                <ul class="divide-y divide-gray-200 text-sm text-gray-800">
                    @foreach($otherFiles as $file)
                        <li class="py-3 px-4 flex justify-between items-center">
                            <a href="{{ asset('storage/' . $file->file_path) }}"
                            target="_blank"
                            class="text-[#BD6F22] hover:text-[#a75f1c] font-semibold hover:underline transition">
                                {{ $file->type }}
                            </a>

                        <button 
                     type="button"
                     onclick="confirmDelete({{ $file->id }}, '{{ $file->type }}')"
                     class="text-red-600 hover:text-red-800 text-sm font-bold ml-2">
                     &times;
                       </button>

                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <h3 class="text-lg font-semibold text-[#BD6F22] mb-4 mt-4 border-t pt-4">Submit a document</h3>

    <template x-for="(doc, index) in documents" :key="index">
        <div class="mb-6 border-b pb-4">
            <!-- Type of Document -->
            <label class="block text-sm font-semibold text-gray-800 mb-1">Type of Document</label>
            <select :name="'additional_documents['+index+'][type]'" 
             x-model="doc.type"
             class="w-full border border-gray-300 rounded-md px-3 py-2 mb-4">
           <option value="">-- Select Document Type --</option>
           <template x-for="option in documentTypes" :key="option">
           <option 
            :value="option"
            :disabled="usedTypes.includes(option) || documents.some((d, i) => i !== index && d.type === option)">
            <span x-text="option"></span>
           </option>
       </template>
       </select>


        <!-- File Input -->
           <div class="flex items-center gap-3 mb-3">
              <input type="file" 
                 :name="'additional_documents['+index+'][file]'" 
                   accept=".pdf"
                   class="flex-1 border border-gray-300 rounded-md px-3 py-2"
                  :disabled="!doc.type"
                 x-ref="'fileInput'+index"
                @change="if (!doc.type) { $refs['fileInput'+index].value = '' }" />
</div>


            <!-- Buttons -->
            <div class="flex justify-end gap-2">
                <template x-if="documents.length > 1">
                    <button type="button"
                            @click="removeDocument(index)"
                            class="text-red-600 hover:underline text-sm">
                        Remove
                    </button>
                </template>
                <template x-if="documents.length === 1">
                    <button type="button"
                            @click="doc.type = ''; $refs['fileInput'+index]?.value = ''"
                            class="text-blue-600 hover:underline text-sm"
                            x-ref="'fileInput' + index">
                        Clear
                    </button>
                </template>
            </div>
        </div>
    </template>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
   function confirmDelete(id, type) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This file will be permanently deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#BD6F22',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/employee/files/${id}`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();

            // 🔥 Update Alpine usedTypes so the deleted type becomes available again
            if (window.Alpine) {
                let root = document.querySelector('[x-data]');
                if (root && root.__x) {
                    root.__x.$data.usedTypes = root.__x.$data.usedTypes.filter(t => t !== type);
                }
            }
        }
    });
}

</script>

