<div x-data="workExperienceForm()" class="space-y-6">

    <!-- Work Experience Entries -->
    <template x-for="(experience, index) in experiences" :key="experience.id">
        <div class="border border-dashed border-[#BD6F22] rounded-lg p-4 relative bg-orange-50 shadow-sm">
            <h3 class="text-md font-semibold text-[#BD6F22] mb-2">
                Work Experience #<span x-text="index + 1"></span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Job Title -->
                <div>
                    <label :for="`job_title_${index}`" class="block text-sm font-medium text-gray-700">Job Title</label>
                    <input type="text" :name="`work_experience[${index}][job_title]`" :id="`job_title_${index}`"
                           class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                           x-model="experience.job_title">
                </div>

                <!-- Company Name -->
                <div>
                    <label :for="`company_name_${index}`" class="block text-sm font-medium text-gray-700">Company Name</label>
                    <input type="text" :name="`work_experience[${index}][company_name]`" :id="`company_name_${index}`"
                           class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                           x-model="experience.company_name">
                </div>

                <!-- Start Date -->
                <div>
                    <label :for="`start_date_${index}`" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" :name="`work_experience[${index}][start_date]`" :id="`start_date_${index}`"
                           class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                           x-model="experience.start_date">
                </div>

                <!-- End Date -->
                <div>
                    <label :for="`end_date_${index}`" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" :name="`work_experience[${index}][end_date]`" :id="`end_date_${index}`"
                           class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                           x-model="experience.end_date">
                </div>
            </div>

            <!-- Delete Button -->
            <button type="button"
                    class="absolute top-2 right-2 text-sm text-red-500 hover:text-red-700"
                    x-show="experiences.length > 1"
                    @click="removeExperience(index)">
                ✕
            </button>
        </div>
    </template>

    <!-- Add Work Experience Button -->
    <div>
        <button type="button" @click="addExperience()"
                class="px-4 py-2 bg-[#BD6F22] text-white rounded hover:bg-[#a65e1d] transition">
            Add Work Experience
        </button>
    </div>

    <!-- Preferred Classification Section -->
    <h3 class="text-lg font-semibold text-[#BD6F22] mt-6">Preferred Classification</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Job Industry -->
        <div>
            <label for="preferred_industry" class="block text-sm font-medium text-gray-700">Job Industry <span class="text-red-500">*</span></label>
            <select name="preferred_industry" id="preferred_industry" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                <option value="">-- Select Industry --</option>
                <option value="IT">IT</option>
                <option value="Engineering">Engineering</option>
                <option value="Healthcare">Healthcare</option>
                <!-- Add more as needed -->
            </select>
        </div>

        <!-- Role Type -->
        <div>
            <label for="preferred_role" class="block text-sm font-medium text-gray-700">Role Type <span class="text-red-500">*</span></label>
            <select name="preferred_role" id="preferred_role" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                <option value="">-- Select Role Type --</option>
                <option value="Full-Time">Full-Time</option>
                <option value="Part-Time">Part-Time</option>
                <option value="Internship">Internship</option>
                <!-- Add more as needed -->
            </select>
        </div>
    </div>
</div>

<script>
    function workExperienceForm() {
        return {
            experiences: [{
                id: Date.now(),
                job_title: '',
                company_name: '',
                start_date: '',
                end_date: ''
            }],
            addExperience() {
                this.experiences.push({
                    id: Date.now() + this.experiences.length,
                    job_title: '',
                    company_name: '',
                    start_date: '',
                    end_date: ''
                });
            },
            removeExperience(index) {
                this.experiences.splice(index, 1);
            }
        };
    }
</script>
