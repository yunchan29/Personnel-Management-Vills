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

  {{-- Bulk Actions Bar --}}
  <div x-show="selectedItems.length > 0" x-cloak class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
    <div class="flex items-center justify-between">
      <span class="text-sm font-medium text-gray-700">
        <span x-text="selectedItems.length"></span> item(s) selected
      </span>
      <div class="flex gap-3">
        @if(auth()->user()->role === 'hrAdmin')
          {{-- Bulk Delete Button for HR Admin --}}
          <button type="button" @click="bulkDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium">
            Delete Selected
          </button>
        @else
          {{-- Bulk Restore Button for HR Staff --}}
          <button type="button" @click="bulkRestore()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium">
            Restore Selected
          </button>
        @endif
        <button type="button" @click="selectedItems = []; selectAll = false;" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm font-medium">
          Cancel
        </button>
      </div>
    </div>
  </div>

  <div class="overflow-x-auto bg-white p-4 rounded-lg shadow-lg">
    <table class="min-w-full text-sm text-left text-gray-700">
      <thead class="border-b font-semibold bg-gray-50">
        <tr>
          <th class="py-3 px-4">
            <input type="checkbox"
                   x-model="selectAll"
                   @change="toggleAll()"
                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
          </th>
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
    @php
      // For hrStaff, only failed_evaluation applications can be restored
      $canRestore = auth()->user()->role === 'hrAdmin' || $application->status === \App\Enums\ApplicationStatus::FAILED_EVALUATION;
    @endphp
    <tr class="border-b hover:bg-gray-50">
      <td class="py-3 px-4">
        <input type="checkbox"
               class="archive-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
               value="{{ $application->id }}"
               x-model="selectedItems"
               @change="updateSelectAll()"
               {{ !$canRestore ? 'disabled' : '' }}
               title="{{ !$canRestore && auth()->user()->role === 'hrStaff' ? 'Only Failed Evaluation applications can be restored' : '' }}">
      </td>
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

{{-- SweetAlert for delete confirmation --}}
<script>
// Alpine.js Archive Manager Component
function archiveManager() {
    return {
        selectedItems: [],
        selectAll: false,
        isHrStaff: {{ auth()->user()->role === 'hrStaff' ? 'true' : 'false' }},

        toggleAll() {
            if (this.selectAll) {
                if (this.isHrStaff) {
                    // For hrStaff, only select restorable items (not disabled)
                    this.selectedItems = Array.from(document.querySelectorAll('.archive-checkbox:not([disabled])')).map(cb => cb.value);
                } else {
                    // For hrAdmin, select all items
                    this.selectedItems = Array.from(document.querySelectorAll('.archive-checkbox')).map(cb => cb.value);
                }
            } else {
                this.selectedItems = [];
            }
        },

        updateSelectAll() {
            const checkboxes = this.isHrStaff
                ? document.querySelectorAll('.archive-checkbox:not([disabled])')
                : document.querySelectorAll('.archive-checkbox');
            this.selectAll = checkboxes.length > 0 && this.selectedItems.length === checkboxes.length;
        },

        bulkDelete() {
            if (this.selectedItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No items selected',
                    text: 'Please select at least one item to delete.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            const selectedIds = this.selectedItems;
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will permanently delete ' + selectedIds.length + ' archived record(s)!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete them!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('hrAdmin.archive.bulkDestroy') }}';

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    const idsInput = document.createElement('input');
                    idsInput.type = 'hidden';
                    idsInput.name = 'ids';
                    idsInput.value = JSON.stringify(selectedIds);
                    form.appendChild(idsInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        },

        bulkRestore() {
            if (this.selectedItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No items selected',
                    text: 'Please select at least one item to restore.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Filter only restorable items (enabled checkboxes)
            const restorableIds = this.selectedItems.filter(id => {
                const checkbox = document.querySelector('.archive-checkbox[value="' + id + '"]');
                return checkbox && !checkbox.disabled;
            });

            if (restorableIds.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Cannot restore',
                    text: 'None of the selected items can be restored. Only applications with Failed Evaluation status can be restored.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Show warning if some items were filtered out
            const filteredCount = this.selectedItems.length - restorableIds.length;
            let warningText = 'This will restore ' + restorableIds.length + ' archived record(s).';
            if (filteredCount > 0) {
                warningText += ' (' + filteredCount + ' item(s) skipped - only Failed Evaluation applications can be restored)';
            }

            Swal.fire({
                title: 'Restore selected items?',
                text: warningText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, restore them!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('hrStaff.archive.bulkRestore') }}';

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    form.appendChild(methodInput);

                    const idsInput = document.createElement('input');
                    idsInput.type = 'hidden';
                    idsInput.name = 'ids';
                    idsInput.value = JSON.stringify(restorableIds);
                    form.appendChild(idsInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    };
}

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
