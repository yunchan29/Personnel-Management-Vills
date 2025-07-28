<div class="overflow-x-auto relative bg-white p-6 rounded-lg shadow-lg">
    <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="border-b font-semibold bg-gray-50">
            <tr>
                <th class="py-3 px-4">Name</th>
                <th class="py-3 px-4">Company</th>
                <th class="py-3 px-4">Start</th>
                <th class="py-3 px-4">End</th>
                <th class="py-3 px-4">Resume</th>
                <th class="py-3 px-4">201 File</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-medium whitespace-nowrap flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded-full {{ $employee->active_status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        {{ $employee->full_name }}
                    </td>
                    <td class="py-3 px-4 whitespace-nowrap">{{ $employee->company ?? '—' }}</td>
                    <td class="py-3 px-4 whitespace-nowrap">{{ $employee->start_date ?? '—' }}</td>
                    <td class="py-3 px-4 whitespace-nowrap">{{ $employee->end_date ?? '—' }}</td>
                    <td class="py-3 px-4">
                        @if($employee->resume_url && $employee->active_status === 'Active')
                            <a href="{{ $employee->resume_url }}" target="_blank"
                               class="bg-[#BD6F22] text-white text-sm font-medium h-8 px-3 rounded shadow hover:bg-[#a95e1d]">
                                View
                            </a>
                        @else
                            <span class="text-gray-400 italic">{{ $employee->active_status === 'Inactive' ? 'Inactive' : 'None' }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        @if($employee->file_201_url && $employee->active_status === 'Active')
                            <a href="{{ $employee->file_201_url }}" target="_blank"
                               class="border border-[#BD6F22] text-[#BD6F22] text-sm font-medium h-8 px-3 rounded hover:bg-[#BD6F22] hover:text-white">
                                View
                            </a>
                        @else
                            <span class="text-gray-400 italic">{{ $employee->active_status === 'Inactive' ? 'Inactive' : 'None' }}</span>
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
</div>
