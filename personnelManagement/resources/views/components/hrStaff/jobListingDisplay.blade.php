@props(['job'])

<div 
    x-data="{
        expanded: false,
        jobId: {{ $job->id }},
        jobTitle: {{ Js::from($job->title) }},
        company: {{ Js::from($job->company) }}
    }"
    class="bg-white border rounded-lg shadow-sm p-6 flex flex-col justify-between"
>
    {{-- Header --}}
    <div class="flex justify-between items-start mb-4">
        <div>
            <h4 class="text-lg font-semibold text-[#BD6F22]">{{ $job->title }}</h4>
            <p class="text-sm text-gray-700">{{ $job->company }}</p>
        </div>
        <div class="text-right text-xs text-gray-500 leading-tight">
            <p>Last Posted: {{ $job->created_at->diffForHumans() }}</p>
            @if($job->apply_until)
                <p>Apply until: {{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</p>
            @endif
        </div>
    </div>

    {{-- Qualifications --}}
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

    {{-- Additional Info --}}
    @if(!empty($job->additional_info))
        <div class="flex items-start text-sm text-gray-600 mb-3" x-show="expanded">
            <img src="{{ asset('images/info.png') }}" alt="Info" class="w-5 h-5 mr-2 mt-1">
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
    @if(count($job->qualifications) > 3 || !empty($job->additional_info))
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
                    $refs.evaluationSection?.scrollIntoView({ behavior: 'smooth' });
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
