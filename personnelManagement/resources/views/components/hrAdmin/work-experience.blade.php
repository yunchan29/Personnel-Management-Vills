<div x-data='workExperienceForm(@json($experiences->toArray()))'
x-init="$nextTick(() => window.formSections.work = $data)"
x-ref="workForm"
class="space-y-6">

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
                    <input type="text"
                           :name="`work_experience[${index}][job_title]`"
                           :id="`job_title_${index}`"
                           x-model="experience.job_title"
                           class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                           >
                </div>

                <!-- Company Name -->
                <div>
                    <label :for="`company_name_${index}`" class="block text-sm font-medium text-gray-700">Company Name</label>
                    <input type="text"
                           :name="`work_experience[${index}][company_name]`"
                           :id="`company_name_${index}`"
                           x-model="experience.company_name"
                           class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                           >
                </div>

                <!-- Start Date -->
                <div>
                    <label :for="`start_date_${index}`" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date"
                           :name="`work_experience[${index}][start_date]`"
                           :id="`start_date_${index}`"
                           x-model="experience.start_date"
                           class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                           >
                </div>

                <!-- End Date -->
                <div>
                    <label :for="`end_date_${index}`" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date"
                           :name="`work_experience[${index}][end_date]`"
                           :id="`end_date_${index}`"
                           x-model="experience.end_date"
                           class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                           >
                </div>
            </div>

            <!-- Delete Button -->
            <button type="button"
                    class="absolute top-2 right-2 text-sm text-red-500 hover:text-red-700"
                    @click="experiences.length > 1 ? removeExperience(index) : clearExperience(index)">
                <span x-show="experiences.length > 1">✕</span>
                <span x-show="experiences.length === 1">🧹</span>
            </button>
        </div>
    </template>

    <!-- Add Work Experience Button -->
    <div>
        <button type="button"
                @click="addExperience()"
                class="px-4 py-2 bg-[#BD6F22] text-white rounded hover:bg-[#a65e1d] transition">
            Add Work Experience
        </button>
    </div>

    <!-- Preferred Classification -->
    <h3 class="text-lg font-semibold text-[#BD6F22] mt-6">Preferred Classification</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Job Industry -->
        <div>
            <label for="job_industry" class="block mb-2 font-medium">Job Industry</label>
            <select name="job_industry" id="job_industry"
                    class="w-full p-2 border rounded">
                <option value="">-- Select Job Industry --</option>
                @foreach([
                    "Accounting", "Administration", "Architecture", "Arts and Design",
                    "Automotive", "Banking and Finance", "Business Process Outsourcing (BPO)",
                    "Construction", "Customer Service", "Data and Analytics", "Education",
                    "Engineering", "Entertainment", "Environmental Services", "Food and Beverage",
                    "Government", "Healthcare", "Hospitality", "Human Resources",
                    "Information Technology", "Insurance", "Legal", "Logistics and Supply Chain",
                    "Manufacturing", "Marketing", "Media and Communications", "Nonprofit",
                    "Pharmaceuticals", "Public Relations", "Real Estate", "Retail", "Sales",
                    "Science and Research", "Skilled Trades", "Sports and Recreation",
                    "Telecommunications", "Tourism", "Transportation", "Utilities",
                    "Warehouse and Distribution", "Writing and Publishing"
                ] as $industry)
                    <option value="{{ $industry }}" {{ $user->job_industry === $industry ? 'selected' : '' }}>{{ $industry }}</option>
                @endforeach
            </select>
        </div>

        <!-- Role Type -->
        <div>
            <label for="preferred_role" class="block text-sm font-medium text-gray-700">Role Type <span class="text-red-500">*</span></label>
            <select name="preferred_role" id="preferred_role"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                <option value="">-- Select Role Type --</option>
                @foreach(['Full-Time', 'Part-Time', 'Internship'] as $type)
                    <option value="{{ $type }}" {{ $user->preferred_role === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<script>
function workExperienceForm(existing = []) {
    return {
        experiences: existing.length
            ? existing.map(exp => ({
                id: exp.id,
                job_title: exp.job_title || '',
                company_name: exp.company_name || '',
                start_date: exp.start_date || '',
                end_date: exp.end_date || ''
            }))
            : [{
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
        },

        clearExperience(index) {
            this.experiences[index].job_title = '';
            this.experiences[index].company_name = '';
            this.experiences[index].start_date = '';
            this.experiences[index].end_date = '';
        },

        validate() {
            for (let i = 0; i < this.experiences.length; i++) {
                const exp = this.experiences[i];
                const fields = [exp.job_title, exp.company_name, exp.start_date, exp.end_date];
                const someFilled = fields.some(f => f && f.trim() !== '');
                const allFilled = fields.every(f => f && f.trim() !== '');

                if (someFilled && !allFilled) {
                    Swal.fire({
                        icon: 'error',
                        title: `Incomplete Work Experience ${i + 1}`,
                        text: `Please complete all fields in Work Experience ${i + 1}.`,
                        confirmButtonColor: '#BD6F22'
                    });
                    return false;
                }

                if (exp.start_date && exp.end_date && exp.start_date > exp.end_date) {
                Swal.fire({
                    icon: 'warning',
                    title: `Invalid Dates in Work Experience #${i + 1}`,
                    text: 'Start date must not be later than end date.',
                    confirmButtonColor: '#BD6F22'
                });
                    return false;
                }
            }
            const jobIndustry = document.getElementById('job_industry');
                
            if (!jobIndustry.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Job Industry',
                    text: 'Please select a job industry.',
                    confirmButtonColor: '#BD6F22'
                });
                return false;
            }

            return true;
        }
    };
}
</script>

