  {{-- Job Listing Display --}}
<div class="mt-10 space-y-6">
    @forelse($jobs as $job)
        <div class="bg-white border rounded-md shadow-sm p-6 relative">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h4 class="text-lg font-semibold text-[#BD6F22]">{{ $job->job_title }}</h4>
                    <p class="text-gray-700">{{ $job->company_name }}</p>
                </div>
                <a href="{{ route('hrAdmin.jobPosting.edit', $job->id) }}" class="text-gray-600 hover:text-[#BD6F22]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5h2m2 14H9m2-10h.01M5 13l4 4L19 7" />
                    </svg>
                </a>
            </div>

            <div class="flex items-start text-sm mb-2 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-1 text-[#BD6F22]" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2a4 4 0 00-4-4H4a4 4 0 100 8h1a4 4 0 004-4zm6 0v-2a4 4 0 014-4h1a4 4 0 110 8h-1a4 4 0 01-4-4z" />
                </svg>
                <div>
                    <strong>Qualification:</strong>
                    <ul class="list-disc ml-6">
                      @foreach($job->qualifications as $line)
    <li>{{ $line }}</li>
@endforeach

                    </ul>
                </div>
            </div>

            <div class="flex items-center text-sm text-gray-600 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#BD6F22]" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 12l4.243-4.243M6.343 7.343L10.586 12l-4.243 4.243" />
                </svg>
                {{ $job->location }}
            </div>

            <div class="flex justify-between items-center text-sm text-gray-500">
                <p>Last Posted: {{ $job->created_at->diffForHumans() }}</p>
                <p>Apply until: {{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</p>
            </div>

            <div class="text-right mt-2">
                <a href="{{ route('hrAdmin.jobPosting.show', $job->id) }}" class="text-sm text-[#BD6F22] hover:underline">
                    See More
                </a>
            </div>
        </div>
    @empty
        <p class="text-center text-gray-500">No job postings available.</p>
    @endforelse
</div>
