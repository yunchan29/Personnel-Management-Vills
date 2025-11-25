@extends(auth()->user()->role === 'applicant' ? 'layouts.applicantHome' : 'layouts.employeeHome')

@section('content')

@if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#BD6F22'
            });
        });
    </script>
@endif

@php
    $licensesData = old('licenses', optional($file201)->licenses ?: []);
@endphp

<form method="POST" action="{{ auth()->user()->role === 'applicant' ? route('applicant.files.store') : route('employee.files.store') }}" enctype="multipart/form-data"
x-data="{
    ...licenseForm({{ Js::from($licensesData) }}),
    activeTab: 'licenses',
    documents: [{ type: '', file: null }],
    documentTypes: [
        'Barangay Clearance',
        'NBI Clearance',
        'Police Clearance',
        'Medical Certificate',
        'Birth Certificate'
    ],
    @if(auth()->user()->role === 'applicant')
    usedTypes: {{ Js::from($otherFiles->pluck('type')) }},
    @endif
    addDocument() {
        @if(auth()->user()->role === 'applicant')
        if (this.documents.length < this.documentTypes.length) {
            this.documents.push({ type: '', file: null });
        }
        @else
        this.documents.push({ type: '', file: null });
        @endif
    },
    removeDocument(index) {
        this.documents.splice(index, 1);
    }
}"

      x-init="initLicenses()"
>
    @csrf

    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">My 201 files</h1>

   <!-- Government Documents -->
<div class="border-t border-gray-300 pt-4 mb-6">
    <h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Government Documents</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @php
        $governmentDocs = [
            'sss_number' => ['max' => 9, 'file' => 'sss_file_path'],
            'philhealth_number' => ['max' => 12, 'file' => 'philhealth_file_path'],
            'pagibig_number' => ['max' => 12, 'file' => 'pagibig_file_path'],
            'tin_id_number' => ['max' => 12, 'file' => 'tin_file_path'],
        ];

        @endphp

        @foreach($governmentDocs as $field => $data)
        <div class="relative">
            <!-- Number Input -->
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ ucwords(str_replace('_', ' ', $field)) }}:
            </label>

            <input
                type="text"
                name="{{ $field }}"
                value="{{ old($field, $file201->$field ?? '') }}"
                maxlength="{{ $data['max'] }}"
                inputmode="numeric"
                oninput="this.value = this.value.replace(/\D/g, '').slice(0,{{ $data['max'] }});"
                class="w-full border border-gray-300 rounded-md px-3 py-2
                    focus:outline-none focus:ring-2 focus:ring-[#BD6F22]"
            >

            <!-- Subtle Upload Button -->
            <label class="text-xs text-gray-500 mt-2 inline-flex items-center cursor-pointer hover:text-[#BD6F22] transition">
                <input
                    type="file"
                    name="{{ str_replace('_path','',$data['file']) }}"
                    accept="image/*,application/pdf"
                    class="hidden"
                    onchange="handleGovFileUpload(this)"
                >

                <span class="underline">Upload file</span>
            </label>

            <!-- Uploaded File Status -->
            @php $existing = $file201->{$data['file']} ?? null; @endphp

            <div class="mt-1 file-status text-xs flex items-center">
                @if($existing)
                    <button type="button"
                        onclick="previewGovFile('{{ asset('storage/'.$existing) }}')"
                        class="text-green-600 font-medium flex items-center hover:underline">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                        {{ basename($existing) }}
                    </button>
                @endif
            </div>

        </div>
        @endforeach
    </div>
</div>  


    <!-- Tabs -->
    <div class="mb-4">
        <div class="flex space-x-4 border-b pb-2">
            <button type="button"
                    @click="activeTab = 'licenses'"
                    :class="activeTab === 'licenses' ? 'text-[#BD6F22] border-b-2 border-[#BD6F22]' : 'text-gray-600'"
                    class="pb-1 text-sm font-semibold">
                Licenses / Certifications
            </button>
            <button type="button"
                    @click="activeTab = 'files'"
                    :class="activeTab === 'files' ? 'text-[#BD6F22] border-b-2 border-[#BD6F22]' : 'text-gray-600'"
                    class="pb-1 text-sm font-semibold">
                Additional Files
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div>
        <x-shared.licenses :licensesData="$licensesData" />
        <x-shared.other-files :otherFiles="$otherFiles" />
    </div>

    <!-- Final Save Button -->
    <div class="mt-6 text-right">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-md transition">
            Save
        </button>
    </div>
</form>

<script src="https://unpkg.com/lucide@latest"></script>

<script>
function handleGovFileUpload(input) {
    const wrapper = input.closest('div');
    const statusDiv = wrapper.querySelector('.file-status');

    const file = input.files[0];
    if (!file) return;

    statusDiv.innerHTML = `
    <button type="button"
        onclick="previewGovFile(URL.createObjectURL(file))"
        class="text-green-600 text-xs font-medium flex items-center hover:underline">
        <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
        ${file.name}
    </button>
`;

    lucide.createIcons();
}
</script>

<script>
function previewGovFile(url) {
    // Open PDF/image in a new tab
    window.open(url, '_blank');
}
</script>

<!-- Alpine Script -->
<script>
function licenseForm(initialLicenses = []) {
    return {
        licenses: [],
        selectedTab: 0,
        initLicenses() {
            this.licenses = initialLicenses.length
                ? initialLicenses
                : [{ name: '', number: '', date: '' }];
        },
        addLicense() {
            this.licenses.push({ name: '', number: '', date: '' });
            this.selectedTab = this.licenses.length - 1;
        },
        removeLicense(index) {
            this.licenses.splice(index, 1);
            if (this.selectedTab >= this.licenses.length) {
                this.selectedTab = this.licenses.length - 1;
            }
        },
        updateField() {}
    };
}
</script>
@endsection
