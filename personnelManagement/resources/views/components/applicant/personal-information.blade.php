<form action="{{ route('applicant.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Profile Form -->
        <div class="flex-1">
            <!-- Personal Info -->
            <h2 class="text-lg font-semibold mb-2" style="color: #BD6F22;">Personal Information</h2>
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
                        @if ($field === 'suffix')
                            <select name="suffix" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                                <option value="">-- Select Suffix --</option>
                                @foreach (['Jr.', 'Sr.', 'II', 'III', 'IV', 'V'] as $suffix)
                                    <option value="{{ $suffix }}" {{ old('suffix', $user->suffix) === $suffix ? 'selected' : '' }}>{{ $suffix }}</option>
                                @endforeach
                            </select>
                        @else
                            <input 
                                type="{{ $field === 'birth_date' ? 'date' : ($field === 'age' ? 'number' : 'text') }}"
                                name="{{ $field }}" 
                                value="{{ old($field, $field === 'birth_date' ? \Carbon\Carbon::parse($user->$field)->format('Y-m-d') : $user->$field) }}"
                                class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                            >
                        @endif
                    </div>
                @endforeach

                <!-- Gender -->
                <div>
                    <label class="block text-sm text-gray-700">Gender</label>
                    <select name="gender" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                        @foreach (['Male', 'Female', 'Other'] as $gender)
                            <option value="{{ $gender }}" {{ old('gender', $user->gender) === $gender ? 'selected' : '' }}>{{ $gender }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Civil Status -->
                <div>
                    <label class="block text-sm text-gray-700">Civil Status</label>
                    <select name="civil_status" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                        @foreach (['Single', 'Married', 'Divorced', 'Widowed', 'Separated'] as $status)
                            <option value="{{ $status }}" {{ old('civil_status', $user->civil_status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Religion -->
                <div>
                    <label class="block text-sm text-gray-700">Religion</label>
                    <input type="text" name="religion" value="{{ old('religion', $user->religion) }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                </div>

                <!-- Nationality -->
                <div>
                    <label class="block text-sm text-gray-700">Nationality</label>
                    <select name="nationality" id="nationality" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                        <option value="">-- Select Nationality --</option>
                        <option value="Filipino" {{ old('nationality', $user->nationality) === 'Filipino' ? 'selected' : '' }}>Filipino</option>
                        <option value="Other" {{ old('nationality', $user->nationality) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
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
                <input 
                    type="text" 
                    name="full_address" 
                    id="full_address" 
                    value="{{ old('full_address', $user->full_address) }}" 
                    class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100 cursor-not-allowed" 
                    readonly>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-sm text-gray-700">Block / House No. / Street</label>
                    <input type="text" name="street_details" value="{{ old('street_details', $user->street_details ?? '') }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Postal Code</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code ?? '') }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm text-gray-700">Province</label>
                    <select id="province" name="province" required class="w-full border rounded px-3 py-2">
                        <option value="">-- Select Province --</option>
                        <option value="Cebu" {{ old('province', $user->province) === 'Cebu' ? 'selected' : '' }}>Cebu</option>
                        <!-- Add more provinces as needed -->
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">City / Municipality</label>
                    <select id="city" name="city" required class="w-full border rounded px-3 py-2" {{ old('city', $user->city) ? '' : 'disabled' }}>
                        <option value="">-- Select City --</option>
                        <option value="Cebu City" {{ old('city', $user->city) === 'Cebu City' ? 'selected' : '' }}>Cebu City</option>
                        <!-- Add more cities based on province -->
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Barangay</label>
                    <select id="barangay" name="barangay" required class="w-full border rounded px-3 py-2" {{ old('barangay', $user->barangay) ? '' : 'disabled' }}>
                        <option value="">-- Select Barangay --</option>
                        <option value="Lahug" {{ old('barangay', $user->barangay) === 'Lahug' ? 'selected' : '' }}>Lahug</option>
                        <!-- Add more barangays based on city -->
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 text-right">
                <button type="submit" class="text-white px-6 py-2 rounded transition" style="background-color: #BD6F22;">Save</button>
            </div>
        </div>
    </div>
</form>

<!-- Auto-Fill Full Address Script -->
<script>
    function updateFullAddress() {
        const street = document.querySelector('[name="street_details"]').value.trim();
        const postal = document.querySelector('[name="postal_code"]').value.trim();

        const provinceSelect = document.querySelector('#province');
        const province = provinceSelect && provinceSelect.value !== ''
            ? provinceSelect.options[provinceSelect.selectedIndex].text
            : '';

        const citySelect = document.querySelector('#city');
        const city = citySelect && citySelect.value !== ''
            ? citySelect.options[citySelect.selectedIndex].text
            : '';

        const barangaySelect = document.querySelector('#barangay');
        const barangay = barangaySelect && barangaySelect.value !== ''
            ? barangaySelect.options[barangaySelect.selectedIndex].text
            : '';

        const addressParts = [street, barangay, city, province].filter(part => part !== '');
        let fullAddress = addressParts.join(', ');

        if (postal) fullAddress += ` ${postal}`;

        document.getElementById('full_address').value = fullAddress;
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Input field listeners
        ['street_details', 'postal_code'].forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) el.addEventListener('input', updateFullAddress);
        });

        // Select dropdown listeners
        ['province', 'city', 'barangay'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', updateFullAddress);
        });

        // Initial fill on load
        updateFullAddress();
    });
</script>
