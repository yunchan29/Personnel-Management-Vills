<div x-data="{ tab: 'profile' }">
    <!-- Tabs -->
    <div class="flex space-x-4 mb-4 border-b">
        <button @click="tab = 'profile'" :class="tab === 'profile' ? 'font-semibold border-b-2 border-[#BD6F22]' : ''">
            Profile
        </button>
        <button @click="tab = 'work'" :class="tab === 'work' ? 'font-semibold border-b-2 border-[#BD6F22]' : ''">
            Work Experience
        </button>
        <button @click="tab = 'gov'" :class="tab === 'gov' ? 'font-semibold border-b-2 border-[#BD6F22]' : ''">
            201 Files
        </button>
    </div>

    <!-- Profile Tab -->
    <div x-show="tab === 'profile'" x-cloak>
        @include('components.hrAdmin.applicantProfile', ['user' => $user])
    </div>

    <!-- Work Experience Tab -->
    <div x-show="tab === 'work'" x-cloak>
        @include('components.hrAdmin.applicantWorkExperience', ['experiences' => $experiences])
    </div>

    <!-- 201 Files Tab -->
    <div x-show="tab === 'gov'" x-cloak>
        @include('components.hrAdmin.applicant201', ['file201' => $file201])
    </div>
</div>
