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
                class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 {{ in_array($field, ['first_name', 'last_name']) ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                {{ in_array($field, ['first_name', 'last_name']) ? 'readonly' : '' }}
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
                    </select>
                </div>
            </div>

            <!-- Contacts -->
            <h2 class="text-lg font-semibold mt-6 mb-2" style="color: #BD6F22;">Contacts</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}" 
                        class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100 cursor-not-allowed" 
                        readonly>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Mobile Number</label>
                    <input
                        type="text"
                        name="mobile_number"
                        pattern="^09\d{9}$"
                        maxlength="11"
                        title="Enter a valid 11-digit mobile number starting with 09"
                        value="{{ old('mobile_number', $user->mobile_number) }}"
                        class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                        required>
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
                    <input type="text" name="street_details" value="{{ old('street_details', $user->street_details ?? '') }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Postal Code</label>
                    <input 
                        type="text" 
                        name="postal_code" 
                        required 
                        maxlength="4"
                        inputmode="numeric"
                        pattern="^\d{4}$"
                        title="Enter a 4-digit postal code"
                        value="{{ old('postal_code', $user->postal_code ?? '') }}" 
                        class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm text-gray-700">Province</label>
                    <select id="province" name="province" required class="w-full border rounded px-3 py-2">
                        <option value="">-- Select Province --</option>
                        <!-- Add more provinces as needed -->
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">City / Municipality</label>
                    <select id="city" name="city" required class="w-full border rounded px-3 py-2" {{ old('city', $user->city) ? '' : 'disabled' }}>
                        <option value="">-- Select City --</option>
                        <!-- Add more cities based on province -->
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Barangay</label>
                    <select id="barangay" name="barangay" required class="w-full border rounded px-3 py-2" {{ old('barangay', $user->barangay) ? '' : 'disabled' }}>
                        <option value="">-- Select Barangay --</option>
                        <!-- Add more barangays based on city -->
                    </select>
                </div>
            </div>
        </div>
    </div>


<!-- Auto-Fill Full Address Script -->
<script>
    function updateFullAddress() {
        const street = document.querySelector('[name="street_details"]')?.value.trim() || '';
        const postal = document.querySelector('[name="postal_code"]')?.value.trim() || '';

        const getSelectedText = (selectId) => {
            const select = document.getElementById(selectId);
            if (select && !select.disabled && select.value !== '') {
                const selectedOption = select.options[select.selectedIndex];
                return selectedOption?.text?.trim() || '';
            }
            return '';
        };

        const province = getSelectedText('province');
        const city = getSelectedText('city');
        const barangay = getSelectedText('barangay');

        const addressParts = [street, barangay, city, province].filter(Boolean);
        let fullAddress = addressParts.join(', ');

        if (postal) fullAddress += ` ${postal}`;

        const fullAddressInput = document.getElementById('full_address');
        if (fullAddressInput) {
            fullAddressInput.value = fullAddress;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const fields = ['street_details', 'postal_code'];
        const selects = ['province', 'city', 'barangay'];

        // Attach input event listeners
        fields.forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) el.addEventListener('input', updateFullAddress);
        });

        // Attach change event listeners for dropdowns
        selects.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', updateFullAddress);

                // Optional: Enable the select if a value is already set
                if (el.value !== '') {
                    el.disabled = false;
                }
            }
        });

        // Delay initial fill to ensure options are rendered
        setTimeout(updateFullAddress, 200);
    });
</script>
