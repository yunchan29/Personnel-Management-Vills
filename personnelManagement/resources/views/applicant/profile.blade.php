@extends('layouts.applicantHome')

@section('content')
<form action="{{ route('applicant.profile.update', auth()->user()->id) }}" method="POST" enctype="multipart/form-data">
     @csrf
    @method('PUT')

<div class="flex flex-col md:flex-row gap-6">
    <!-- Profile Picture -->
    <div class="flex flex-col items-center">
        <img id="previewImage" 
             src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default.jpg') }}" 
             alt="Profile Picture" 
             class="rounded-full w-36 h-36 object-cover border-2 border-gray-300">
        
        <label class="mt-4 cursor-pointer text-white px-4 py-2 rounded transition" style="background-color: #BD6F22;">
            Edit Picture
            <input type="file" name="profile_picture" id="profile_picture" class="hidden" onchange="previewFile(this)">
        </label>
    </div>

    <!-- Profile Form -->
    <div class="flex-1">
        
            <!-- Personal Information -->
            <h2 class="text-lg font-semibold mb-2" style="color: #BD6F22;">Personal Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm text-gray-700">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Suffix</label>
                    <input type="text" name="suffix" value="{{ old('suffix', $user->suffix) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Birth Date</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Birth Place</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $user->birth_place) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Age</label>
                    <input type="number" name="age" value="{{ old('age', $user->age) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Gender</label>
                    <input type="text" name="gender" value="{{ old('gender', $user->gender) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Civil Status</label>
                    <input type="text" name="civil_status" value="{{ old('civil_status', $user->civil_status) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Religion</label>
                    <input type="text" name="religion" value="{{ old('religion', $user->religion) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Nationality</label>
                    <input type="text" name="nationality" value="{{ old('nationality', $user->nationality) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <!-- Contacts -->
            <h2 class="text-lg font-semibold mt-6 mb-2" style="color: #BD6F22;">Contacts</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Mobile Number</label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <!-- Present Address -->
            <h2 class="text-lg font-semibold mt-6 mb-2" style="color: #BD6F22;">Present Address</h2>
            <div class="mb-4">
                <label class="block text-sm text-gray-700">Full Address</label>
                <input type="text" name="full_address" value="{{ old('full_address', $user->full_address) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-700">Province</label>
                    <input type="text" name="province" value="{{ old('province', $user->province) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">City / Municipality</label>
                    <input type="text" name="city" value="{{ old('city', $user->city) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Barangay</label>
                    <input type="text" name="barangay" value="{{ old('barangay', $user->barangay) }}" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="text-white px-6 py-2 rounded transition" style="background-color: #BD6F22;">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Image Preview Script -->
<script>
    function previewFile(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('previewImage').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
</script>

@endsection
