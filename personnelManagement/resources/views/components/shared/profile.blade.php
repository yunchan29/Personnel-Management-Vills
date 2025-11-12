@props(['user', 'experiences', 'updateRoute'])

<!-- Profile Picture + Form Row -->
<div class="flex flex-col md:flex-row gap-6">

    <!-- Profile Picture -->
    <div class="w-full md:w-auto flex justify-center md:justify-start">
        <div class="flex flex-col items-center">
            <img id="previewImage"
                src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default.png') }}"
                alt="Profile Picture"
                class="rounded-full w-36 h-36 object-cover border-2 border-gray-300">

            <div class="mt-4">
                <button type="button"
                        onclick="document.getElementById('profile_picture').click()"
                        class="cursor-pointer text-white px-4 py-2 rounded transition"
                        style="background-color: #BD6F22;">
                    Edit Picture
                </button>
            </div>
        </div>
    </div>

<!-- Right Column -->
<div class="w-full md:flex-1">
    <div class="max-w-4xl w-full mx-auto flex flex-col">

        <!-- Tab Title -->
        <div class="flex flex-col md:flex-row justify-between items-center md:items-start mb-4 gap-2">
            <nav class="flex space-x-4 text-sm font-medium">
                <button type="button" id="tab-personal-btn" class="tab-btn text-[#BD6F22] border-b-2 border-[#BD6F22] pb-2">
                    Personal Information
                </button>

                <button type="button" id="tab-work-btn" class="tab-btn text-gray-600 hover:text-[#BD6F22] pb-2">
                    Work Experience
                </button>

                @if(auth()->user()->role === 'applicant')
                <button type="button" id="tab-preference-btn" class="tab-btn text-gray-600 hover:text-[#BD6F22] pb-2">
                    Preference
                </button>
                @endif
            </nav>
        </div>

        <!-- Tab Content -->
        <div id="tab-personal" class="tab-content">
            <form id="personalInfoForm"
                  action="{{ route(auth()->user()->role . '.profile.updatePersonalInfo') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="file"
                    id="profile_picture"
                    name="profile_picture"
                    accept="image/jpeg, image/png"
                    onchange="validateImage(this)"
                    class="hidden">
                <x-shared.personal-information :user="$user" :editable="auth()->user()->role === 'applicant'" />
            </form>
        </div>

        <div id="tab-work" class="tab-content hidden">
            <form id="workExperienceForm"
                  action="{{ route(auth()->user()->role . '.profile.updateWorkExperience') }}"
                  method="POST">
                @csrf
                @method('PUT')
                <x-shared.work-experience :experiences="$experiences" :user="$user" />
            </form>
        </div>

        @if(auth()->user()->role === 'applicant')
        <div id="tab-preference" class="tab-content hidden">
            <form id="preferenceForm"
                  action="{{ route(auth()->user()->role . '.profile.updatePreference') }}"
                  method="POST">
                @csrf
                @method('PUT')
                <x-shared.preference :user="$user" />
            </form>
        </div>
        @endif

    </div>
</div>

</div>


