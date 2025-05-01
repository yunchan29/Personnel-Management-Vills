@extends('layouts.applicantHome')

@section('content')
<section>
    <h2 class="text-xl font-semibold text-[#BD6F22] mb-4">Settings</h2>

    <!-- Change Password -->
    <div class="border-t border-gray-300 pt-4 mb-6">
        <h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Account Settings</h3>
        <h4 class="text-md font-medium text-gray-700 mb-2">Change Password</h4>

        <form class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" name="current_password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="new_password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Re-type Password</label>
                <input type="password" name="retype_password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
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
            <!-- Black bordered circle with exclamation mark -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" fill="white" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
            </svg>
            <span>
                Deleting your account is a <strong class="text-[#DD6161]">permanent action</strong>.
                Your data will be removed and cannot be restored.
            </span>
        </p>
        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
            <div class="w-full sm:w-1/2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" name="delete_password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#DD6161]">
            </div>
            <button type="button" class="bg-[#DD6161] hover:bg-[#c45252] text-white font-semibold px-4 py-2 rounded-md">
                Delete my account
            </button>
        </div>
    </div>
</section>
@endsection
