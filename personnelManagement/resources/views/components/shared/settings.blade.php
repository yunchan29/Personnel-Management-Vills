<section class="p-6">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">
        @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff']))
        <i class="fas fa-cog mr-2"></i>
        @endif
        Settings
    </h1>

    <!-- Change Password Section -->
    <div class="@if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) bg-gray-50 p-6 rounded-md shadow-md max-w-md @else border-t border-gray-300 pt-4 mb-6 @endif">
        <h3 class="text-lg font-semibold text-[#BD6F22] mb-@if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff']))4 @else 3 @endif">
            @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff']))
            <i class="fas fa-lock mr-2"></i>Account Security
            @else
            Account Settings
            @endif
        </h3>

        @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff']))
        <p class="text-gray-500 mb-4 text-sm">
            Update your password regularly to keep your account secure.
        </p>
        @else
        <h4 class="text-md font-medium text-gray-700 mb-2">Change Password</h4>
        @endif

        <form method="POST" action="{{ route('user.changePassword') }}"
               class="grid grid-cols-1 @if(!in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) md:grid-cols-3 @endif gap-4 @if(!in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) items-end mb-4 @endif">
            @csrf

            <div>
                <label @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) for="current_pass" @endif class="block text-sm @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) font-semibold @else font-medium @endif text-gray-700 mb-1">
                    Current Password
                    @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff']))
                    <i class="fas fa-question-circle ml-1 text-gray-400" title="Enter your existing password first"></i>
                    @endif
                </label>
                <input @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) id="current_pass" @endif name="current_password" type="password" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>

            <div>
                <label @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) for="new_pass" @endif class="block text-sm @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) font-semibold @else font-medium @endif text-gray-700 mb-1">
                    New Password
                    @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff']))
                    <i class="fas fa-question-circle ml-1 text-gray-400" title="Choose a strong password with letters, numbers, and symbols"></i>
                    @endif
                </label>
                <input @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) id="new_pass" @endif name="new_password" type="password" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>

            <div>
                <label @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) for="new_pass_confirm" @endif class="block text-sm @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) font-semibold @else font-medium @endif text-gray-700 mb-1">
                    Re-type Password
                </label>
                <input @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) id="new_pass_confirm" @endif name="new_password_confirmation" type="password" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>

            <div class="@if(!in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) md:col-span-3 @endif text-right mt-@if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff']))4 @else 2 @endif">
                <button type="submit"
                        class="bg-[#BD6F22] hover:bg-[#a75f1c] text-white font-semibold px-4 py-2 rounded-md transition">
                    Save
                </button>
            </div>
        </form>
    </div>

    @if(auth()->user()->role === 'applicant')
    <!-- Account Status -->
    <div class="border-t border-gray-300 pt-4 mb-6"
        x-data="{ status: '{{ auth()->user()->active_status }}' }">
        <h4 class="text-md font-medium text-gray-700 mb-2">Account Status</h4>

        <form method="POST" action="{{ route('applicant.user.toggleVisibility') }}">
            @csrf
            <input type="hidden" name="active_status" :value="status">

            <button
                type="submit"
                @click.prevent="status = status === 'Active' ? 'Inactive' : 'Active'; $nextTick(() => $el.form.submit())"
                class="inline-flex items-center gap-2"
            >
                <!-- Toggle Icon -->
                <div :class="status === 'Active' ? 'bg-green-500 justify-end' : 'bg-gray-300 justify-start'"
                    class="w-10 h-6 rounded-full flex items-center px-1 transition-all duration-200">
                    <div class="w-4 h-4 bg-white rounded-full shadow"></div>
                </div>

                <span class="text-sm font-medium text-gray-800" x-text="status"></span>
            </button>
        </form>

        <!-- Descriptions -->
        <div class="text-sm text-gray-600 flex items-start mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 6a9 9 0 110 18 9 9 0 010-18z" />
            </svg>
            <span x-show="status === 'Active'">Your profile will be visible to HR, and they may contact you regarding job opportunities.</span>
            <span x-show="status === 'Inactive'">Your profile is hidden. HR won't be able to see or contact you.</span>
        </div>
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
            <button type="submit" id="deleteAccountBtn" class="bg-[#DD6161] hover:bg-[#c45252] text-white font-semibold px-4 py-2 rounded-md">
                Delete my account
            </button>
        </form>
    </div>
    @endif
</section>

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

        @if(auth()->user()->role === 'applicant')
        // Delete account confirmation
        document.getElementById('deleteAccountBtn').addEventListener('click', function(e) {
            e.preventDefault();
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
        @endif
    });
</script>
@endsection
