<div class="flex flex-col items-center">
    <img id="previewImage" 
        src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default.png') }}" 
        alt="Profile Picture" 
        class="rounded-full w-36 h-36 object-cover border-2 border-gray-300">

    <label class="mt-4 cursor-pointer text-white px-4 py-2 rounded transition" style="background-color: #BD6F22;">
        Edit Picture
        <input type="file" name="profile_picture" id="profile_picture" class="hidden" onchange="previewFile(this)">
    </label>
</div>
