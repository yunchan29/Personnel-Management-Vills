@props([
    'jobId',
    'title',
    'company',
    'location',
    'qualifications',
    'addinfo',
    'vacancies',
    'lastPosted',
    'deadline',
    'hasResume',
    'hasApplied' => false,
    'hasTrainingOrPassed' => false,
])

<div 
    x-data="{
        showDetails: false,
        hasApplied: {{ $hasApplied ? 'true' : 'false' }},
        hasTrainingOrPassed: {{ $hasTrainingOrPassed ? 'true' : 'false' }},
        apply() {
            if (this.hasApplied || this.hasTrainingOrPassed) return; // Block if applied or training passed 

            Swal.fire({ 
                title: 'Are you sure?', 
                text: 'Do you want to apply for this job?', 
                icon: 'question', 
                showCancelButton: true, 
                confirmButtonColor: '#BD6F22', 
                cancelButtonColor: '#d33', 
                confirmButtonText: 'Yes, apply!' 
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('jobs.apply', $jobId) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire('Applied!', 'Your application has been submitted.', 'success')
                                .then(() => { this.hasApplied = true; });
                        } else {
                            Swal.fire('Failed!', 'Failed to apply. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error!', 'An error occurred.', 'error');
                    });
                }
            });
        },
    }" 
    class="w-full border rounded-lg shadow-sm p-6 bg-white space-y-4 transition-all duration-300 relative"
>

 <!-- Vacancies Badge -->
    <span class="absolute top-4 right-4 bg-[#BD6F22] text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
        {{ $vacancies }} Vacanc{{ $vacancies > 1 ? 'ies' : 'y' }}
    </span> 
    <!-- Top Row -->
    <div class="flex justify-between items-start gap-4 flex-wrap">
        <!-- Date Info -->
        <div class="text-sm text-gray-500" x-show="!showDetails">
            <p>Last Posted: {{ $lastPosted }}</p>
            <p>Apply until: {{ $deadline }}</p>
        </div>

        <!-- Job Info -->
        <div class="flex-1 min-w-[200px]">
            <h3 class="text-[#BD6F22] text-xl font-bold">{{ $title }}</h3>
            <p class="text-gray-800 font-semibold">{{ $company }}</p>

            <!-- Basic Qualifications -->
            <div class="flex items-start mt-2 gap-2" x-show="!showDetails">
                <img src="/images/briefcaseblack.png" class="w-5 h-5 mt-1" alt="Qualification">
                <ul class="text-sm text-gray-700 list-disc list-inside">
                    @php $limitedQualifications = collect($qualifications)->take(3); @endphp
                    @foreach ($limitedQualifications as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Location -->
            <div class="flex items-center mt-2 text-gray-700" x-show="!showDetails">
                <img src="/images/location.png" class="w-5 h-5 mr-1" alt="Location">
                <p>{{ $location }}</p>
            </div>
        </div>

   <!-- Apply Button and Date Info (expanded view) -->
<div x-show="showDetails" x-transition class="flex items-center gap-4 text-sm text-gray-500 self-end">
    <!-- Apply Button -->
     
        @if ($hasTrainingOrPassed) 
        <button 
        :disabled="hasTrainingOrPassed"
         @click.prevent="apply()"
         :class="hasTrainingOrPassed 
                ? 'bg-gray-400 cursor-not-allowed text-white' 
                : 'bg-[#BD6F22] hover:bg-[#a75d1c] text-white'"
           class="px-6 py-2 rounded-md flex items-center gap-2 text-sm font-medium transition">
            <img src="/images/mousepointer.png" class="w-4 h-4" alt="Apply" x-show="!hasTrainingOrPassed">
            <span x-text="hasTrainingOrPassed ? 'Apply' : 'Apply Now'"></span>
        </button>

        @elseif ($hasResume)
        <button 
            :disabled="hasApplied"
            @click.prevent="apply()"
            :class="hasApplied 
                ? 'bg-gray-400 cursor-not-allowed text-white' 
                : 'bg-[#BD6F22] hover:bg-[#a75d1c] text-white'"
            class="px-6 py-2 rounded-md flex items-center gap-2 text-sm font-medium transition">
            <img src="/images/mousepointer.png" class="w-4 h-4" alt="Apply" x-show="!hasApplied">
            <span x-text="hasApplied ? 'Applied' : 'Apply Now'"></span>
        </button>
    @else
        <button 
            @click="window.location.href = '{{ route('applicant.application') }}'" 
            class="bg-[#BD6F22] text-white px-6 py-2 rounded-md flex items-center gap-2 text-sm font-medium transition"
        >
            <img src="/images/leaveForm.png" class="w-4 h-4" alt="Apply">
            <span>Upload Resume</span>
        </button>
    @endif

</div>
</div>
  <!-- See More/See Less -->
    <div class="text-center" x-show="!showDetails">
        <button 
            @click="showDetails = !showDetails" 
            class="text-[#BD6F22] hover:text-[#a75d1c] text-sm font-semibold underline transition"
        >See More</button>
    </div>

    <!-- Expandable Section -->
    <div 
        x-show="showDetails"
        x-transition:enter="transition-all ease-out duration-300"
        x-transition:enter-start="opacity-0 max-h-0"
        x-transition:enter-end="opacity-100 max-h-screen"
        x-transition:leave="transition-all ease-in duration-300"
        x-transition:leave-start="opacity-100 max-h-screen"
        x-transition:leave-end="opacity-0 max-h-0"
        class="overflow-hidden text-gray-800 space-y-3 text-sm mt-4"
    >
        @if (!empty($qualifications))
        <div>
            <div class="flex items-start space-x-2">
                <img src="/images/briefcaseblack.png" class="w-5 h-5 mt-1" alt="Qualification">
                <p class="font-semibold">All Qualifications:</p>
            </div>
            <ul class="list-disc list-inside">
                @foreach ($qualifications as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="flex items-center mt-2 text-gray-700">
            <img src="/images/location.png" class="w-5 h-5 mr-1" alt="Location">
            <p>{{ $location }}</p>
        </div>

        @if(!empty($addinfo) && is_array($addinfo))
            <ul>
                @foreach($addinfo as $info)
                    <li>{{ $info }}</li>
                @endforeach
            </ul>
        @else
            <p>No additional info available.</p>
        @endif

        <!-- See Less button at bottom -->
        <div class="text-center mt-4">
            <button 
                @click="showDetails = !showDetails" 
                class="text-[#BD6F22] hover:text-[#a75d1c] text-sm font-semibold underline transition"
            >See Less</button>
        </div>
    </div>
</div>
