@extends('layouts.hrAdmin')

@section('content')
<section>
    <h2 class="text-xl font-semibold text-[#BD6F22] mb-4">Settings</h2>

    <!-- Change Password -->
    <div class="border-t border-gray-300 pt-4 mb-6">
        <h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Account Settings</h3>
        <h4 class="text-md font-medium text-gray-700 mb-2">Change Password</h4>

        <form method="POST" action="{{ route('user.changePassword') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" name="current_password" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="new_password" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Re-type Password</label>
                <input type="password" name="new_password_confirmation" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div class="md:col-span-3 text-right mt-2">
                <button type="submit" class="bg-[#BD6F22] hover:bg-[#a75f1c] text-white font-semibold px-4 py-2 rounded-md">
                    Save
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Account -->
    <div class="border-t border-gray-300 pt-4">
        <h4 class="text-md font-medium text-gray-700 mb-2">Delete Account</h4>
        <p class="text-sm text-gray-600 mb-2 flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" fill="white" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
            </svg>
            <span>
                Deleting your account is a <strong class="text-[#DD6161]">permanent action</strong>.
                Your data will be removed and cannot be restored.
            </span>
        </p>
        <form method="POST" action="{{ route('user.deleteAccount') }}" class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
            @csrf
            @method('DELETE')
            <div class="w-full sm:w-1/2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" name="delete_password" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#DD6161]">
            </div>
            <button type="button" id="deleteAccountBtn" class="bg-[#DD6161] hover:bg-[#c45252] text-white font-semibold px-4 py-2 rounded-md">
                Delete my account
            </button>
        </form>
    </div>
</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#BD6F22'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#DD6161'
            });
        @endif

        // Delete account confirmation
        document.getElementById('deleteAccountBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action is permanent and cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#DD6161',
                cancelButtonColor: '#bbb',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form when confirmed
                    document.querySelector('form[action="{{ route('user.deleteAccount') }}"]').submit();
                }
            });
        });
    });
</script>
@endsection
