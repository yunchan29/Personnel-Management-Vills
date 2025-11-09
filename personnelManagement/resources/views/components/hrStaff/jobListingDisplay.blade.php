@props(['job'])

<div 
    x-data="{
        expanded: false,
        jobId: {{ $job->id }},
        jobTitle: {{ Js::from($job->job_title) }},
        company: {{ Js::from($job->company_name) }}
    }"
    class="bg-white border rounded-lg shadow-sm p-6 flex flex-col justify-between"
>
    {{-- Header --}}
    <div class="flex justify-between items-start mb-4">
        <div>
            <h4 class="text-lg font-semibold text-[#BD6F22]">{{ $job->job_title }}</h4>
            <p class="text-sm text-gray-700">{{ $job->company_name }}</p>
        </div>
        <div class="text-right text-xs text-gray-500 leading-tight">
            <p>Last Posted: {{ $job->created_at->diffForHumans() }}</p>
            @if($job->apply_until)
                <p>Apply until: {{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</p>
            @endif
        </div>
    </div>

    {{-- Qualifications --}}
    @if(!empty($job->qualifications))
    <div class="flex items-start text-sm text-gray-600 mb-3">
        <img src="{{ asset('images/briefcaseblack.png') }}" alt="Qualifications" class="w-5 h-5 mr-2 mt-1">
        <div>
            <strong>Qualifications:</strong>
            <ul class="list-disc ml-6">
                @foreach($job->qualifications as $index => $qual)
                    <li x-show="expanded || {{ $index }} < 3">{{ $qual }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Additional Info --}}
    @if(!empty($job->additional_info))
        <div class="flex items-start text-sm text-gray-600 mb-3" x-show="expanded">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <strong>Additional Info:</strong>
                <ul class="list-disc ml-6">
                    @foreach($job->additional_info as $info)
                        <li>{{ $info }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Location --}}
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <img src="{{ asset('images/location.png') }}" alt="Location" class="w-5 h-5 mr-2">
        <span>{{ $job->location ?? 'No location specified' }}</span>
    </div>

    {{-- Expand Button --}}
    @if((is_array($job->qualifications) && count($job->qualifications) > 3) || !empty($job->additional_info))
        <div class="flex justify-center w-full mb-3">
            <button
                @click="expanded = !expanded"
                class="text-[#BD6F22] text-xs hover:underline"
            >
                <span x-text="expanded ? 'See Less' : 'See More'"></span>
            </button>
        </div>
    @endif

    {{-- Footer --}}
    <div class="flex justify-end items-center text-sm">
        <button 
            @click="
                selectedJobId = jobId;
                selectedJobTitle = jobTitle;
                selectedCompany = company;
                tab = 'evaluation';

                $nextTick(() => {
                    setTimeout(() => {
                        const target = $root.$refs.evaluationSection;
                        if (target?.scrollIntoView) {
                            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }, 300);
                });
            "
            type="button"
            class="text-[#BD6F22] hover:underline flex items-center gap-1 text-sm"
        >
            View
            <span class="bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                {{ $job->applications_count }}
            </span>
        </button>
    </div>
</div>
