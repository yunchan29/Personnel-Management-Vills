@extends('layouts.applicantHome')

@section('content')
<form action="{{ route('applicant.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Profile Picture -->
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

        <!-- Profile Form -->
        <div class="flex-1">
            <!-- Personal Information -->
            <h2 class="text-lg font-semibold mb-2" style="color: #BD6F22;">Personal Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach ([
                    'first_name' => 'First Name',
                    'middle_name' => 'Middle Name',
                    'last_name' => 'Last Name',
                    'suffix' => 'Suffix',
                    'birth_date' => 'Birth Date',
                    'birth_place' => 'Birth Place',
                    'age' => 'Age',
                    'gender' => 'Gender',
                    'civil_status' => 'Civil Status',
                    'religion' => 'Religion',
                    'nationality' => 'Nationality'
                ] as $field => $label)
                <div>
                    <label class="block text-sm text-gray-700">{{ $label }}</label>
                    <input 
                        type="{{ $field === 'birth_date' ? 'date' : ($field === 'age' ? 'number' : 'text') }}"
                        name="{{ $field }}" 
                        value="{{ old($field, $field === 'birth_date' ? \Carbon\Carbon::parse($user->$field)->format('Y-m-d') : $user->$field) }}"
                        class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                    >
                </div>
                @endforeach
            </div>

            <!-- Contacts -->
            <h2 class="text-lg font-semibold mt-6 mb-2" style="color: #BD6F22;">Contacts</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Mobile Number</label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                </div>
            </div>

            <!-- Present Address -->
            <h2 class="text-lg font-semibold mt-6 mb-2" style="color: #BD6F22;">Present Address</h2>
            <div class="mb-4">
                <label class="block text-sm text-gray-700">Full Address</label>
                <input type="text" name="full_address" value="{{ old('full_address', $user->full_address) }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-700">Province</label>
                    <input type="text" name="province" value="{{ old('province', $user->province) }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">City / Municipality</label>
                    <input type="text" name="city" value="{{ old('city', $user->city) }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Barangay</label>
                    <input type="text" name="barangay" value="{{ old('barangay', $user->barangay) }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                </div>
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="text-white px-6 py-2 rounded transition" style="background-color: #BD6F22;">Save</button>
            </div>
        </div>
    </div>
</form>

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
