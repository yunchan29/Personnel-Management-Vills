
<div class="space-y-6">
    <!-- Work Experience Entries -->
    @if($experiences && $experiences->count() > 0)
        @foreach($experiences as $index => $experience)
            <div class="border border-dashed border-[#BD6F22] rounded-lg p-4 bg-orange-50 shadow-sm">
                <h3 class="text-md font-semibold text-[#BD6F22] mb-2">
                    Work Experience #{{ $index + 1 }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Job Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Job Title</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-100">
                            {{ $experience->job_title ?? '-' }}
                        </div>
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Company Name</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-100">
                            {{ $experience->company_name ?? '-' }}
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-100">
                            {{ $experience->start_date ?? '-' }}
                        </div>
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-100">
                            {{ $experience->end_date ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-8">
            <p class="text-gray-500 italic">This applicant has no work experience.</p>
        </div>
    @endif

</div>
