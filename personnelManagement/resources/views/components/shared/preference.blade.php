@props(['user'])

<div x-data="preferenceForm()"
x-init="$nextTick(() => {
    window.formSections = window.formSections || {};
    window.formSections.preference = $data;
})"
class="space-y-6">

    <!-- Preferred Classification -->
    <h3 class="text-lg font-semibold text-[#BD6F22]">Preferred Classification</h3>
    <div class="mt-2">
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
    </div>
</div>

<script>
function preferenceForm() {
    return {
        validate() {
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
