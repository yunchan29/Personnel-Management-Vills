<style>
  @keyframes fadeSlideUp {
    0% {
      opacity: 0;
      transform: translateY(30px);
    }
    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .fade-slide-up {
    opacity: 0;
    transform: translateY(30px);
    transition: all 1s ease-out;
  }

  .fade-slide-up.visible {
    opacity: 1;
    transform: translateY(0);
  }
</style>

<div x-data="{ selectedJob: null, showModal: false }">

  <!-- Section Title -->
  <div class="mb-6 sm:mb-8 px-2">
    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Available Positions</h2>
    <p class="text-sm sm:text-base text-gray-600">Explore our current job openings and find your next career opportunity</p>
  </div>

  <!-- Cards Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8 px-2">
    @forelse($jobs as $job)
      <div class="job-card fade-slide-up flex flex-col sm:flex-row items-start p-4 sm:p-6 border rounded-lg shadow-sm bg-white gap-4 sm:gap-6 hover:shadow-md hover:-translate-x-2 transition-all duration-300">
        <!-- Top/Left Side -->
        <div class="flex flex-row sm:flex-col justify-between sm:justify-start w-full sm:w-auto sm:min-w-[160px] gap-4 text-gray-600">
          <div class="text-xs sm:text-sm">
            <p>Posted: <span class="font-semibold">{{ $job->created_at->diffForHumans() }}</span></p>
            <p>Deadline: <span class="font-semibold">{{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</span></p>
          </div>
          <!-- Apply Now (Card) -->
         <a href="{{ route('login') }}"
   class="w-28 sm:w-32 bg-[#BD9168] text-white text-xs sm:text-sm py-2 rounded-md hover:bg-[#a37653]
          flex items-center justify-center gap-1 sm:gap-2 transition text-center flex-shrink-0">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
  </svg>
  Apply Now
</a>

        </div>

        <!-- Bottom/Right Side -->
        <div class="flex-1 space-y-2 w-full">
          <h3 class="text-base sm:text-lg font-semibold text-[#BD9168]">{{ $job->job_title }}</h3>
          <p class="text-xs sm:text-sm font-medium text-gray-800">{{ $job->company_name }}</p>

          <div class="flex items-start gap-2 text-xs sm:text-sm text-gray-700 mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#BD9168] mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
  class="mt-2 text-xs sm:text-sm text-[#BD9168] hover:underline focus:outline-none font-medium">
  See More
</button>


        </div>
      </div>
    @empty
      <p class="text-gray-500 text-sm px-2">No job listings available right now.</p>
    @endforelse
  </div>

  <!-- Modal -->
<div x-show="showModal" x-transition x-cloak
     class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 px-4"
     @keydown.window.escape="showModal = false">

  <div
    x-ref="modal"
    class="bg-white rounded-lg shadow-lg p-4 sm:p-6 relative space-y-4 
           w-full max-w-sm sm:max-w-lg lg:max-w-2xl 
           max-h-[85vh] overflow-y-auto"
  >
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h2 class="text-lg sm:text-xl font-bold text-[#BD9168]" x-text="selectedJob?.job_title"></h2>
      <button @click="showModal = false"
              class="text-gray-500 hover:text-gray-700 text-2xl leading-none">
        &times;
      </button>
    </div>

    <!-- Company & Location -->
    <div>
      <p class="text-base font-medium text-gray-800" x-text="selectedJob?.company_name"></p>
      <p class="text-sm text-gray-600" x-text="'Location: ' + selectedJob?.location"></p>
    </div>

    <!-- Qualifications -->
    <div>
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

    <!-- Footer -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mt-4">
      <a href="{{ route('login') }}"
         class="inline-flex items-center bg-[#BD9168] text-white px-4 py-2 text-sm rounded-md hover:bg-[#a37653] transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
        Apply Now
      </a>

      <div class="flex flex-col text-sm text-gray-600">
        <p>Posted: <span class="font-semibold" x-text="selectedJob?.posted"></span></p>
        <p>Deadline: <span class="font-semibold" x-text="selectedJob?.deadline"></span></p>
      </div>
    </div>
  </div>
</div>

      </div>
    </div>
  </div>

</div>

<script>
  // Scroll animation for job cards - re-animates on scroll
  document.addEventListener('DOMContentLoaded', function() {
    const jobCards = document.querySelectorAll('.job-card');

    const cardObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // Add visible class when entering viewport
          entry.target.classList.add('visible');
        } else {
          // Remove visible class when leaving viewport
          entry.target.classList.remove('visible');
        }
      });
    }, {
      threshold: 0.2
    });

    jobCards.forEach(card => cardObserver.observe(card));
  });
</script>
