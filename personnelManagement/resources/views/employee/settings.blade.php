@extends('layouts.employeeHome')

@section('content')
<section class="p-6">
    <h2 class="text-2xl font-semibold text-[#BD6F22] mb-6">Settings</h2>

    <!-- Change Password Section -->
    <div class="bg-gray-50 p-6 rounded-md shadow-md mb-6">
        <h3 class="text-lg font-semibold text-[#BD6F22] mb-4">Account Settings</h3>

        <div class="mb-4">
            <h4 class="text-md font-semibold text-gray-700 mb-2">Change Password</h4>
            <p class="text-gray-500 mb-4 text-sm">
                Update your password regularly to keep your account secure.
            </p>

            <form method="POST" action="{{ route('user.changePassword') }}"
                   class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1" for="current_pass">
                        Current Password
                    </label>
                    <input id="current_pass" name="current_password" type="password" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1" for="new_pass">
                        New Password
                    </label>
                    <input id="new_pass" name="new_password" type="password" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1" for="new_pass_confirm">
                        Re-type Password
                    </label>
                    <input id="new_pass_confirm" name="new_password_confirmation" type="password" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                </div>

                <div class="md:col-span-3 text-right mt-4">
                    <button type="submit"
                            class="bg-[#BD6F22] hover:bg-[#a75f1c] text-white font-semibold px-4 py-2 rounded-md">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

   

</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({ icon:'success', title:'Success', text:'{{ session('success') }}', confirmButtonColor:'#BD6F22' });
        @endif

        @if($errors->any()) 
            Swal.fire({ icon:'error', title:'Error', html:`{!! implode('<br>', $errors->all()) !!}` , confirmButtonColor:'#DD6161' });
        @endif

        // Delete account confirmation
        document.getElementById('deleteAccountBtn').addEventListener('click', function() {
            Swal.fire({ 
                title:'Are you sure?', 
                text:'This action is permanent and cannot be undone.', 
                icon:'warning',
                showCancelButton:true,
                confirmButtonText:'Yes, delete it!', 
                cancelButtonText:'Cancel',
                confirmButtonColor:'#DD6161',
                cancelButtonColor:'#bbb',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('form[action="{{ route('user.deleteAccount') }}"]').submit();
                }
            });
        });
    });
</script>
@endsection