<!-- Scripts -->
<!-- Auto-Fill Full Address Script -->
<script>
    function updateFullAddress() {
        const street = document.querySelector('[name="street_details"]')?.value.trim() || '';
        const postal = document.querySelector('[name="postal_code"]')?.value.trim() || '';

        const getSelectedText = (selectId) => {
            const select = document.getElementById(selectId);
            if (select && @if(auth()->user()->role === 'employee') !select.disabled && @endif select.value !== '') {
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

    @if(auth()->user()->role === 'applicant')
    // ✅ Attach listeners, even if selects are added dynamically
    function attachListeners() {
        const fields = ['street_details', 'postal_code'];
        const selects = ['province', 'city', 'barangay'];

        fields.forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el && !el.dataset.listenerAttached) {
                el.addEventListener('input', updateFullAddress);
                el.dataset.listenerAttached = true;
            }
        });

        selects.forEach(id => {
            const el = document.getElementById(id);
            if (el && !el.dataset.listenerAttached) {
                el.addEventListener('change', updateFullAddress);
                el.dataset.listenerAttached = true;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        attachListeners();

        // ✅ Re-attach listeners in case selects are replaced (e.g., AJAX)
        const observer = new MutationObserver(() => attachListeners());
        observer.observe(document.body, { childList: true, subtree: true });

        // ✅ Run periodically to ensure address updates as data loads
        const interval = setInterval(() => {
            updateFullAddress();
        }, 500);

        // Stop after 5 seconds (once everything loads)
        setTimeout(() => clearInterval(interval), 5000);
    });
    @else
    document.addEventListener('DOMContentLoaded', () => {
        const fields = ['street_details', 'postal_code'];
        const selects = ['province', 'city', 'barangay'];

        // Input field listeners
        fields.forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) el.addEventListener('input', updateFullAddress);
        });

        // Select dropdown listeners
        selects.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', updateFullAddress);
                if (el.value !== '') el.disabled = false;
            }
        });

        // Delay initial fill to ensure dropdowns are populated
        setTimeout(updateFullAddress, 300);
    });
    @endif
</script>


