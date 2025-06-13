@extends('layouts.hrAdmin')

@section('content')
<section class="p-6">
    <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">
        <i class="fas fa-cog mr-2"></i>Settings
    </h1>

    <div class="bg-gray-50 p-6 rounded-md shadow-md max-w-md">
        <h3 class="text-lg font-semibold text-[#BD6F22] mb-4">
            <i class="fas fa-lock mr-2"></i>Account Security
        </h3>

        <p class="text-gray-500 mb-4 text-sm">
            Update your password regularly to keep your account secure.
        </p>

        <form method="POST" action="{{ route('user.changePassword') }}"
               class="grid grid-cols-1 gap-4">
            @csrf

            <div>
                <label for="current_pass" class="block text-sm font-semibold text-gray-700 mb-1">
                    Current Password <i class="fas fa-question-circle ml-1 text-gray-400" title="Enter your existing password first"></i>
                </label>
                <input id="current_pass" name="current_password" type="password" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>

            <div>
                <label for="new_pass" class="block text-sm font-semibold text-gray-700 mb-1">
                    New Password <i class="fas fa-question-circle ml-1 text-gray-400" title="Choose a strong password with letters, numbers, and symbols"></i>
                </label>
                <input id="new_pass" name="new_password" type="password" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>

            <div>
                <label for="new_pass_confirm" class="block text-sm font-semibold text-gray-700 mb-1">
                    Re-type Password
                </label>
                <input id="new_pass_confirm" name="new_password_confirmation" type="password" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>

            <div class="text-right mt-4">
                <button type="submit"
                        class="bg-[#BD6F22] hover:bg-[#a75f1c] text-white font-semibold px-4 py-2 rounded-md transition">
                    Save
                </button>
            </div>
        </form>
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
    });
</script>
@endsection
