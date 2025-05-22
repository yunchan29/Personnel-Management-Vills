@extends('layouts.applicantHome')

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

<form method="POST" action="{{ route('file201.store') }}" x-data="licenseForm()" x-init="loadLicenses">
    @csrf
    <h2 class="text-xl font-semibold text-[#BD6F22] mb-4">My 201 files</h2>

    <div class="border-t border-gray-300 pt-4">
        <!-- Government Documents -->
        <h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Government Documents</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SSS number:</label>
                <input type="text" name="sss_number"
                    value="{{ old('sss_number', $file201->sss_number ?? '') }}"
                    maxlength="9"
                    pattern="\d{9}"
                    inputmode="numeric"
                    oninput="this.value = this.value.replace(/\D/g, '').slice(0,9);"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Philhealth number:</label>
                <input type="text" name="philhealth_number"
                    value="{{ old('philhealth_number', $file201->philhealth_number ?? '') }}"
                    maxlength="12"
                    pattern="\d{12}"
                    inputmode="numeric"
                    oninput="this.value = this.value.replace(/\D/g, '').slice(0,12);"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pag-Ibig number:</label>
                <input type="text" name="pagibig_number"
                    value="{{ old('pagibig_number', $file201->pagibig_number ?? '') }}"
                    maxlength="12"
                    pattern="\d{12}"
                    inputmode="numeric"
                    oninput="this.value = this.value.replace(/\D/g, '').slice(0,12);"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tin ID number:</label>
                <input type="text" name="tin_id_number"
                    value="{{ old('tin_id_number', $file201->tin_id_number ?? '') }}"
                    maxlength="12"
                    pattern="\d{9,12}"
                    inputmode="numeric"
                    oninput="this.value = this.value.replace(/\D/g, '').slice(0,12);"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
        </div>

        <!-- Licenses / Certifications -->
        <h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Licenses / Certifications</h3>

        <!-- Tab Navigation -->
        <div class="flex flex-wrap gap-2 mb-4">
            <template x-for="(license, index) in licenses" :key="index">
                <button type="button"
                    @click="selectedTab = index"
                    :class="selectedTab === index ? 'bg-[#BD6F22] text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                    License #<span x-text="index + 1"></span>
                </button>
            </template>
        </div>

        <!-- Slide-In License Form -->
        <div class="relative h-auto min-h-[320px] overflow-hidden">
            <template x-for="(license, index) in licenses" :key="index">
                <div x-show="selectedTab === index"
                    x-transition:enter="transition transform duration-500"
                    x-transition:enter-start="opacity-0 translate-x-full"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition transform duration-300 absolute inset-0"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-full"
                    class="space-y-4 border-t border-gray-200 pt-4 absolute inset-0 bg-white"
                    x-cloak>
                    <div class="flex items-center justify-between">
                        <h4 class="text-md font-semibold text-gray-700">
                            Editing License / Certification #<span x-text="index + 1"></span>
                        </h4>
                        <button type="button"
                            class="text-red-600 text-sm hover:underline"
                            x-show="licenses.length > 1"
                            @click="removeLicense(index)">
                            Remove
                        </button>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">License / Certification</label>
                        <input type="text"
                            :name="'licenses['+index+'][name]'"
                            x-model="license.name"
                            @input="updateField()"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">License / Certification number</label>
                        <input type="text"
                            :name="'licenses['+index+'][number]'"
                            x-model="license.number"
                            @input="updateField()"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Taken</label>
                        <input type="date"
                            :name="'licenses['+index+'][date]'"
                            x-model="license.date"
                            @input="updateField()"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                    </div>
                </div>
            </template>
        </div>

        <!-- License Summary -->
        <div class="mt-6">
            <h4 class="text-md font-semibold text-[#BD6F22] mb-2">Summary of License / Certification Names</h4>
            <ul class="list-disc list-inside text-sm text-gray-800" x-show="licenses.length > 0">
                <template x-for="(license, index) in licenses" :key="index">
                    <li x-text="license.name || 'Unnamed License/Certification #' + (index + 1)"></li>
                </template>
            </ul>
        </div>

        <!-- Add Button -->
        <button type="button"
            @click="addLicense()"
            class="mt-6 bg-[#BD6F22] hover:bg-[#a75f1c] text-white font-semibold px-4 py-2 rounded-md transition">
            Add New
        </button>

        <!-- Save Button -->
        <div class="mt-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-md transition">
                Save
            </button>
        </div>
    </div>
</form>

<!-- Alpine Component -->
<script>
    function licenseForm() {
        return {
            licenses: [],
            selectedTab: 0,

            loadLicenses() {
                const saved = localStorage.getItem('licensesData');
                this.licenses = saved ? JSON.parse(saved) : [{ name: '', number: '', date: '' }];
            },

            saveLicenses() {
                localStorage.setItem('licensesData', JSON.stringify(this.licenses));
            },

            updateField() {
                this.saveLicenses();
            },

            addLicense() {
                this.licenses.push({ name: '', number: '', date: '' });
                this.selectedTab = this.licenses.length - 1;
                this.saveLicenses();
            },

            removeLicense(index) {
                this.licenses.splice(index, 1);
                if (this.selectedTab >= this.licenses.length) this.selectedTab = this.licenses.length - 1;
                this.saveLicenses();
            }
        }
    }
</script>

<!-- Validation -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelector('form').addEventListener('submit', function (e) {
        const sss = document.querySelector('input[name="sss_number"]').value.trim();
        const philhealth = document.querySelector('input[name="philhealth_number"]').value.trim();
        const pagibig = document.querySelector('input[name="pagibig_number"]').value.trim();
        const tin = document.querySelector('input[name="tin_id_number"]').value.trim();

        const isDigits = /^\d+$/;

        if (!isDigits.test(sss) || sss.length !== 9) {
            e.preventDefault();
            return Swal.fire('Invalid SSS Number', 'SSS number must be exactly 9 digits.', 'error');
        }

        if (!isDigits.test(philhealth) || philhealth.length !== 12) {
            e.preventDefault();
            return Swal.fire('Invalid PhilHealth Number', 'PhilHealth number must be exactly 12 digits.', 'error');
        }

        if (!isDigits.test(pagibig) || pagibig.length !== 12) {
            e.preventDefault();
            return Swal.fire('Invalid Pag-IBIG Number', 'Pag-IBIG number must be exactly 12 digits.', 'error');
        }

        if (!isDigits.test(tin) || tin.length < 9 || tin.length > 12) {
            e.preventDefault();
            return Swal.fire('Invalid TIN', 'TIN must be between 9 and 12 digits.', 'error');
        }
    });
</script>

@endsection
