@extends('layouts.hrAdmin')

@section('content')
<section class="p-6">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Employees</h1>

    @if($employees->isEmpty())
        <p class="text-lg text-gray-600">No employees found.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white text-base">
                <thead class="bg-gray-100 text-left text-gray-800">
                    <tr>
                        <th class="py-4 px-6">Name</th>
                        <th class="py-4 px-6">Job Position</th>
                        <th class="py-4 px-6">Company</th>
                        <th class="py-4 px-6">Resume</th>
                        <th class="py-4 px-6">201 File</th>
                        <th class="py-4 px-6">Start</th>
                        <th class="py-4 px-6">End</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                        <tr class="hover:bg-gray-50 border-b-2 border-gray-300">
                            <td class="py-4 px-6 text-lg">{{ $employee->full_name }}</td>
                            <td class="py-4 px-6 text-lg">{{ $employee->job_position ?? '—' }}</td>
                            <td class="py-4 px-6 text-lg">{{ $employee->company ?? '—' }}</td>
                            <td class="py-4 px-6">
                                <a href="{{ $employee->resume_url }}" class="inline-block bg-[#BD6F22] text-white text-sm font-medium px-4 py-2 rounded hover:bg-[#a55f1d] transition">
                                    See Attachment
                                </a>
                            </td>
                            <td class="py-4 px-6">
                                <a href="{{ $employee->file_201_url }}" class="inline-block bg-[#BD6F22] text-white text-sm font-medium px-4 py-2 rounded hover:bg-[#a55f1d] transition">
                                    View
                                </a>
                            </td>
                            <td class="py-4 px-6 text-lg">{{ $employee->start_date ?? '—' }}</td>
                            <td class="py-4 px-6 text-lg">{{ $employee->end_date ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
