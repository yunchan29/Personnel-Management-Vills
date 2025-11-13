@extends(auth()->user()->role === 'hrAdmin' ? 'layouts.hrAdmin' : 'layouts.hrStaff')

@section('content')
<div x-data class="relative">
  @if(auth()->user()->role === 'hrAdmin')
  <div class="p-6 max-w-6xl mx-auto">
    <h1 class="text-2xl font-semibold text-[#BD6F22] mb-6">Archived Applicants</h1>
  </div>
  @else
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Archived Applications</h1>
  </div>
  @endif

  <div class="overflow-x-auto bg-white p-4 rounded-lg shadow-lg">
    <table class="min-w-full text-sm text-left text-gray-700">
      <thead class="border-b font-semibold bg-gray-50">
        <tr>
          <th class="py-3 px-4">Name</th>
          <th class="py-3 px-4">Email</th>
          <th class="py-3 px-4">Job Title</th>
          <th class="py-3 px-4">Company</th>
          <th class="py-3 px-4">Archived On</th>
          <th class="py-3 px-4">Actions</th>
        </tr>
      </thead>
      <tbody>
  @forelse($applications as $application)
    <tr class="border-b hover:bg-gray-50">
      <td class="py-3 px-4">
        {{ $application->user->first_name ?? 'N/A' }}
        {{ $application->user->last_name ?? '' }}
      </td>
      <td class="py-3 px-4">{{ $application->user->email ?? 'N/A' }}</td>
      <td class="py-3 px-4">{{ $application->job->job_title ?? 'N/A' }}</td>
      <td class="py-3 px-4">{{ $application->job->company_name ?? 'N/A' }}</td>
      <td class="py-3 px-4 italic">
        {{ \Carbon\Carbon::parse($application->updated_at)->format('F d, Y') }}
      </td>

      <td class="py-3 px-4 flex gap-3">
        {{-- Details Button --}}
        <button type="button" onclick="openArchiveDetailsModal({{ $application->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
          Details
        </button>

        @if(auth()->user()->role === 'hrAdmin')
          {{-- HR Admin Delete --}}
          <form action="{{ route('hrAdmin.archive.destroy', $application->id) }}" method="POST" class="delete-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
              Delete
            </button>
          </form>
        @endif
      </td>
    </tr>
  @empty
    <tr>
      <td colspan="6" class="py-6 text-center text-gray-500">
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

{{-- SweetAlert for delete confirmation --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Delete confirmation
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the archived {{ auth()->user()->role === 'hrAdmin' ? 'employee record' : 'application' }}!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Success Toast
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    @endif

    // Error Toast
    @if(session('error'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: "{{ session('error') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    @endif
});
</script>
@endsection
