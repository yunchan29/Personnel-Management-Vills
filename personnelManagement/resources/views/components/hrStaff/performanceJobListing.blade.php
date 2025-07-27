@props(['job', 'employeeCount'])

<div 
    x-data="{ expanded: false }"
    class="bg-white border rounded-lg shadow-sm p-6 flex flex-col justify-between"
>
    {{-- Header --}}
    <div class="flex justify-between items-start mb-4">
        <div>
            <h4 class="text-lg font-semibold text-[#BD6F22]">{{ $job->job_title }}</h4>
            <p class="text-sm text-gray-700">{{ $job->company }}</p>
        </div>
        <div class="text-right text-xs text-gray-500 leading-tight">
            <span>Employees:</span>
            <span class="bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                {{ $employeeCount }}
            </span>
        </div>
    </div>

    {{-- Qualifications --}}
    <div class="text-sm text-gray-800 mb-3">
        <p class="font-medium mb-1">Qualification:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($job->qualifications as $index => $qual)
                <li x-show="expanded || {{ $index }} < 3">{{ $qual }}</li>
            @endforeach
        </ul>
    </div>

    {{-- Expand Button --}}
    @if(count($job->qualifications) > 3)
        <div class="flex justify-center w-full mb-3">
            <button 
                @click="expanded = !expanded" 
                class="text-[#BD6F22] text-xs hover:underline"
            >
                <span x-text="expanded ? 'See Less' : 'See More'"></span>
            </button>
        </div>
    @endif

    {{-- Location & View --}}
    <div class="flex justify-between items-center text-sm text-gray-700 mt-3">
        <div class="flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#BD6F22]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C8.13401 2 5 5.13401 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.86599-3.134-7-7-7z" />
                <circle cx="12" cy="9" r="2.5" fill="currentColor" />
            </svg>
            <span>{{ $job->location ?? 'No location' }}</span>
        </div>
        <button 
            @click="$store.performance.tab = 'employees'; $store.performance.selectedJobId = {{ $job->id }}" 
            class="text-sm font-medium text-gray-500 hover:underline"
        >
            View Evaluation
        </button>
    </div>
</div>
