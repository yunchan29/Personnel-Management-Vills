<div x-data="applicantsHandler()" x-init="init(); pageContext = 'interview'; showAll = true" class="relative">
  <div x-data="interviewHandler($data)">
    <!-- Applicants Table -->
    <div class="overflow-x-auto relative bg-white p-6 rounded-lg shadow-lg w-full">
      
    <!-- Master Checkbox + Mass Interview Buttons -->
    <div x-show="selectedApplicants.length > 0"
         x-transition
         class="flex flex-wrap items-center gap-4 mb-4">
        <!-- Master Checkbox -->
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input
                type="checkbox"
                x-ref="masterCheckbox"
                @change="toggleSelectAll($event)"
                class="rounded border-gray-300"
            >
            <span>Select All</span>
        </label>
      <!-- Mass Interview Buttons -->

    <!-- Schedule Interview Button -->
    <button
        @click="openBulk('bulk')"
        class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2
               hover:text-[#8B4513] transition-colors duration-150
               focus:outline-none">
        <!-- Lucide: Calendar -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
          <line x1="16" y1="2" x2="16" y2="6"></line>
          <line x1="8" y1="2" x2="8" y2="6"></line>
          <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        <span class="text-sm" x-text="`Schedule Interview (${selectedApplicants.length})`"></span>
    </button>

    <!-- Reschedule Interview Button -->
    <button
        @click="openBulk('bulk-reschedule')"
        class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2
               hover:text-[#8B4513] transition-colors duration-150
               focus:outline-none">
        <!-- Lucide: Refresh-Ccw -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <path d="M3 2v6h6"></path>
          <path d="M21 12a9 9 0 0 0-9-9H9"></path>
          <path d="M21 22v-6h-6"></path>
          <path d="M3 12a9 9 0 0 0 9 9h3"></path>
        </svg>
        <span class="text-sm" x-text="`Reschedule Interview (${selectedApplicants.length})`"></span>
    </button>

    <!-- Manage Results Button -->
    <button
        @click="openBulkManage"
        class="min-w-[160px] text-gray-700 px-4 py-2 flex items-center justify-center gap-2
               hover:text-[#8B4513] transition-colors duration-150
               focus:outline-none">
        <!-- Lucide: Settings -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5"
             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="3"></circle>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33
                   1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51
                   1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06
                   a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09
                   a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06
                   a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65
                   1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09c0 .66.39 1.26 1 1.51
                   .46.2.99.2 1.45 0h.05a2 2 0 0 1 2.83 2.83l-.06.06
                   c-.39.39-.51.96-.33 1.48.18.52.66.91 1.22.91H21a2 2 0 0 1 0 4h-.09
                   a1.65 1.65 0 0 0-1.51 1z"></path>
        </svg>
        <span class="text-sm" x-text="`Manage Results (${selectedApplicants.length})`"></span>
    </button>
</div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
        <thead class="border-b font-semibold bg-gray-50">
          <tr>
            
            <th class="py-3 px-4"></th>
            <th class="py-3 px-4">Name</th>
            <th class="py-3 px-4">Position</th>
            <th class="py-3 px-4">Company</th>
            <th class="py-3 px-4">Interview Schedule</th>
            <th class="py-3 px-4">Resume</th>
            <th class="py-3 px-4">Profile</th>
            <th class="py-3 px-4">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $application)
            {{-- Controller already filters by approved/for_interview statuses --}}
            <tr
              data-applicant-id="{{ $application->id }}"
              data-interview-start="{{ optional($application->interview)?->start_date ? \Carbon\Carbon::parse($application->interview->start_date)->format('Y-m-d H:i') : '' }}"
              data-interview-end="{{ optional($application->interview)?->end_date ? \Carbon\Carbon::parse($application->interview->end_date)->format('Y-m-d H:i') : '' }}"
              data-status="{{ $application->status->value }}"
              x-show="(showAll || '{{ optional($application->interview)?->scheduled_at }}' === '')
                      && !removedApplicants.includes({{ $application->id }})"
              class="border-b hover:bg-gray-50 transition-opacity duration-300 ease-in-out">

             
          <td class="py-3 px-4">
             @if ($application->status->value !== 'interviewed')
          <label class="relative inline-flex items-center cursor-pointer">
                    <input 
                  type="checkbox"
                  class="applicant-checkbox"
                  :value="JSON.stringify({
                      application_id: {{ $application->id }},
                      user_id: {{ $application->user_id }},
                      name: '{{ $application->user->first_name }} {{ $application->user->last_name }}',
                      has_schedule: {{ $application->interview ? 'true' : 'false' }},
                  })"
                  :checked="selectedApplicants.some(a => a.application_id === {{ $application->id }})"
                 @change="toggleItem($event, {{ $application->id }}); updateMasterCheckbox()"
              >
            <!-- Custom checkmark -->
            <svg class="absolute left-0.5 top-0.5 hidden peer-checked:block w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
              <path d="M5 13l4 4L19 7" />
            </svg>
          </label>
          @endif
        </td>

            
              <!-- Name -->
              <td class="py-3 px-4 font-medium whitespace-nowrap">
                {{ $application->user->first_name }} {{ $application->user->last_name }}
              </td>

              <!-- Position -->
              <td class="py-3 px-4 whitespace-nowrap">{{ $application->job->job_title ?? 'N/A' }}</td>

              <!-- Company -->
              <td class="py-3 px-4 whitespace-nowrap">{{ $application->job->company_name ?? 'N/A' }}</td>

            <!-- Interview Schedule -->
            <td class="py-3 px-4 whitespace-nowrap">
            @if(optional($application->interview)?->scheduled_at)
            {{ \Carbon\Carbon::parse($application->interview->scheduled_at)->format('M d, Y h:i A') }}
            @else
            Not Set
            @endif
            </td>
              <!-- Resume -->
              <td class="py-3 px-4">
                  @if($application->resume_snapshot)
                      <button
                          @click="openResume('{{ asset('storage/' . $application->resume_snapshot) }}')"
                          class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                          View
                      </button>
                  @elseif($application->user->resume && $application->user->resume->resume)
                      <button
                          @click="openResume('{{ asset('storage/' . $application->user->resume->resume) }}')"
                          class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                          View
                      </button>
                  @else
                      <span class="text-gray-500 italic">None</span>
                  @endif
              </td>

              <!-- Profile -->
              <td class="py-3 px-4">
                  @if($application->user->active_status === 'Active')
                      <button
                          @click="openProfile({{ $application->user->id }})"
                          class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                          View
                      </button>
                  @else
                      <span class="text-gray-400 italic">Inactive</span>
                  @endif
              </td>

              <!-- Status -->
              <td class="py-3 px-4">
                  <span class="text-xs px-2 py-1 rounded-full transition-colors duration-300 whitespace-nowrap {{ $application->status_badge_class }}">
                      {{ $application->status_label }}
                  </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="py-6 text-center text-gray-500">No applicants yet.</td>
            </tr>
          @endforelse
        </tbody>
        </table>
      </div>
    </div>

    <!-- Feedback Toast -->
    <x-shared.feedbackToast />

    <!-- Modals -->
    @include('components.hrAdmin.modals.resume')
    @include('components.hrAdmin.modals.setInterview')
    @include('components.hrAdmin.modals.statusConfirmation')

    @foreach ($applications as $application)
      @include('components.hrAdmin.modals.profile', ['user' => $application->user])
    @endforeach

    <!-- Filter Toggle -->
    <div class="flex justify-center mb-4">
        <button
            @click="showAll = !showAll"
            class="px-4 py-2 text-gray-700 text-sm underline underline-offset-4 hover:text-[#8B4513] transition-colors duration-150">
            <span x-text="showAll ? 'Show Only Pending Interviews' : 'Show All Interviews'"></span>
        </button>
    </div>
  </div>
</div>

<!-- Handlers -->
<script src="{{ asset('js/utils/checkboxUtils.js') }}"></script>
<script src="{{ asset('js/utils/timeUtils.js') }}"></script>
<script src="{{ asset('js/applicantsHandler.js') }}"></script>
<script src="{{ asset('js/interviewHandler.js') }}"></script>

<!-- Styles -->
<style>
[x-cloak] { display: none !important; }
.animate-checkmark { animation: checkmark 0.3s ease-in-out; }
@keyframes checkmark {
  from { transform: scale(0.8) rotate(-20deg); opacity: 0; }
  to { transform: scale(1) rotate(0); opacity: 1; }
}
.animate-progress-bar {
  animation: progress 3s linear forwards;
}
@keyframes progress {
  from { width: 100%; }
  to { width: 0%; }
}
</style>
