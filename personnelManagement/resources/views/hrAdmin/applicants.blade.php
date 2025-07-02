
<div x-data="applicantsHandler()" class="relative">
    <div class="flex justify-end mb-4">
    <button
        @click="showAll = !showAll"
        class="px-4 py-2 bg-[#BD6F22] text-white text-sm font-medium rounded shadow hover:bg-[#a95e1d]">
        <span x-text="showAll ? 'Show Only Pending Applicants' : 'Show All Applicants'"></span>
    </button>
</div>


    <!-- Applicants Table -->
    <div class="overflow-x-auto relative bg-white p-6 rounded-lg shadow-lg">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="border-b font-semibold bg-gray-50">
                <tr>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Position</th>
                    <th class="py-3 px-4">Company</th>
                    <th class="py-3 px-4">Applied On</th>
                    <th class="py-3 px-4">Resume</th>
                    <th class="py-3 px-4">Profile</th>
                    <th class="py-3 px-4">Progress</th>
                </tr>
            </thead>
           <tbody>
    @forelse($applications as $application)
        <tr 
            x-data="{ 
                visible: true, 
                id: {{ $application->id }}, 
                status: '{{ $application->status }}' 
            }"
            x-show="(showAll || status !== 'approved') && !removedApplicants.includes(id)"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @applicant-approved.window="if ($event.detail.id === id) visible = false"
            data-applicant-id="{{ $application->id }}"
            data-status="{{ $application->status }}"
            class="border-b hover:bg-gray-50"
        >
            <td class="py-3 px-4 font-medium whitespace-nowrap flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full {{ $application->user->active_status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                {{ $application->user->first_name . ' ' . $application->user->last_name }}
            </td>

            <td class="py-3 px-4 whitespace-nowrap">
                {{ $application->job->job_title ?? 'N/A' }}
            </td>
            <td class="py-3 px-4 whitespace-nowrap">
                {{ $application->job->company_name ?? 'N/A' }}
            </td>
            <td class="py-3 px-4 italic whitespace-nowrap">
                {{ \Carbon\Carbon::parse($application->created_at)->format('F d, Y') }}
            </td>

            <td class="py-3 px-4">
                @if($application->user->active_status === 'Active' && $application->resume_snapshot)
                    <button
                        @click="openResume('{{ asset('storage/' . $application->resume_snapshot) }}')"
                        class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                        View
                    </button>
                @elseif($application->user->active_status === 'Inactive')
                    <span class="text-gray-400 italic">Inactive</span>
                @else
                    <span class="text-gray-500 italic">None</span>
                @endif
            </td>

            <td class="py-3 px-4">
                @if($application->user->active_status === 'Active')
                    <button
                        @click="openProfile({{ $application->id }})"
                        class="border border-[#BD6F22] text-[#BD6F22] text-sm font-medium h-8 px-3 rounded hover:bg-[#BD6F22] hover:text-white">
                        View
                    </button>
                @else
                    <span class="text-gray-400 italic">Inactive</span>
                @endif
            </td>

         <td class="py-3 px-4">
    @if($application->user->active_status === 'Active')
        <div x-data="{ applicantStatus: '{{ $application->status }}' }">
            <template x-if="applicantStatus === 'approved'">
                <button 
                    class="bg-green-600 text-white text-sm font-medium h-8 px-3 rounded shadow cursor-not-allowed opacity-70"
                    disabled>
                    Approved
                </button>
            </template>
            <template x-if="applicantStatus !== 'approved'">
                <button
                    @click="selectedApplicant = { 
                        id: {{ $application->id }},
                        name: '{{ $application->user->first_name }} {{ $application->user->last_name }}',
                        status: applicantStatus
                    }; showStatusModal = true"
                    class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                    Manage
                </button>
            </template>
        </div>
    @else
        <span class="text-gray-400 italic">Inactive</span>
    @endif
</td>


        </tr>
    @empty
        <tr>
            <td colspan="7" class="py-6 text-center text-gray-500">No applicants yet.</td>
        </tr>
    @endforelse
</tbody>


        </table>
    </div>

<!-- Feedback Toast -->
<div 
    x-show="feedbackVisible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-4 rounded-xl shadow-lg z-50 w-80 overflow-hidden"
    x-cloak
