<div class="flex flex-col md:flex-row gap-6">
    <!-- Profile Display -->
    <div class="flex-1">
        <!-- Personal Info -->
        <h2 class="text-lg font-semibold mb-2 text-[#BD6F22]">Personal Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach ([
                'first_name' => 'First Name',
                'middle_name' => 'Middle Name',
                'last_name' => 'Last Name',
                'suffix' => 'Suffix',
                'birth_date' => 'Birth Date',
                'birth_place' => 'Birth Place',
                'age' => 'Age'
            ] as $field => $label)
                <div>
                    <label class="block text-sm text-gray-700">{{ $label }}</label>
                    <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                        {{ $field === 'birth_date' ? \Carbon\Carbon::parse($user->$field)->format('F d, Y') : ($user->$field ?? '-') }}
                    </div>
                </div>
            @endforeach

            <!-- Gender -->
            <div>
                <label class="block text-sm text-gray-700">Gender</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->gender ?? '-' }}
                </div>
            </div>

            <!-- Civil Status -->
            <div>
                <label class="block text-sm text-gray-700">Civil Status</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->civil_status ?? '-' }}
                </div>
            </div>

            <!-- Religion -->
            <div>
                <label class="block text-sm text-gray-700">Religion</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->religion ?? '-' }}
                </div>
            </div>

            <!-- Nationality -->
            <div>
                <label class="block text-sm text-gray-700">Nationality</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->nationality ?? '-' }}
                </div>
            </div>
        </div>

        <!-- Contacts -->
        <h2 class="text-lg font-semibold mt-6 mb-2 text-[#BD6F22]">Contacts</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-700">Email</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->email }}
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-700">Mobile Number</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->mobile_number ?? '-' }}
                </div>
            </div>
        </div>

        <!-- Present Address -->
        <h2 class="text-lg font-semibold mt-6 mb-2 text-[#BD6F22]">Present Address</h2>
        <div class="mb-4">
            <label class="block text-sm text-gray-700">Full Address</label>
            <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                {{ $user->full_address ?? '-' }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-3">
                <label class="block text-sm text-gray-700">Block / House No. / Street</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->street_details ?? '-' }}
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-700">Postal Code</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->postal_code ?? '-' }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm text-gray-700">Province</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->province ?? '-' }}
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-700">City / Municipality</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->city ?? '-' }}
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-700">Barangay</label>
                <div class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100">
                    {{ $user->barangay ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

@if (!isset($user))
    <p class="text-red-600">⚠️ User data not passed to applicantProfile.blade.php</p>
@endif
