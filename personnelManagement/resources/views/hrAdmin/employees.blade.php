@extends('layouts.hrAdmin')

@section('content')
<section class="p-4">
    <h1 class="mb-4 text-2xl font-bold text-[#BD6F22]">Employees</h1>

    @if($employees->isEmpty())
        <p class="text-gray-600">No employees found.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead class="bg-gray-100 text-left text-gray-700">
                    <tr>
                        <th class="py-3 px-4 border-b">Name</th>
                        <th class="py-3 px-4 border-b">Job Position</th>
                        <th class="py-3 px-4 border-b">Company</th>
                        <th class="py-3 px-4 border-b">Resume</th>
                        <th class="py-3 px-4 border-b">201 File</th>
                        <th class="py-3 px-4 border-b">Start</th>
                        <th class="py-3 px-4 border-b">End</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b">{{ $employee->full_name }}</td>
                            <td class="py-2 px-4 border-b">{{ $employee->job_position ?? '—' }}</td>
                            <td class="py-2 px-4 border-b">{{ $employee->company ?? '—' }}</td>
                            <td class="py-2 px-4 border-b">
                                <a href="{{ $employee->resume_url }}" class="inline-block bg-[#BD6F22] text-white text-xs font-semibold px-3 py-1 rounded hover:bg-[#a55f1d] transition">
                                    See Attachment
                                </a>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <a href="{{ $employee->file_201_url }}" class="inline-block bg-[#BD6F22] text-white text-xs font-semibold px-3 py-1 rounded hover:bg-[#a55f1d] transition">
                                    View
                                </a>
                            </td>
                            <td class="py-2 px-4 border-b">{{ $employee->start_date ?? '—' }}</td>
                            <td class="py-2 px-4 border-b">{{ $employee->end_date ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