>
    <div class="flex items-center gap-3">
        <!-- ✅ Check Animation Icon -->
        <svg class="w-6 h-6 text-white animate-checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span class="font-semibold text-sm" x-text="feedbackMessage"></span>
    </div>

    <!-- ✅ Progress Bar -->
    <div class="mt-3 h-1 w-full bg-white/20 rounded overflow-hidden">
        <div class="h-full bg-white animate-progress-bar"></div>
    </div>
</div>


    <!-- Resume Modal -->
    <div x-show="showModal"
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         x-cloak>
        <div class="bg-white rounded-lg overflow-hidden w-[90%] max-w-3xl shadow-xl relative">
            <button @click="showModal = false"
                    class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">
                &times;
            </button>
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4 text-[#BD6F22]">Resume Preview</h2>
                <iframe :src="resumeUrl" class="w-full h-[70vh] border rounded" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    <!-- Status Modal -->
    <div x-show="showStatusModal"
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         x-cloak>
        <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative">
            <button @click="showStatusModal = false"
                    class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
                &times;
            </button>
            <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">
    Manage Application
</h2>
<p class="mb-6 text-sm text-gray-700">
    What action would you like to take for 
    <span class="font-semibold text-[#BD6F22]" x-text="selectedApplicant?.name"></span>?
</p>

<div class="flex justify-end gap-3 flex-wrap">
    <button @click="showStatusModal = false"
            class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">
        Cancel
    </button>

    <!-- If already approved -->
<template x-if="selectedApplicant && selectedApplicant.status === 'approved'">
    <button
        class="px-4 py-2 text-sm rounded bg-green-600 text-white cursor-not-allowed opacity-70"
        disabled>
        Approved
    </button>
</template>

<!-- If not yet approved -->
<template x-if="selectedApplicant && selectedApplicant.status !== 'approved'">
    <div class="flex gap-3">
       <button
    @click="statusAction = 'approved'; submitStatusChange()"
    class="px-4 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700">
    Approve
</button>

        <button
            @click="statusAction = 'declined'; submitStatusChange()"
            class="px-4 py-2 text-sm rounded bg-red-600 text-white hover:bg-red-700">
            Decline
        </button>
    </div>
</template>

</div>

        </div>
    </div>

    <!-- Profile Modals -->
    @foreach ($applications as $application)
        <div x-show="showProfile && activeProfileId === {{ $application->id }}"
             x-transition
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             x-cloak>
            <div class="bg-white rounded-lg overflow-y-auto max-h-[90vh] w-[95%] max-w-6xl shadow-xl relative p-6">
                <button @click="showProfile = false"
                        class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
                    &times;
                </button>

                <div x-data="{ tab: 'profile' }" class="flex flex-col md:flex-row gap-6">
                    <div class="flex justify-center md:justify-start flex-shrink-0 w-full md:w-auto">
                        <div class="flex flex-col items-center text-center">
                            <img src="{{ $application->user->profile_picture ? asset('storage/' . $application->user->profile_picture) : asset('images/default.png') }}"
                                alt="Profile Picture"
                                class="rounded-full w-36 h-36 object-cover border-2 border-gray-300 shadow-md mb-3">
                            <h1 class="text-lg font-semibold text-[#BD6F22]">
                                {{ $application->user->first_name }} {{ $application->user->last_name }}
                            </h1>
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="flex space-x-6 border-b mb-4 text-sm font-medium">
                            <button @click="tab = 'profile'"
                                    :class="tab === 'profile' ? 'border-b-2 border-[#BD6F22] text-[#BD6F22]' : ''"
                                    class="pb-2">Profile</button>
                            <button @click="tab = 'work'"
                                    :class="tab === 'work' ? 'border-b-2 border-[#BD6F22] text-[#BD6F22]' : ''"
                                    class="pb-2">Work Experience</button>
                        </div>

                        <div x-show="tab === 'profile'" x-cloak>
                            @include('components.hrAdmin.applicantProfile', ['user' => $application->user])
                        </div>
                        <div x-show="tab === 'work'" x-cloak>
                            @include('components.hrAdmin.applicantWorkExperience', [
                                'experiences' => $application->user->workExperiences,
                                'user' => $application->user
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Alpine.js and Handler Script -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="{{ asset('js/applicantHandler.js') }}"></script>

