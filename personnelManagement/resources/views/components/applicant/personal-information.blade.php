<div x-data="personalForm()" x-init="$nextTick(() => {
    window.formSections = window.formSections || {};
    window.formSections.personal = $data;
})">
    <div class="flex flex-col md:flex-row gap-6">
        <div class="flex-1">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold" style="color: #BD6F22;">Personal Information</h2>
                <button type="button"
                        class="px-4 py-2 bg-[#BD6F22] text-white rounded-md shadow"
                        @click="toggleEdit"
                        x-text="isEditing ? 'Cancel' : 'Edit'">
                </button>
            </div>

            <!-- Personal Info -->
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
                        <label class="block text-sm text-gray-700">
                            {{ $label }}
                            @if(!in_array($field, ['suffix','middle_name']))
                                <span x-show="isNewAccount && !getValue('{{ $field }}')" class="text-red-600">*</span>
                            @endif
                        </label>
                        @if ($field === 'suffix')
                            <select name="suffix"
                                    class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                                    :class="isEditing ? 'bg-white cursor-pointer' : 'bg-gray-100 cursor-not-allowed'"
                                    :disabled="!isEditing">
                                <option value="">-- Select Suffix --</option>
                                @foreach (['Jr.', 'Sr.', 'II', 'III', 'IV', 'V'] as $suffix)
                                    <option value="{{ $suffix }}" {{ old('suffix', $user->suffix) === $suffix ? 'selected' : '' }}>{{ $suffix }}</option>
                                @endforeach
                            </select>
                        @else
                            <input 
                                type="{{ $field === 'birth_date' ? 'date' : ($field === 'age' ? 'number' : 'text') }}"
                                name="{{ $field }}" 
                                value="{{ old($field, $field === 'birth_date' && $user->$field ? \Carbon\Carbon::parse($user->$field)->format('Y-m-d') : $user->$field) }}"
                                class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                                :class="(isEditing && !(locks['{{ $field }}'])) ? 'bg-white cursor-text' : 'bg-gray-100 cursor-not-allowed'"
                                :readonly="!isEditing || locks['{{ $field }}']">
                        @endif
                    </div>
                @endforeach

                <!-- Gender -->
                <div>
                    <label class="block text-sm text-gray-700">
                        Gender <span x-show="isNewAccount && !getValue('gender')" class="text-red-600">*</span>
                    </label>
                    <select name="gender"
                            class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                            :class="(isEditing && !locks['gender']) ? 'bg-white cursor-pointer' : 'bg-gray-100 cursor-not-allowed'"
                            :disabled="!isEditing || locks['gender']">
                        @foreach (['Male', 'Female', 'Other'] as $gender)
                            <option value="{{ $gender }}" {{ old('gender', $user->gender) === $gender ? 'selected' : '' }}>{{ $gender }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Civil Status -->
                <div>
                    <label class="block text-sm text-gray-700">
                        Civil Status <span x-show="isNewAccount && !getValue('civil_status')" class="text-red-600">*</span>
                    </label>
                    <select name="civil_status"
                            class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                            :class="isEditing ? 'bg-white cursor-pointer' : 'bg-gray-100 cursor-not-allowed'"
                            :disabled="!isEditing">
                        @foreach (['Single', 'Married', 'Divorced', 'Widowed', 'Separated'] as $status)
                            <option value="{{ $status }}" {{ old('civil_status', $user->civil_status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Religion -->
                <div>
                    <label class="block text-sm text-gray-700">
                        Religion <span x-show="isNewAccount && !getValue('religion')" class="text-red-600">*</span>
                    </label>
                    <input type="text"
                           name="religion"
                           value="{{ old('religion', $user->religion) }}"
                           class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                           :class="isEditing ? 'bg-white cursor-text' : 'bg-gray-100 cursor-not-allowed'"
                           :readonly="!isEditing">
                </div>

                <!-- Nationality -->
                <div>
                    <label class="block text-sm text-gray-700">
                        Nationality <span x-show="isNewAccount && !getValue('nationality')" class="text-red-600">*</span>
                    </label>
                    <select name="nationality" id="nationality"
                            class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                            :class="(isEditing && !locks['nationality']) ? 'bg-white cursor-pointer' : 'bg-gray-100 cursor-not-allowed'"
                            :disabled="!isEditing || locks['nationality']">
                        <option value="">-- Select Nationality --</option>
                    </select>
                </div>
            </div>

            <!-- Contacts -->
            <h2 class="text-lg font-semibold mt-6 mb-2" style="color: #BD6F22;">Contacts</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100 cursor-not-allowed"
                           readonly>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">
                        Mobile Number <span x-show="isNewAccount && !getValue('mobile_number')" class="text-red-600">*</span>
                    </label>
                    <input type="text"
                           name="mobile_number"
                           pattern="^09\\d{9}$"
                           maxlength="11"
                           title="Enter a valid 11-digit mobile number starting with 09"
                           value="{{ old('mobile_number', $user->mobile_number) }}"
                           class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                           :class="isEditing ? 'bg-white cursor-text' : 'bg-gray-100 cursor-not-allowed'"
                           :readonly="!isEditing">
                </div>
            </div>

            <!-- Address -->
            <h2 class="text-lg font-semibold mt-6 mb-2" style="color: #BD6F22;">Present Address</h2>
            <div class="mb-4">
                <label class="block text-sm text-gray-700">Full Address</label>
                <input type="text" name="full_address" id="full_address"
                       value="{{ old('full_address', $user->full_address) }}"
                       class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100 cursor-not-allowed"
                       readonly>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-sm text-gray-700">
                        Block / House No. / Street <span x-show="isNewAccount && !getValue('street_details')" class="text-red-600">*</span>
                    </label>
                    <input type="text" name="street_details" value="{{ old('street_details', $user->street_details ?? '') }}"
                           class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                           :class="isEditing ? 'bg-white cursor-text' : 'bg-gray-100 cursor-not-allowed'"
                           :readonly="!isEditing">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">
                        Postal Code <span x-show="isNewAccount && !getValue('postal_code')" class="text-red-600">*</span>
                    </label>
                    <input type="text" name="postal_code" maxlength="4" inputmode="numeric"
                           pattern="^\\d{4}$" title="Enter a 4-digit postal code"
                           value="{{ old('postal_code', $user->postal_code ?? '') }}"
                           class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                           :class="isEditing ? 'bg-white cursor-text' : 'bg-gray-100 cursor-not-allowed'"
                           :readonly="!isEditing">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm text-gray-700">
                        Province <span x-show="isNewAccount && !getValue('province')" class="text-red-600">*</span>
                    </label>
                    <select id="province" name="province"
                            class="w-full border rounded px-3 py-2"
                            :class="isEditing ? 'bg-white cursor-pointer' : 'bg-gray-100 cursor-not-allowed'"
                            :disabled="!isEditing">
                        <option value="">-- Select Province --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">
                        City / Municipality <span x-show="isNewAccount && !getValue('city')" class="text-red-600">*</span>
                    </label>
                    <select id="city" name="city"
                            class="w-full border rounded px-3 py-2"
                            :class="isEditing ? 'bg-white cursor-pointer' : 'bg-gray-100 cursor-not-allowed'"
                            :disabled="!isEditing">
                        <option value="">-- Select City --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">
                        Barangay <span x-show="isNewAccount && !getValue('barangay')" class="text-red-600">*</span>
                    </label>
                    <select id="barangay" name="barangay"
                            class="w-full border rounded px-3 py-2"
                            :class="isEditing ? 'bg-white cursor-pointer' : 'bg-gray-100 cursor-not-allowed'"
                            :disabled="!isEditing">
                        <option value="">-- Select Barangay --</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function personalForm() {
    return {
        isEditing: false,
        isNewAccount: {{ $user->created_at ? 'false' : 'true' }},
        locks: {
            birth_date: {{ $user->birth_date ? 'true' : 'false' }},
            birth_place: {{ $user->birth_place ? 'true' : 'false' }},
            age: {{ $user->age ? 'true' : 'false' }},
            gender: {{ $user->gender ? 'true' : 'false' }},
            nationality: {{ $user->nationality ? 'true' : 'false' }},
        },
        toggleEdit() {
            this.isEditing = !this.isEditing;
        },
        getValue(field) {
            const el = document.querySelector(`[name="${field}"]`);
            return el ? el.value.trim() : '';
        }
    }
}
</script>
