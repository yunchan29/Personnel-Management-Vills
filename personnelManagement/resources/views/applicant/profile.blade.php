@extends('layouts.applicantHome')


@section('content')

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Profile Picture -->
        <div class="flex flex-col items-center">
            <img src="{{ asset('path_to_default_or_uploaded_image.jpg') }}" alt="Profile Picture" class="rounded-full w-36 h-36 object-cover border-2 border-gray-300">
            <button type="button" class="mt-4 text-white px-4 py-2 rounded transition" style="background-color: #BD6F22;">Edit Picture</button>
        </div>

        <!-- Profile Form -->
        <div class="flex-1">
            <form action="#" method="POST">
                @csrf

                <!-- Personal Information -->
                <h2 class="text-lg font-semibold mb-2" style="color: #BD6F22;">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">First Name</label>
                        <input type="text" name="first_name" value="" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Middle Name</label>
                        <input type="text" name="middle_name" value="" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Last Name</label>
                        <input type="text" name="last_name" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Suffix</label>
                        <input type="text" name="suffix" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Birth Date</label>
                        <input type="date" name="birth_date" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Birth Place</label>
                        <input type="text" name="birth_place" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Age</label>
                        <input type="number" name="age" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Sex</label>
                        <input type="text" name="sex" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Civil Status</label>
                        <input type="text" name="civil_status" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Religion</label>
                        <input type="text" name="religion" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Nationality</label>
                        <input type="text" name="nationality" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <!-- Contacts -->
                <h2 class="text-lg font-semibold mt-6 mb-2" style="color: #BD6F22;">Contacts</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">Email</label>
                        <input type="email" name="email" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Mobile Number</label>
                        <input type="text" name="mobile_number" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <!-- Present Address -->
                <h2 class="text-lg font-semibold mt-6 mb-2" style="color: #BD6F22;">Present Address</h2>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Full Address</label>
                    <input type="text" name="full_address" class="w-full border border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">Province</label>
                        <input type="text" name="province" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">City / Municipality</label>
                        <input type="text" name="city" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Barangay</label>
                        <input type="text" name="barangay" class="w-full border border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div class="mt-6 text-right">
                    <button type="submit" class="text-white px-6 py-2 rounded transition" style="background-color: #BD6F22;">Save</button>
                </div>
            </form>
        </div>
    </div>


@endsection