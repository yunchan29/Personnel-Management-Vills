{{-- Job Listing Display --}}
<div
    x-data="{
        expandedId: null,
        selectedJobs: [],
        selectAll: false,
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedJobs = {{ $jobs->pluck('id')->toJson() }};
            } else {
                this.selectedJobs = [];
            }
        },
        get hasSelection() {
            return this.selectedJobs.length > 0;
        }
    }"
    class="mt-10"
>
    {{-- Bulk Actions Bar - Minimalist --}}
    <div x-show="hasSelection" x-transition class="mb-6 py-3 border-y border-gray-200">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Selection Count --}}
            <span class="text-sm text-gray-600">
                <span x-text="selectedJobs.length" class="font-medium text-gray-900"></span> selected
            </span>

            <div class="h-4 w-px bg-gray-300"></div>

            {{-- Quick Extend --}}
            <form method="POST" action="{{ route('hrAdmin.jobPosting.bulkExtend') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="job_ids" :value="JSON.stringify(selectedJobs)">
                <select name="days" class="text-sm border-0 border-b border-gray-300 py-1 px-0 focus:border-gray-900 focus:ring-0" required>
                    <option value="" disabled selected>Extend</option>
                    <option value="7">+7 days</option>
                    <option value="14">+14 days</option>
                    <option value="30">+30 days</option>
                    <option value="60">+60 days</option>
                </select>
            </form>

            {{-- Status Update --}}
            <form method="POST" action="{{ route('hrAdmin.jobPosting.bulkUpdateStatus') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="job_ids" :value="JSON.stringify(selectedJobs)">
                <select name="status" class="text-sm border-0 border-b border-gray-300 py-1 px-0 focus:border-gray-900 focus:ring-0" required>
                    <option value="" disabled selected>Status</option>
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                    <option value="filled">Filled</option>
                </select>
            </form>

            {{-- Delete --}}
            <form method="POST" action="{{ route('hrAdmin.jobPosting.bulkDelete') }}" onsubmit="return confirm('Delete selected jobs?')" class="inline">
                @csrf
                @method('DELETE')
                <input type="hidden" name="job_ids" :value="JSON.stringify(selectedJobs)">
                <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                    Delete
                </button>
            </form>

            <div class="ml-auto"></div>

            {{-- Clear --}}
            <button @click="selectedJobs = []; selectAll = false" class="text-sm text-gray-500 hover:text-gray-700">
                Clear
            </button>
        </div>
    </div>

    {{-- Select All Checkbox --}}
    <div class="mb-4 flex items-center gap-2">
        <input
            type="checkbox"
            x-model="selectAll"
            @change="toggleSelectAll()"
            class="rounded border-gray-300"
        >
        <label class="text-sm font-medium text-gray-700">Select All</label>
    </div>

    <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 items-start">
    @forelse($jobs as $job)
        @php
            $isExpired = \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($job->apply_until));
        @endphp
        <div
            x-data="{
                id: {{ $job->id }},
                qualifications: {{ Js::from($job->qualifications) }},
                additionalInfo: {{ Js::from($job->additional_info ?? []) }},
                expanded: false,
                showAll: false,
                init() {
                    this.$watch('expanded', value => {
                        if (value) {
                            this.$root.expandedId = this.id;
                        } else {
                            this.showAll = false;
                        }
                    });
                    this.$watch('$root.expandedId', value => {
                        this.expanded = value === this.id;
                    });
                }
            }"
            x-init="init"
            class="bg-white border rounded-lg shadow-sm p-6 flex flex-col justify-between transition-all duration-300 relative {{ $isExpired ? 'opacity-50' : '' }}"
        >
            {{-- Selection Checkbox --}}
            <div class="absolute top-2 left-2">
                <input
                    type="checkbox"
                    :value="{{ $job->id }}"
                    x-model="$root.selectedJobs"
                    class="rounded border-gray-300"
                >
            </div>

            {{-- Expired Label --}}
            @if($isExpired)
                <span class="absolute top-2 right-2 bg-red-200 text-red-800 text-xs px-2 py-1 rounded-full">
                    Expired
                </span>
            @elseif($job->status === 'filled')
                <span class="absolute top-2 right-2 bg-green-200 text-green-800 text-xs px-2 py-1 rounded-full">
                    Filled
                </span>
            @endif

            {{-- Header --}}
            <div class="flex justify-between items-start mb-4 mt-4">
                <div>
                    <h4 class="text-lg font-semibold text-[#BD6F22]">{{ $job->job_title }}</h4>
                    <p class="text-gray-700 text-sm">{{ $job->company_name }}</p>
                </div>
                <button
                    @click="$dispatch('open-job-modal', {{ json_encode($job) }})"
                    class="text-gray-500 hover:text-[#BD6F22] p-1"
                    title="Edit Job"
                >
                    <img src="{{ asset('images/edit.png') }}" alt="Edit" class="w-5 h-5">
                </button>
            </div>

            {{-- Qualifications --}}
            <div class="flex items-start text-sm text-gray-600 mb-3">
                <img src="{{ asset('images/briefcaseblack.png') }}" alt="Qualifications" class="w-5 h-5 mr-2 mt-1">
                <div>
                    <strong>Qualifications:</strong>
                    <ul class="list-disc ml-6">
                        <template x-for="(item, index) in qualifications" :key="index">
                            <li 
                                x-show="showAll || index < 3"
                                x-transition.duration.300ms 
                                x-text="item"
                            ></li>
                        </template>
                    </ul>
                </div>
            </div>

            {{-- Additional Info --}}
            <template x-if="showAll && additionalInfo.length">
                <div
                    class="flex items-start text-sm text-gray-600 mb-3"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <strong>Additional Info:</strong>
                        <ul class="list-disc ml-6">
                            <template x-for="(info, idx) in additionalInfo" :key="idx">
                                <li x-text="info"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </template>

            {{-- Role Type --}}
            <div
                class="flex items-start text-sm text-gray-600 mb-2"
                x-show="expanded"
                x-transition
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <div>
                    <strong>Role Type:</strong> {{ $job->role_type }}
                </div>
            </div>

            {{-- Job Industry --}}
            <div
                class="flex items-start text-sm text-gray-600 mb-2"
                x-show="expanded"
                x-transition
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <div>
                    <strong>Industry:</strong> {{ $job->job_industry }}
                </div>
            </div>

            {{-- Vacancies (remaining available positions) --}}
            <div
                class="flex items-start text-sm text-gray-600 mb-2"
                x-show="expanded"
                x-transition
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <div>
                    <strong>Available Vacancies:</strong> {{ $job->vacancies }}
                </div>
            </div>

            {{-- Location --}}
            <div class="flex items-center text-sm text-gray-600 mb-2">
                <img src="{{ asset('images/location.png') }}" alt="Location" class="w-5 h-5 mr-2">
                {{ $job->location }}
            </div>

            {{-- See More / See Less --}}
            <template x-if="qualifications.length > 3 || additionalInfo.length > 0">
                <div class="flex justify-center w-full">
                    <button 
                        @click="
                            if (!expanded) $root.expandedId = id;
                            showAll = !showAll;
                        " 
                        class="text-[#BD6F22] text-xs hover:underline mb-2"
                    >
                        <span x-text="showAll ? 'See Less' : 'See More'"></span>
                    </button>
                </div>
            </template>

            {{-- Timestamps --}}
            <div class="flex justify-between items-center text-sm text-gray-500 mt-auto pt-2 border-t border-gray-200">
                <p>Last Posted: {{ $job->created_at->diffForHumans() }}</p>
                <p>Apply until: {{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-200 items-center">
                {{-- Quick Extend Dropdown --}}
                <form method="POST" action="{{ route('hrAdmin.jobPosting.quickExtend', $job->id) }}" class="inline-flex items-center gap-1" x-data="{ days: '' }">
                    @csrf
                    <select
                        name="days"
                        x-model="days"
                        class="border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-green-500"
                        required
                    >
                        <option value="" disabled selected>Extend Duration</option>
                        <option value="7">+7 days</option>
                        <option value="14">+14 days</option>
                        <option value="30">+30 days</option>
                        <option value="60">+60 days</option>
                    </select>
                    <button
                        type="submit"
                        class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!days"
                    >
                        Apply
                    </button>
                </form>

                {{-- Repost Button (for expired or filled jobs) --}}
                @if($isExpired || $job->status === 'filled')
                    <form method="POST" action="{{ route('hrAdmin.jobPosting.repost', $job->id) }}" class="inline ml-auto">
                        @csrf
                        <button type="submit" class="bg-[#BD6F22] text-white px-3 py-1 rounded text-xs hover:bg-[#a65e1d]" title="Create a new posting with the same details">
                            Repost Job
                        </button>
                    </form>
                @endif

                {{-- Applications Count Badge --}}
                @if($job->applications_count > 0)
                    <span class="ml-auto bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                        {{ $job->applications_count }} application{{ $job->applications_count > 1 ? 's' : '' }}
                    </span>
                @endif
            </div>
        </div>
    @empty
        <p class="text-center text-gray-500 col-span-full">No job postings available.</p>
    @endforelse
</div>
