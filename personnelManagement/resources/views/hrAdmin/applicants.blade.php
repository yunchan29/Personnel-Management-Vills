<div x-data="{
    showModal: false,
    resumeUrl: '',
    showProfile: false,
    activeProfileId: null,
    showStatusModal: false,
    statusAction: '',
    selectedApplicant: null
}" class="relative">

    <!-- Applicants Table -->
    <div class="overflow-x-auto overflow-visible bg-white p-6 rounded-lg shadow-lg">
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
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium whitespace-nowrap">
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
                            @if($application->resume_snapshot)
                                <button
                                    @click="resumeUrl = '{{ asset('storage/' . $application->resume_snapshot) }}'; showModal = true"
                                    class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                    View
                                </button>
                            @else
                                <span class="text-gray-500 italic">None</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <button
                                @click="showProfile = true; activeProfileId = {{ $application->id }}"
                                class="border border-[#BD6F22] text-[#BD6F22] text-sm font-medium h-8 px-3 rounded hover:bg-[#BD6F22] hover:text-white">
                                View
                            </button>
                        </td>
                        <td class="py-3 px-4 relative group overflow-visible z-20">
                            <button type="button"
                                class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow flex items-center gap-2">
                                {{ $application->status ?? 'Pending' }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                            <div class="absolute hidden group-hover:block bg-white border border-gray-200 rounded shadow-md z-50 mt-1 w-32">
                                <button
                                    @click="statusAction = 'approve'; selectedApplicant = { id: {{ $application->id }}, name: '{{ $application->user->first_name }} {{ $application->user->last_name }}' }; showStatusModal = true"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Approve
                                </button>
                                <button
                                    @click="statusAction = 'decline'; selectedApplicant = { id: {{ $application->id }}, name: '{{ $application->user->first_name }} {{ $application->user->last_name }}' }; showStatusModal = true"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Decline
                                </button>
                            </div>
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
                Confirm <span x-text="statusAction === 'approve' ? 'Approval' : 'Decline'"></span>
            </h2>
            <p class="mb-6 text-sm text-gray-700">
                Are you sure you want to <span class="font-bold" x-text="statusAction"></span> the application of
                <span class="font-semibold text-[#BD6F22]" x-text="selectedApplicant?.name"></span>?
            </p>
            <div class="flex justify-end gap-3">
                <button @click="showStatusModal = false"
                        class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">
                    Cancel
                </button>
                <button
                    @click="console.log('Submitting status change for', selectedApplicant, statusAction); showStatusModal = false"
                    class="px-4 py-2 text-sm rounded bg-[#BD6F22] text-white hover:bg-[#a95e1d]">
                    Confirm
                </button>
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
                    <!-- Profile Picture -->
                    <div class="w-full md:w-1/4 flex justify-center md:justify-start">
                        <div class="flex flex-col items-center">
                            <img src="{{ $application->user->profile_picture ? asset('storage/' . $application->user->profile_picture) : asset('images/default.png') }}"
                                 alt="Profile Picture"
                                 class="rounded-full w-36 h-36 object-cover border-2 border-gray-300 shadow-md">
                        </div>
                    </div>

                    <!-- Tabbed Content -->
                    <div class="w-full md:w-3/4">
                        <div class="flex space-x-6 border-b mb-4 text-sm font-medium">
                            <button @click="tab = 'profile'"
                                    :class="tab === 'profile' ? 'border-b-2 border-[#BD6F22] text-[#BD6F22]' : ''"
                                    class="pb-2">Profile</button>
                            <button @click="tab = 'work'"
                                    :class="tab === 'work' ? 'border-b-2 border-[#BD6F22] text-[#BD6F22]' : ''"
                                    class="pb-2">Work Experience</button>
                            <button @click="tab = 'gov'"
                                    :class="tab === 'gov' ? 'border-b-2 border-[#BD6F22] text-[#BD6F22]' : ''"
                                    class="pb-2">201 Files</button>
                        </div>

                        <div x-show="tab === 'profile'" x-cloak>
                            @include('components.hrAdmin.applicantProfile', ['user' => $application->user])
                        </div>
                        <div x-show="tab === 'work'" x-cloak>
                            @include('components.hrAdmin.applicantWorkExperience', ['experiences' => $application->user->experiences])
                        </div>
                        <div x-show="tab === 'gov'" x-cloak>
                            @include('components.hrAdmin.applicant201', ['file201' => $application->user->file201])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