<!-- SweetAlert2 for notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Component Tab logic -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const personalBtn = document.getElementById('tab-personal-btn');
        const workBtn = document.getElementById('tab-work-btn');
        const personalTab = document.getElementById('tab-personal');
        const workTab = document.getElementById('tab-work');

        @if(auth()->user()->role === 'applicant')
        const preferenceBtn = document.getElementById('tab-preference-btn');
        const preferenceTab = document.getElementById('tab-preference');
        @endif

        function activateTab(tab) {
            // Hide all tabs
            personalTab.classList.add('hidden');
            workTab.classList.add('hidden');
            @if(auth()->user()->role === 'applicant')
            preferenceTab.classList.add('hidden');
            @endif

            // Remove active state from all buttons
            personalBtn.classList.remove('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
            personalBtn.classList.add('text-gray-600');
            workBtn.classList.remove('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
            workBtn.classList.add('text-gray-600');
            @if(auth()->user()->role === 'applicant')
            preferenceBtn.classList.remove('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
            preferenceBtn.classList.add('text-gray-600');
            @endif

            // Show selected tab and activate button
            if (tab === 'personal') {
                personalTab.classList.remove('hidden');
                personalBtn.classList.add('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
                personalBtn.classList.remove('text-gray-600');
            } else if (tab === 'work') {
                workTab.classList.remove('hidden');
                workBtn.classList.add('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
                workBtn.classList.remove('text-gray-600');
            }
            @if(auth()->user()->role === 'applicant')
            else if (tab === 'preference') {
                preferenceTab.classList.remove('hidden');
                preferenceBtn.classList.add('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
                preferenceBtn.classList.remove('text-gray-600');
            }
            @endif
        }

        personalBtn.addEventListener('click', () => activateTab('personal'));
        workBtn.addEventListener('click', () => activateTab('work'));
        @if(auth()->user()->role === 'applicant')
        preferenceBtn.addEventListener('click', () => activateTab('preference'));
        @endif

        // Check URL parameter for tab on page load
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            activateTab(tabParam);
        }
    });

    function validateImage(input) {
        const file = input.files[0];
        if (!file) return;

        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            input.value = '';
            Swal.fire({
                icon: 'error',
                title: 'Invalid file type',
                text: 'Only JPG and PNG files are allowed.',
                confirmButtonColor: '#BD6F22'
            });
            return;
        }

        previewFile(input); // 👈 this ensures the preview still happens
    }

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

// Load Nationalities
fetch("https://restcountries.com/v3.1/all?fields=name,demonyms")
    .then(res => res.json())
    .then(data => {
        const select = document.getElementById("nationality");

        const options = data.map(country => {
            const demonym = country.demonyms?.eng?.m || country.name.common;
            return { label: demonym, value: demonym };
        });

        // Sort alphabetically by label (case-insensitive)
        options.sort((a, b) => a.label.localeCompare(b.label, undefined, { sensitivity: 'base' }));

        options.forEach(opt => {
            select.add(new Option(opt.label, opt.value));
        });

        select.value = "{{ old('nationality', $user->nationality) }}";
    });

// PSGC Dropdowns
const provinceSelect = document.getElementById('province');
const citySelect = document.getElementById('city');
const barangaySelect = document.getElementById('barangay');

const oldProvince = @json(old('province', $user->province));
const oldCity = @json(old('city', $user->city));
const oldBarangay = @json(old('barangay', $user->barangay));

// Store mappings of names to codes
const provinceNameToCode = {};
const cityNameToCode = {};

fetch('https://psgc.gitlab.io/api/provinces/')
    .then(res => res.json())
    .then(data => {
        data
            .sort((a, b) => a.name.localeCompare(b.name))
            .forEach(province => {
                // Store name-to-code mapping
                provinceNameToCode[province.name] = province.code;
                // Save NAME as value instead of code
                provinceSelect.add(new Option(province.name, province.name));
            });

        if (oldProvince) {
            provinceSelect.value = oldProvince;
            @if(auth()->user()->role === 'applicant')
            // Trigger city load automatically
            provinceSelect.dispatchEvent(new Event('change'));
            @else
            provinceSelect.dispatchEvent(new Event('change'));
            @endif
}

    });

provinceSelect.addEventListener('change', () => {
    const provinceName = provinceSelect.value;
    const provCode = provinceNameToCode[provinceName];
    citySelect.innerHTML = '<option value="">-- Select City --</option>';
    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
    // Clear city mapping for new province
    Object.keys(cityNameToCode).forEach(key => delete cityNameToCode[key]);
    @if(auth()->user()->role === 'employee' || in_array(auth()->user()->role, ['hrAdmin', 'hrStaff']))
    citySelect.disabled = true;
    barangaySelect.disabled = true;
    @endif
    if (!provCode) return;

    Promise.all([
        fetch(`https://psgc.gitlab.io/api/provinces/${provCode}/cities/`).then(res => res.json()),
        fetch(`https://psgc.gitlab.io/api/provinces/${provCode}/municipalities/`).then(res => res.json())
    ]).then(([cities, municipalities]) => {
        [...cities, ...municipalities]
            .sort((a, b) => a.name.localeCompare(b.name))
            .forEach(loc => {
                // Store name-to-code mapping
                cityNameToCode[loc.name] = loc.code;
                // Save NAME as value instead of code
                citySelect.add(new Option(loc.name, loc.name));
            });
         @if(auth()->user()->role === 'applicant')
         // ✅ Only select city *after* options are loaded
       if (oldCity && provinceSelect.value === oldProvince) {
            citySelect.value = oldCity;
            citySelect.dispatchEvent(new Event('change'));
}
        @else
        citySelect.disabled = false;
        if (oldCity) {
            citySelect.value = oldCity;
            citySelect.dispatchEvent(new Event('change'));
        }
        @endif

    });
});

citySelect.addEventListener('change', () => {
    const cityName = citySelect.value;
    const cityCode = cityNameToCode[cityName];
    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
    @if(auth()->user()->role === 'employee' || in_array(auth()->user()->role, ['hrAdmin', 'hrStaff']))
    barangaySelect.disabled = true;
    @endif
    if (!cityCode) return;

    fetch(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`)
        .then(res => res.json())
        .then(barangays => {
            barangays
                .sort((a, b) => a.name.localeCompare(b.name))
                .forEach(brgy => barangaySelect.add(new Option(brgy.name, brgy.name)));

            @if(auth()->user()->role === 'applicant')
            // ✅ Select saved barangay only when it matches
            if (oldBarangay && citySelect.value === oldCity) {
                barangaySelect.value = oldBarangay;
            }
            // ✅ Once all location fields are restored, update full address
            if (typeof updateFullAddress === 'function') {
                updateFullAddress();
            }
            @else
            barangaySelect.disabled = false;
            if (oldBarangay) {
                barangaySelect.value = oldBarangay;
            }
            @endif
        });
});



    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Profile Updated',
            text: '{{ session("success") }}',
            confirmButtonColor: '#BD6F22'
        });
    @endif
</script>
