@extends(auth()->user()->role === 'hrAdmin' ? 'layouts.hrAdmin' : 'layouts.hrStaff')

@section('content')
<div x-data="archiveManager()" class="relative">
  @if(auth()->user()->role === 'hrAdmin')
  <div class="p-6 max-w-6xl mx-auto">
    <h1 class="text-2xl font-semibold text-[#BD6F22] mb-6">Archived Applicants</h1>
  </div>
  @else
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Archived Applications</h1>
  </div>
  @endif

  {{-- Archive Table --}}
  <div class="overflow-x-auto bg-white p-4 rounded-lg shadow-lg">
    <table class="min-w-full text-sm text-left text-gray-700">
      <thead class="border-b font-semibold bg-gray-50">
        <tr>
          <th class="py-3 px-4">
          </th>
          <th class="py-3 px-4">Name</th>
          <th class="py-3 px-4">Email</th>
          <th class="py-3 px-4">Job Title</th>
          <th class="py-3 px-4">Company</th>
          <th class="py-3 px-4">Deletion</th>
          <th class="py-3 px-4">Actions</th>
        </tr>
      </thead>
      <tbody>
 @forelse($applications as $application)
  @php
    $restorableStatuses = [
        \App\Enums\ApplicationStatus::DECLINED,
        \App\Enums\ApplicationStatus::FAILED_INTERVIEW,
        \App\Enums\ApplicationStatus::FAILED_EVALUATION
    ];

    $canRestore = in_array($application->status, $restorableStatuses);
  @endphp

  <tr class="border-b hover:bg-gray-50">
    <td class="py-3 px-4">

    <td class="py-3 px-4">
      {{ $application->user->first_name ?? 'N/A' }}
      {{ $application->user->last_name ?? '' }}
    </td>

    <td class="py-3 px-4">{{ $application->user->email ?? 'N/A' }}</td>
    <td class="py-3 px-4">{{ $application->job->job_title ?? 'N/A' }}</td>
    <td class="py-3 px-4">{{ $application->job->company_name ?? 'N/A' }}</td>

   @php
    $archivedDate = \Carbon\Carbon::parse($application->updated_at);
    $deletionDate = $archivedDate->copy()->addDays(30);
    $remainingDays = (int) now()->diffInDays($deletionDate, false) + 1;

@endphp

<td class="py-3 px-4 italic">

    @if($remainingDays < 0)
        <span class="text-red-600 font-semibold">Pending deletion</span>

    @elseif($remainingDays <= 5)
        <span class="text-red-500 font-semibold">
            {{ $remainingDays }} day{{ $remainingDays == 1 ? '' : 's' }} left
        </span>

    @else
        <span class="text-gray-700">
            {{ $remainingDays }} days left
        </span>
    @endif

    <div class="text-xs text-gray-400">
        (Archived: {{ $archivedDate->format('F d, Y') }})
    </div>

</td>




    <td class="py-3 px-4 flex gap-3">
      <button type="button" onclick="openArchiveDetailsModal({{ $application->id }})"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
        Details
      </button>
      </td>
    </tr>
  @empty
    <tr>
      <td colspan="7" class="py-6 text-center text-gray-500">
        No archived {{ auth()->user()->role === 'hrAdmin' ? 'applicants' : 'applications' }}.
      </td>
    </tr>
  @endforelse
</tbody>

    </table>
  </div>
</div>

{{-- Include Archive Details Modal --}}
<x-shared.modals.archiveDetails />
@endsection     