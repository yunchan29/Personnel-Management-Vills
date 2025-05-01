@extends('layouts.applicantHome')

@section('content')
<section x-data="{ licenses: [{ name: '', number: '', date: '' }] }">
    <h2 class="text-xl font-semibold text-[#BD6F22] mb-4">My 201 files</h2>

    <div class="border-t border-gray-300 pt-4">
        <!-- Government Documents -->
        <h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Government Documents</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SSS number:</label>
                <input type="text" name="sss_number" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Philhealth number:</label>
                <input type="text" name="philhealth_number" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pag-Ibig number:</label>
                <input type="text" name="pagibig_number" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tin ID number:</label>
                <input type="text" name="tin_id_number" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
        </div>

        <!-- Licenses / Certifications -->
        <h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Licenses / Certifications</h3>

        <template x-for="(license, index) in licenses" :key="index">
            <div class="space-y-4 mb-6 border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-md font-semibold text-gray-700">
                        License / Certification #<span x-text="index + 1"></span>
                    </h4>
                    <button type="button" class="text-red-600 text-sm" x-show="licenses.length > 1" @click="licenses.splice(index, 1)">
                        Remove
                    </button>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">License / Certification</label>
                    <input type="text" :name="'licenses['+index+'][name]'" x-model="license.name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">License / Certification number</label>
                    <input type="text" :name="'licenses['+index+'][number]'" x-model="license.number" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Taken</label>
                    <input type="date" :name="'licenses['+index+'][date]'" x-model="license.date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                </div>
            </div>
        </template>

        <button type="button" @click="licenses.push({ name: '', number: '', date: '' })" class="bg-[#BD6F22] hover:bg-[#a75f1c] text-white font-semibold px-4 py-2 rounded-md">
            Add New
        </button>
    </div>
</section>


@endsection