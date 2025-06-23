{{-- Job Listing Display --}}
<div class="mt-10 grid gap-6 sm:grid-cols-1 md:grid-cols-2">
    @forelse($jobs as $job)
        <div class="bg-white border rounded-lg shadow-sm p-6 relative flex flex-col justify-between">
            {{-- Header: Job Title + Company + Actions --}}
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h4 class="text-lg font-semibold text-[#BD6F22]">{{ $job->job_title }}</h4>
                    <p class="text-gray-700 text-sm">{{ $job->company_name }}</p>
                </div>

                {{-- Edit & Delete Actions --}}
                <div class="flex gap-2">
                    {{-- Edit Button --}}
                    <button 
                        @click="$dispatch('open-job-modal', {{ json_encode($job) }})" 
                        class="text-gray-500 hover:text-[#BD6F22] p-1"
                        title="Edit Job"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.232 5.232l3.536 3.536M9 11l6-6m2 2L9 17H7v-2l10-10z" />
                        </svg>
                    </button>

                    {{-- Delete Button --}}
                   <form action="{{ route('hrAdmin.jobPosting.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this job posting?');">

                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-500 hover:text-red-600 p-1" title="Delete Job">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m4-4h2a1 1 0 011 1v1H8V4a1 1 0 011-1z" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Qualifications --}}
            <div class="flex items-start text-sm text-gray-600 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 mt-1 text-[#BD6F22]" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
                </svg>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 22s8-4.5 8-10a8 8 0 10-16 0c0 5.5 8 10 8 10z" />
                </svg>
                {{ $job->location }}
            </div>

            {{-- Timestamps --}}
            <div class="flex justify-between items-center text-sm text-gray-500">
                <p>Last Posted: {{ $job->created_at->diffForHumans() }}</p>
                <p>Apply until: {{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</p>
            </div>

            {{-- See More --}}
            <div class="text-right mt-3">
                <a href="{{ route('hrAdmin.jobPosting.show', $job->id) }}"

                     class="text-[#BD6F22] hover:underline text-sm">    
                    See More
                </a>
            </div>
        </div>
    @empty
        <p class="text-center text-gray-500">No job postings available.</p>
    @endforelse
</div>
