<div 
    x-data="{
        showProfile: false,
        activeProfileId: null,
        openResume(url) {
            window.open(url, '_blank');
        },
        openProfile(id) {
            this.showProfile = true;
            this.activeProfileId = id;
        }
    }"
    class="overflow-x-auto relative bg-white p-6 rounded-lg shadow-lg"
>
    <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="border-b font-semibold bg-gray-50">
            <tr>
                <th class="py-3 px-4">Name</th>
                <th class="py-3 px-4">Company</th>
                <th class="py-3 px-4">Start</th>
                <th class="py-3 px-4">End</th>
                <th class="py-3 px-4">Resume</th>
                <th class="py-3 px-4">Profile</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
                <tr class="border-b hover:bg-gray-50">
                    <!-- Name -->
                    <td class="py-3 px-4 font-medium whitespace-nowrap flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded-full {{ $employee->active_status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        {{ $employee->full_name }}
                    </td>

                    <!-- Company -->
                    <td class="py-3 px-4 whitespace-nowrap">
                        {{ $employee->job->company_name ?? '—' }}
                    </td>

                    <!-- Start Date -->
                    <td class="py-3 px-4 whitespace-nowrap">
                        {{ $employee->start_date ?? '—' }}
                    </td>

                    <!-- End Date -->
                    <td class="py-3 px-4 whitespace-nowrap">
                        {{ $employee->end_date ?? '—' }}
                    </td>

                    <!-- Resume -->
                    <td class="py-3 px-4">
                        @if($employee->resume && $employee->resume->getResume() && $employee->active_status === 'Active')
                            <button
                                @click="openResume('{{ $employee->resume->getResume() }}')"
                                class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                View
                            </button>
                        @else
                            <span class="text-gray-400 italic">
                                {{ $employee->active_status === 'Inactive' ? 'Inactive' : 'None' }}
                            </span>
                        @endif
                    </td>

                    <!-- Profile -->
                    <td class="py-3 px-4">
                        @if($employee->active_status === 'Active')
                            <button
                                @click="openProfile({{ $employee->id }})"
                                class="border border-[#BD6F22] text-[#BD6F22] text-sm font-medium h-8 px-3 rounded hover:bg-[#BD6F22] hover:text-white">
                                View
                            </button>
                        @else
                            <span class="text-gray-400 italic">Inactive</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-gray-500">No employees found for this position.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Profile Modals -->
    @foreach ($employees as $employee)
        <div 
            x-show="showProfile && activeProfileId === {{ $employee->id }}"
            x-transition
            x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-lg overflow-y-auto max-h-[90vh] w-[95%] max-w-6xl shadow-xl relative p-6">
                <!-- Close Button -->
                <button 
                    @click="showProfile = false"
                    class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 text-2xl font-bold">
                    &times;
                </button>

                <x-hrAdmin.modals.profile :user="$employee" />
            </div>
        </div>
    @endforeach
</div>
