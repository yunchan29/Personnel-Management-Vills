<div x-data="{ selectedJob: null, showModal: false }">

  <!-- Cards Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    @forelse($jobs as $job)
      <div class="flex items-start p-6 border rounded-lg shadow-sm bg-white gap-6 hover:shadow-md transition duration-300">
        <!-- Left Side -->
        <div class="flex flex-col gap-4 min-w-[160px] text-gray-600">
          <div class="text-sm">
            <p>Posted: <span class="font-semibold">{{ $job->created_at->diffForHumans() }}</span></p>
            <p>Deadline: <span class="font-semibold">{{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</span></p>
          </div>
          <!-- Apply Now (Card) -->
          <a href="{{ route('login') }}"
             class="bg-[#BD9168] text-white text-sm px-5 py-2 rounded-md hover:bg-[#a37653] flex items-center gap-2 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
            Apply Now
          </a>
        </div>

        <!-- Right Side -->
        <div class="flex-1 space-y-2">
          <h3 class="text-lg font-semibold text-[#BD9168]">{{ $job->job_title }}</h3>
          <p class="text-sm font-medium text-gray-800">{{ $job->company_name }}</p>

          <div class="flex items-start gap-2 text-sm text-gray-700 mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#BD9168] mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 12.414m0 0L9.172 8.172m4.242 4.242A4 4 0 1116.657 7.343a4 4 0 01-4.243 4.243z" />
            </svg>
            <p>{{ $job->location }}</p>
          </div>
   <!-- See more button -->
<button
  @click="
    selectedJob = {
      job_title: @js($job->job_title),
      company_name: @js($job->company_name),
      location: @js($job->location),
      qualifications: @js($job->qualifications ?? []),
      posted: @js($job->created_at->diffForHumans()),
      deadline: @js(\Carbon\Carbon::parse($job->apply_until)->format('F d, Y'))
    };
    showModal = true
  "
  class="mt-2 text-sm text-[#BD9168] hover:underline focus:outline-none font-medium">
  See More
</button>


        </div>
      </div>
    @empty
      <p class="text-gray-500 text-sm">No job listings available right now.</p>
    @endforelse
  </div>

  <!-- Modal -->
    <div x-show="showModal" x-transition x-cloak
     class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 px-4"
       @keydown.window.escape="showModal = false">
    <div
      x-ref="modal"
      x-init="/* Drag code remains same */"
      class="bg-white rounded-lg shadow-lg p-6 relative space-y-4 overflow-auto resize"
      style="min-width: 300px; min-height: 200px; max-width: 90vw; max-height: 90vh;"
    >

      <!-- Drag Handle -->
      <div class="modal-header cursor-move flex justify-between items-center mb-0">
        <h2 class="text-xl font-bold text-[#BD9168]" x-text="selectedJob?.job_title"></h2>
        <button @click="showModal = false"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">
          &times;
        </button>
      </div>

      <div>
        <p class="text-base font-medium text-gray-800" x-text="selectedJob?.company_name"></p>
       <p class="text-sm text-gray-600" x-text="'Location: ' + selectedJob?.location"></p>
      </div>

        <div class="mt-3">
          <p class="font-semibold text-sm text-gray-800">Qualifications:</p>
          <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 mt-1">
            <template x-for="qual in selectedJob?.qualifications ?? []" :key="qual">
              <li x-text="qual"></li>
            </template>
            <template x-if="!(selectedJob?.qualifications && selectedJob?.qualifications.length)">
              <li>No qualifications listed.</li>
            </template>
          </ul>
        </div>

        <!-- Modal "Apply Now" -->
          <div class="flex items-center justify-end gap-6 mt-4">
          <a href="{{ route('login') }}"
             class="inline-flex items-center bg-[#BD9168] text-white px-4 py-2 text-sm rounded-md hover:bg-[#a37653] transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
            Apply Now
          </a>
          <!-- Posted -->
 <div class="flex flex-col text-sm text-gray-600">
    <p>Posted: <span class="font-semibold" x-text="selectedJob?.posted"></span></p>
    <p>Deadline: <span class="font-semibold" x-text="selectedJob?.deadline"></span></p>
  </div>
      </div>
    </div>
  </div>

</div>
