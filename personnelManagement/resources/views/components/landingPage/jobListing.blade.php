 <!-- Job Listing Section -->
    <section class="w-full">
        <!-- Header -->
        <div class="bg-[#BD9168] w-full h-16 flex items-center justify-center animate-fadeIn">
            <h2 class="text-white text-3xl font-bold">Job Listing</h2>
        </div>

        <!-- Content Canvas -->
        <div class="p-6 bg-white animate-fadeIn delay-200">
            <!-- Search Bar -->
            <div class="flex items-center gap-4 mb-6">
               <form method="GET" action="{{ route('welcome') }}" class="flex items-center gap-4 mb-6">
    <label for="search" class="text-lg font-medium text-gray-700">Search Position</label>
    <input 
        type="text" 
        name="search"
        id="search" 
        placeholder="Enter job title..." 
        class="border border-gray-300 rounded-lg px-4 py-2 w-full max-w-md focus:outline-none focus:ring-2 focus:ring-[#BD9168]"
        value="{{ request('search') }}"
    />
    <button type="submit" class="bg-[#BD9168] text-white px-4 py-2 rounded-lg hover:bg-[#a37653] flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z" />
        </svg>
        Search
    </button>
</form>

            </div>

            <!-- Job cards will go here next -->
            <div class="p-6 bg-white">
                <!-- Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    @forelse($jobs as $job)
        <div class="flex items-start p-6 border rounded-lg shadow-sm bg-white gap-6 transform hover:scale-105 transition duration-300">
            <!-- Left Side -->
            <div class="flex flex-col items-start gap-4">
                <div class="text-gray-500 text-sm">
                    <p>Last Posted: <span class="font-medium">{{ $job->created_at->diffForHumans() }}</span></p>
                    <p>Apply until: <span class="font-medium">{{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</span></p>
                </div>
                <a href="{{ route('job.show', $job->id) }}" class="bg-[#BD9168] text-white px-6 py-2 rounded-md hover:bg-[#a37653] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                    Apply Now
                </a>
            </div>

            <!-- Right Side -->
            <div class="flex flex-col gap-2">
                <h3 class="text-[#BD9168] text-2xl font-bold">{{ $job->job_title }}</h3>
                <p class="text-black font-semibold">{{ $job->company_name }}</p>

                <div class="flex items-start gap-2 mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#BD9168] mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 01-8 0M12 14a8 8 0 00-8 8h16a8 8 0 00-8-8z" />
                    </svg>
                    <div>
                        <p class="font-semibold">Qualifications:</p>
                     @if (!empty($job->qualifications) && is_array($job->qualifications))
    <ul class="list-disc list-inside text-black">
        @foreach ($job->qualifications as $qual)
            <li>{{ $qual }}</li>
        @endforeach
    </ul>
@else
    <p>No qualifications listed.</p>
@endif


                    </div>
                </div>

                <div class="flex items-center gap-2 mt-4 text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#BD9168]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414m0 0L9.172 8.172m4.242 4.242A4 4 0 1116.657 7.343a4 4 0 01-4.243 4.243z" />
                    </svg>
                    <p>{{ $job->location }}</p>
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-500">No job listings available right now.</p>
    @endforelse
</div>


                          <!-- Pagination -->
<div class="mt-6">
   {{ $jobs->links('vendor.pagination.tailwind') }}

</div>
            </div>
        </div>
    </section>