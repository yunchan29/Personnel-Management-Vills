@props(['user' => null, 'application' => null'])

@php
    $targetUser = $user ?? $application?->user;
@endphp

<!-- Profile Modal -->
<div x-show="showProfile && activeProfileId === {{ $targetUser->id }}"
     x-transition
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     x-cloak>
    <div class="bg-white rounded-lg overflow-y-auto max-h-[90vh] w-[95%] max-w-6xl shadow-xl relative p-6">
        <!-- Close Button -->
        <button @click="showProfile = false"
                class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>

        <div x-data="{ tab: 'profile' }" class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar -->
            <div class="flex justify-center md:justify-start flex-shrink-0 w-full md:w-auto">
                <div class="flex flex-col items-center text-center">
                    <img src="{{ $targetUser->profile_picture ? asset('storage/' . $targetUser->profile_picture) : asset('images/default.png') }}"
                         alt="Profile Picture"
                         class="rounded-full w-36 h-36 object-cover border-2 border-gray-300 shadow-md mb-3">
                    <h1 class="text-lg font-semibold text-[#BD6F22]">
                        {{ $targetUser->first_name }} {{ $targetUser->last_name }}
                    </h1>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                <!-- Tabs -->
                <div class="flex space-x-6 border-b mb-4 text-sm font-medium">
                    <button @click="tab = 'profile'"
                            :class="tab === 'profile' ? 'border-b-2 border-[#BD6F22] text-[#BD6F22]' : ''"
                            class="pb-2">Profile</button>
                    <button @click="tab = 'work'"
                            :class="tab === 'work' ? 'border-b-2 border-[#BD6F22] text-[#BD6F22]' : ''"
                            class="pb-2">Work Experience</button>
                    <button @click="tab = 'files'"
                            :class="tab === 'files' ? 'border-b-2 border-[#BD6F22] text-[#BD6F22]' : ''"
                            class="pb-2">201 Files</button>
                </div>

                <!-- Tab Contents -->
                <div x-show="tab === 'profile'" x-cloak>
                    @include('components.hrAdmin.applicantProfile', ['user' => $targetUser])
                </div>
                <div x-show="tab === 'work'" x-cloak>
                    @include('components.hrAdmin.applicantWorkExperience', [
                        'experiences' => $targetUser->workExperiences,
                        'user' => $targetUser
                    ])
                </div>
                <div x-show="tab === 'files'" x-cloak>
                    @include('components.hrAdmin.applicant201', ['user' => $targetUser])
                </div>
            </div>
        </div>
    </div>
</div>
