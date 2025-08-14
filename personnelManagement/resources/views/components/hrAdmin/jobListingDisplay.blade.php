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
                    <img src="{{ asset('images/info.png') }}" alt="Info" class="w-5 h-5 mr-2 mt-1">
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
                <img src="{{ asset('images/tag.png') }}" alt="Role Type" class="w-5 h-5 mr-2 mt-1">
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
                <img src="{{ asset('images/industry.png') }}" alt="Industry" class="w-5 h-5 mr-2 mt-1">
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
                <img src="{{ asset('images/group.png') }}" alt="Vacancies" class="w-5 h-5 mr-2 mt-1">
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
