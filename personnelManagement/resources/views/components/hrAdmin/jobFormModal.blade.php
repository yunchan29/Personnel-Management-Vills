<div 
    x-data="{
        open: false,
        job: {},
        openModal(event) {
            this.job = event.detail || {};
            this.open = true;
        },
        get isEdit() {
            return !!this.job.id;
        },
        confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This job posting will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    }" 
    x-init="window.addEventListener('open-job-modal', e => openModal(e))"
    x-show="open"
    @keydown.escape.window="open = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-end md:items-center"
    style="display: none;"
>
    <div 
        x-show="open"
        x-transition:enter="transition transform ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-12 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition transform ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-12 scale-95"
        class="bg-white w-full max-w-4xl rounded-t-2xl md:rounded-lg shadow-lg p-6 relative"
    >
        {{-- Close Button --}}
        <button 
            @click="open = false"
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-xl"
        >
            ✕
        </button>

        {{-- Modal Content --}}
        <h3 class="text-xl font-semibold text-[#BD6F22] mb-6" x-text="isEdit ? 'Edit Job' : 'Add Job'"></h3>

        <form :action="isEdit ? `/hrAdmin/jobPosting/${job.id}` : '{{ route('hrAdmin.jobPosting.store') }}'" method="POST">
            @csrf
            <template x-if="isEdit">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label for="job_title" class="block font-medium mb-1">Job Title</label>
                        <input type="text" name="job_title" id="job_title" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2" 
                            :value="job.job_title || ''" required>
                    </div>
                    <div>
                        <label for="company_name" class="block font-medium mb-1">Company</label>
                        <input type="text" name="company_name" id="company_name" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2" 
                            :value="job.company_name || ''" required>
                    </div>
                    <div>
                        <label for="vacancies" class="block font-medium mb-1">Number of Vacancies</label>
                        <input type="number" name="vacancies" id="vacancies" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2" 
                            :value="job.vacancies || ''" required>
                    </div>
                    <div>
                        <label for="qualifications" class="block font-medium mb-1">Qualification</label>
                        <textarea name="qualifications" id="qualifications" rows="5"
                            class="w-full border border-gray-300 rounded-md px-3 py-2"
                            x-text="
                                Array.isArray(job.qualifications)
                                    ? job.qualifications.flatMap(q => q.split(',')).join('\n')
                                    : (job.qualifications || '')
                            "
                            required></textarea>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <label for="job_industry" class="block mb-1 font-medium">Job Industry</label>
                        <select name="job_industry" id="job_industry" class="w-full p-2 border rounded">
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
                                <option 
                                    value="{{ $industry }}" 
                                    :selected="(job.job_industry || '') === '{{ $industry }}'"
                                >
                                    {{ $industry }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="location" class="block font-medium mb-1">Location</label>
                        <input type="text" name="location" id="location" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2" 
                            :value="job.location || ''" required>
                    </div>
                    <div>
                        <label for="apply_until" class="block font-medium mb-1">Apply until</label>
                        <input type="date" name="apply_until" id="apply_until" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2" 
                            :value="job.apply_until || ''" required>
                    </div>
                    <div>
                        <label for="additional_info" class="block font-medium mb-1">Additional Information</label>
                        <textarea name="additional_info" id="additional_info" rows="5"
                            class="w-full border border-gray-300 rounded-md px-3 py-2"
                            x-text="
                                Array.isArray(job.additional_info)
                                    ? job.additional_info.flatMap(info => info.split(',')).join('\n')
                                    : (job.additional_info || '')
                            "></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mt-6 flex-wrap gap-3">
                {{-- Delete Button (only in Edit mode) --}}
                <template x-if="isEdit">
                    <form 
                        :id="`delete-form-${job.id}`"
                        :action="`/hrAdmin/jobPosting/${job.id}`" 
                        method="POST"
                    >
                        @csrf
                        @method('DELETE')
                        <button 
                            type="button"
                            @click="confirmDelete(job.id)"
                            class="px-4 py-2 w-[110px] bg-red-600 text-white rounded-md hover:bg-red-700 transition text-sm"
                        >
                            Delete
                        </button>
                    </form>
                </template>

                {{-- Update / Save Button --}}
                <button 
                    type="submit" 
                    class="px-4 py-2 w-[110px] bg-[#BD6F22] text-white rounded-md hover:bg-[#a65e1d] transition text-sm"
                >
                    <span x-text="isEdit ? 'Update' : 'Save'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
