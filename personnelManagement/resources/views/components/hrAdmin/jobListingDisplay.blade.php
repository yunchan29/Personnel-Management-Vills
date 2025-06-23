{{-- Job Listing Display --}}
<div class="mt-10 grid gap-6 sm:grid-cols-1 md:grid-cols-2">
    @forelse($jobs as $job)
        <div class="bg-white border rounded-lg shadow-sm p-6 relative flex flex-col justify-between">
            {{-- Header: Job Title + Company --}}
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h4 class="text-lg font-semibold text-[#BD6F22]">{{ $job->job_title }}</h4>
                    <p class="text-gray-700 text-sm">{{ $job->company_name }}</p>
                </div>

                {{-- Edit Button --}}
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
                        @foreach($job->qualifications as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Location --}}
            <div class="flex items-center text-sm text-gray-600 mb-4">
                <img src="{{ asset('images/location.png') }}" alt="Location" class="w-5 h-5 mr-2">
                {{ $job->location }}
            </div>

            {{-- Timestamps --}}
            <div class="flex justify-between items-center text-sm text-gray-500">
                <p>Last Posted: {{ $job->created_at->diffForHumans() }}</p>
                <p>Apply until: {{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</p>
            </div>

            {{-- See More (temporarily hidden) --}}
            {{--
            <div class="text-right mt-3">
                <a href="{{ route('hrAdmin.jobPosting.show', $job->id) }}"
                   class="text-[#BD6F22] hover:underline text-sm">    
                    See More
                </a>
            </div>
            --}}
        </div>
    @empty
        <p class="text-center text-gray-500">No job postings available.</p>
    @endforelse
</div>
