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
            'age' => 'Age'
        ] as $field => $label)
            <div>
                <label class="block text-sm text-gray-700">{{ $label }}</label>

                @if ($field === 'suffix')
                    <select 
                        name="suffix"
                        class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                    >
                        <option value="">-- Select Suffix --</option>
                        @foreach (['Jr.', 'Sr.', 'II', 'III', 'IV', 'V'] as $suffix)
                            <option value="{{ $suffix }}" {{ old('suffix', $user->suffix) === $suffix ? 'selected' : '' }}>
                                {{ $suffix }}
                            </option>
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

<!-- Gender Dropdown -->
<div>
    <label class="block text-sm text-gray-700">Gender</label>
    <select name="gender" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
        @foreach (['Male', 'Female', 'Other'] as $gender)
            <option value="{{ $gender }}" {{ old('gender', $user->gender) === $gender ? 'selected' : '' }}>{{ $gender }}</option>
        @endforeach
    </select>
</div>

<!-- Civil Status Dropdown -->
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
                    <input 
                        type="text" 
                        name="religion" 
                        value="{{ old('religion', $user->religion) }}" 
                        class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                    >
                </div>

               <!-- Nationality -->
<div>
    <label for="nationality" class="block text-sm text-gray-700">Nationality</label>
<select 
    name="nationality" 
    id="nationality"
    class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
>
    <option value="">-- Select Nationality --</option>
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
                <input type="text" name="full_address" value="{{ old('full_address', $user->full_address) }}" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Province -->
                <div>
                    <label class="block text-sm text-gray-700">Province</label>
                    <select id="province" name="province" required class="w-full border rounded px-3 py-2">
                        <option value="">-- Select Province --</option>
                        {{-- Options will be loaded via JS --}}
                    </select>
                </div>

                <!-- City/Municipality -->
                <div>
                    <label class="block text-sm text-gray-700">City / Municipality</label>
                    <select id="city" name="city" required class="w-full border rounded px-3 py-2" disabled>
                        <option value="">-- Select City --</option>
                        {{-- Options will be loaded via JS --}}
                    </select>
                </div>

                <!-- Barangay -->
                <div>
                    <label class="block text-sm text-gray-700">Barangay</label>
                    <select id="barangay" name="barangay" required class="w-full border rounded px-3 py-2" disabled>
                        <option value="">-- Select Barangay --</option>
                        {{-- Options will be loaded via JS --}}
                    </select>
                </div>
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="text-white px-6 py-2 rounded transition" style="background-color: #BD6F22;">Save</button>
            </div>
        </div>
    </div>
</form>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Image Preview
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

    // Load countries using REST Countries API
    fetch("https://restcountries.com/v3.1/all?fields=name,demonyms")
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            if (!Array.isArray(data)) {
                throw new Error('Invalid response format');
            }

            const select = document.getElementById("nationality");
            const sortedCountries = data.sort((a, b) => a.name.common.localeCompare(b.name.common));

            sortedCountries.forEach(country => {
                const option = document.createElement("option");
                const demonym = country.demonyms?.eng?.m || country.name.common;
                option.value = demonym;
                option.textContent = demonym;
                select.appendChild(option);
            });

            // Pre-select current nationality if exists
            const current = "{{ old('nationality', $user->nationality) }}";
            if (current) {
                select.value = current;
            }
        })
        .catch(error => {
            console.error("Error loading countries:", error);
        });

     document.addEventListener('DOMContentLoaded', function () {
        // Intercept form submission with SweetAlert2 confirmation
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault(); // Stop normal submission

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save your changes?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#BD6F22',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit(); // Submit the form if confirmed
                }
            });
        });
    });

    // Load provinces, cities, and barangays using PSGC API
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const barangaySelect = document.getElementById('barangay');

    const oldProvince = @json(old('province', $user->province));
    const oldCity = @json(old('city', $user->city));
    const oldBarangay = @json(old('barangay', $user->barangay));

    // Load provinces
    fetch('https://psgc.gitlab.io/api/provinces/')
        .then(res => res.json())
        .then(data => {
            data.forEach(province => {
                const option = new Option(province.name, province.code);
                provinceSelect.add(option);
            });

            if (oldProvince) {
                provinceSelect.value = oldProvince;
                provinceSelect.dispatchEvent(new Event('change'));
            }
        });

    // On province change → load cities and municipalities
    provinceSelect.addEventListener('change', () => {
        const provCode = provinceSelect.value;
        citySelect.innerHTML = '<option value="">-- Select City --</option>';
        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
        citySelect.disabled = true;
        barangaySelect.disabled = true;

        if (!provCode) return;

        // Combine cities and municipalities
        Promise.all([
            fetch(`https://psgc.gitlab.io/api/provinces/${provCode}/cities/`).then(res => res.json()),
            fetch(`https://psgc.gitlab.io/api/provinces/${provCode}/municipalities/`).then(res => res.json())
        ])
        .then(([cities, municipalities]) => {
            const combined = [...cities, ...municipalities];
            combined.forEach(loc => {
                const option = new Option(loc.name, loc.code);
                citySelect.add(option);
            });
            citySelect.disabled = false;

            if (oldCity) {
                citySelect.value = oldCity;
                citySelect.dispatchEvent(new Event('change'));
            }
        });
    });

    // On city/municipality change → load barangays
    citySelect.addEventListener('change', () => {
        const cityCode = citySelect.value;
        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
        barangaySelect.disabled = true;

        if (!cityCode) return;

        fetch(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`)
            .then(res => res.json())
            .then(barangays => {
                barangays.forEach(brgy => {
                    const option = new Option(brgy.name, brgy.name);
                    barangaySelect.add(option);
                });
                barangaySelect.disabled = false;

                if (oldBarangay) {
                    barangaySelect.value = oldBarangay;
                }
            });
    });

    // SweetAlert2 on success
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Profile Updated',
            text: '{{ session("success") }}',
            confirmButtonColor: '#BD6F22'
        });
    @endif
</script>

@endsection
