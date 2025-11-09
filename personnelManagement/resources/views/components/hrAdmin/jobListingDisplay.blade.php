{{-- Job Listing Display --}}
<div 
    x-data="{ expandedId: null }" 
    class="mt-10 grid gap-6 sm:grid-cols-1 md:grid-cols-2 items-start"
>
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
            {{-- Expired Label --}}
            @if($isExpired)
                <span class="absolute top-2 right-2 bg-red-200 text-red-800 text-xs px-2 py-1 rounded-full">
                    Expired
                </span>
            @endif

            {{-- Header --}}
            <div class="flex justify-between items-start mb-4">
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

            {{-- Vacancies --}}
            <div
                class="flex items-start text-sm text-gray-600 mb-2"
                x-show="expanded"
                x-transition
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <div>
                    <strong>Vacancies:</strong> {{ $job->vacancies }}
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
        </div>
    @empty
        <p class="text-center text-gray-500 col-span-full">No job postings available.</p>
    @endforelse
</div>
