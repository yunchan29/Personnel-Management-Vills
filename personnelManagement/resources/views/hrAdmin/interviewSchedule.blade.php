<div x-data="applicantsHandler()" x-init="init(); pageContext = 'interview'" class="relative">

  <!-- Applicants Table -->
  <div class="overflow-x-auto relative bg-white p-6 rounded-lg shadow-lg">
    <!-- Mass Interview Button -->
    <div class="flex gap-2 mb-4">
        <!-- Mass Schedule -->
        <button 
            @click="openBulk('bulk')"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="selectedApplicants.length <= 1">
            Set Mass Interview
        </button>

        <!-- Mass Reschedule -->
        <button 
            @click="openBulk('bulk-reschedule')"
            class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="selectedApplicants.length <= 1">
            Mass Reschedule
        </button>

        <!-- Mass Manage -->
        <button 
            @click="openBulkStatusModal()"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="selectedApplicants.length <= 1">
            Mass Manage
        </button>
    </div>

    <table class="min-w-full text-sm text-left text-gray-700">
      <thead class="border-b font-semibold bg-gray-50">
        <tr>
<th class="py-3 px-4">
<input 
  type="checkbox" 
  x-ref="masterCheckbox"
  @change="toggleSelectAll($event)"
>

</th>


          <th class="py-3 px-4">Name</th>
          <th class="py-3 px-4">Position</th>
          <th class="py-3 px-4">Company</th>
          <th class="py-3 px-4">Interview Schedule</th>
          <th class="py-3 px-4">Resume</th>
          <th class="py-3 px-4">Profile</th>
          <th class="py-3 px-4">Status</th>
          <th class="py-3 px-4">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($applications as $application)
          <tr
            data-applicant-id="{{ $application->id }}"
            data-interview-start="{{ optional($application->interview)?->start_date ? \Carbon\Carbon::parse($application->interview->start_date)->format('Y-m-d H:i') : '' }}"
            data-interview-end="{{ optional($application->interview)?->end_date ? \Carbon\Carbon::parse($application->interview->end_date)->format('Y-m-d H:i') : '' }}"
            data-status="{{ $application->status }}"
            x-show="(['approved', 'for_interview', 'interviewed', 'declined'].includes('{{ $application->status }}')) 
                    && (showAll || '{{ optional($application->interview)?->scheduled_at }}' === '') 
                    && !removedApplicants.includes({{ $application->id }})"
            class="border-b hover:bg-gray-50 transition-opacity duration-300 ease-in-out">

<td class="py-3 px-4">
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
    :disabled="{{ $application->trainingSchedule ? 'true' : 'false' }}"
    @change="toggleItem($event, {{ $application->id }}); updateMasterCheckbox()"
  />
</td>



            <!-- Name -->
            <td class="py-3 px-4 font-medium whitespace-nowrap flex items-center gap-2">
              <span class="inline-block w-3 h-3 rounded-full {{ $application->user->active_status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
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
                @if($application->user->active_status === 'Active' && $application->resume_snapshot)
                    <button @click="openResume('{{ asset('storage/' . $application->resume_snapshot) }}')"
                        class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                        View
                    </button>
                @elseif($application->user->active_status === 'Inactive')
                    <span class="text-gray-400 italic">Inactive</span>
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
                <!-- Passed -->
                <template x-if="(applicants.find(a => a.id === {{ $application->id }})?.status || '{{ $application->status }}') === 'interviewed'">
                    <span class="text-xs bg-green-200 text-green-800 px-2 py-1 rounded-full transition-colors duration-300 whitespace-nowrap">
                        Passed
                    </span>
                </template>

                <!-- Failed -->
                <template x-if="(applicants.find(a => a.id === {{ $application->id }})?.status || '{{ $application->status }}') === 'declined'">
                    <span class="text-xs bg-red-200 text-red-800 px-2 py-1 rounded-full transition-colors duration-300 whitespace-nowrap">
                        Failed
                    </span>
                </template>

                <!-- For Interview -->
                <template x-if="(applicants.find(a => a.id === {{ $application->id }})?.status || '{{ $application->status }}') === 'for_interview'">
                    <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full transition-colors duration-300 whitespace-nowrap">
                        For Interview
                    </span>
                </template>

                <!-- Pending -->
                <template x-if="!['interviewed','declined','for_interview'].includes(applicants.find(a => a.id === {{ $application->id }})?.status || '{{ $application->status }}')">
                    <span class="text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded-full transition-colors duration-300 whitespace-nowrap">
                        Pending
                    </span>
                </template>
            </td>
            
            <!-- Action -->
            <td class="py-3 px-4">
                @if($application->user->active_status === 'Active')
                    <div class="flex gap-2">
                        <button
                            @click="openSetInterview(
                              {{ $application->id }},
                              '{{ $application->user->first_name }} {{ $application->user->last_name }}',
                              {{ $application->user_id }},
                              '{{ optional($application->interview)?->scheduled_at ? \Carbon\Carbon::parse($application->interview->scheduled_at)->format('Y-m-d H:i:s') : '' }}'
                            )"
                            class="bg-blue-600 text-white text-sm font-medium h-8 px-3 rounded hover:bg-blue-700 disabled:opacity-50"
                            :disabled="['interviewed','declined'].includes(applicants.find(a => a.id === {{ $application->id }})?.status)"
                        >
                          <span x-text="'{{ optional($application->interview)?->scheduled_at ? 'Reschedule' : 'Interview' }}'"></span>
                        </button>


                        <button
                            @click="openStatusModal({{ $application->id }}, '{{ $application->user->first_name }} {{ $application->user->last_name }}')"
                            class="bg-green-600 text-white text-sm font-medium h-8 px-3 rounded hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="(applicants.find(a => a.id === {{ $application->id }})?.status || '{{ $application->status }}') !== 'for_interview'">
                            Manage
                        </button>
                    </div>
                @else
                    <span class="text-gray-400 italic">Inactive</span>
                @endif
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

  <!-- Feedback Toast -->
  <div x-show="feedbackVisible"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 translate-y-4"
       x-transition:enter-end="opacity-100 translate-y-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100 translate-y-0"
       x-transition:leave-end="opacity-0 translate-y-4"
       class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-4 rounded-xl shadow-lg z-50 w-80 overflow-hidden"
       x-cloak>
      <div class="flex items-center gap-3">
        <svg class="w-6 h-6 text-white animate-checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span class="font-semibold text-sm" x-text="feedbackMessage"></span>
      </div>
      <div class="mt-3 h-1 w-full bg-white/20 rounded overflow-hidden">
        <div class="h-full bg-white animate-progress-bar"></div>
      </div>
  </div>

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
          class="px-4 py-2 bg-[#bd6f2200] text-black text-sm font-medium hover:text-[#a95e1d]">
          <span x-text="showAll ? 'Show Only Pending Interviews' : 'Show All Interviews'"></span>
      </button>
  </div>

</div>

<!-- Alpine + Handler -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="{{ asset('js/applicantsHandler.js') }}"></script>

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
