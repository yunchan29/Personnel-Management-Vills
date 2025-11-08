@props(['licensesData'])

@php
    $licensesData = old('licenses', $licensesData);
@endphp

<div x-show="activeTab === 'licenses'" x-cloak>
    <!-- Tabs -->
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

    <!-- License Form -->
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
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">License / Certification number</label>
                    <input type="text"
                        :name="'licenses['+index+'][number]'"
                        x-model="license.number"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Taken</label>
                    <input type="date"
                        :name="'licenses['+index+'][date]'"
                        x-model="license.date"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                </div>
            </div>
        </template>
    </div>

    <!-- Summary -->
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
</div>
