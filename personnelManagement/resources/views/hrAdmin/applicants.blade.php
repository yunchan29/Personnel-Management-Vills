<div x-data="{ showModal: false, resumeUrl: '' }" class="relative">
    <div class="overflow-x-auto bg-white p-4 rounded-lg shadow">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="border-b font-semibold bg-gray-50">
                <tr>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Position Applied</th>
                    <th class="py-3 px-4">Company</th>
                    <th class="py-3 px-4">Date Applied</th>
                    <th class="py-3 px-4">Resume</th>
                    <th class="py-3 px-4">Profile</th>
                    <th class="py-3 px-4">Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">
                            {{ $application->user->first_name . ' ' . $application->user->last_name }}
                        </td>
                        <td class="py-3 px-4">
                            {{ $application->job->job_title ?? 'N/A' }}
                        </td>
                        <td class="py-3 px-4">
                            {{ $application->job->company_name ?? 'N/A' }}
                        </td>
                        <td class="py-3 px-4 italic">
                            {{ \Carbon\Carbon::parse($application->created_at)->format('F d, Y') }}
                        </td>
                        <td class="py-3 px-4">
                            @if($application->resume_snapshot)
                                <button
                                    @click="resumeUrl = '{{ asset('storage/' . $application->resume_snapshot) }}'; showModal = true"
                                    class="bg-[#BD6F22] text-white px-3 py-1 rounded shadow hover:bg-[#a95e1d]">
                                    See Attachment
                                </button>
                            @else
                                <span class="text-gray-500 italic">None</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <a href="#" class="border border-[#BD6F22] text-[#BD6F22] px-3 py-1 rounded hover:bg-[#BD6F22] hover:text-white">
                                View
                            </a>
                        </td>
                        <td class="py-3 px-4">
                            <span class="bg-[#BD6F22] text-white px-3 py-1 rounded shadow">
                                {{ $application->status ?? 'Pending' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-4 text-center text-gray-500">No applicants yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Resume Modal --}}
    <div x-show="showModal"
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         x-cloak>
        <div class="bg-white rounded-lg overflow-hidden w-[90%] max-w-3xl shadow-lg relative">
            <button @click="showModal = false"
                    class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">
                &times;
            </button>
            <div class="p-4">
                <h2 class="text-lg font-semibold mb-4 text-[#BD6F22]">Resume Preview</h2>
                <iframe :src="resumeUrl" class="w-full h-[70vh]" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
