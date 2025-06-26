@extends('layouts.applicantHome')

@section('content')
<form id="profileForm" 
      action="{{ route('applicant.profile.update') }}" 
      method="POST" 
      enctype="multipart/form-data">

    @csrf
    @method('PUT')
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
                <button type="button" onclick="document.getElementById('profile_picture').click()" class="cursor-pointer text-white px-4 py-2 rounded transition" style="background-color: #BD6F22;">
                    Edit Picture
                </button>
                <input type="file" name="profile_picture" id="profile_picture" class="hidden" onchange="previewFile(this)">
            </div>
            
        </div>
    </div>

    <!-- Right Column -->
    <div class="w-full md:flex-1 flex flex-col">

        <!-- Tab Title + Buttons -->
            <div class="flex flex-col md:flex-row justify-between items-center md:items-start mb-4 gap-2">   
                <nav class="flex space-x-4 text-sm font-medium">
                    <button type="button" id="tab-personal-btn" class="tab-btn text-[#BD6F22] border-b-2 border-[#BD6F22] pb-2">
                        Personal Information
                    </button>

                    <button type="button" id="tab-work-btn" class="tab-btn text-gray-600 hover:text-[#BD6F22] pb-2">
                        Work Experience
                    </button>
                </nav>
            </div>

                    <!-- Tab Content -->
            <div id="tab-personal" class="tab-content">
                <x-applicant.personal-information :user="$user" />
            </div>

            <div id="tab-work" class="tab-content hidden">
                <x-applicant.work-experience :experiences="$experiences" :user="$user" />
            </div>
    </div>
</div>
    <!-- Submit Button -->
    <div class="mt-6 text-right">
        <button 
            type="button" 
            onclick="validateAllTabsAndSubmit()"
            class="text-white px-6 py-2 rounded transition" 
            style="background-color: #BD6F22;">
            Save
        </button>
    </div>
</form>

<!-- Scripts -->


<!-- Global Form Validation -->
<script>
window.formSections = {};
function validateAllTabsAndSubmit() {
    // Access both registered Alpine components
    const sections = window.formSections;
    const order = ['personal', 'work'];

    for (const key of order) {
        const section = sections[key];
        if (section && typeof section.validate === 'function') {
            const valid = section.validate();
            if (!valid) {
                // Show the tab to user
                document.querySelector(`#tab-${key}-btn`)?.click();
                return; // Stop submission if one is invalid
            }
        }
    }

    // All valid — programmatically submit
    document.getElementById('profileForm').submit();
}
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

        function activateTab(tab) {
            if (tab === 'personal') {
                personalTab.classList.remove('hidden');
                workTab.classList.add('hidden');
                personalBtn.classList.add('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
                workBtn.classList.remove('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
                workBtn.classList.add('text-gray-600');

                // Disable required when user's on personal information tab
                job_industry.removeAttribute('required');
            } else {
                personalTab.classList.add('hidden');
                workTab.classList.remove('hidden');
                workBtn.classList.add('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
                personalBtn.classList.remove('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
                personalBtn.classList.add('text-gray-600');

                // Enable required on visible fields
                job_industry.setAttribute('required', 'required');
            }
        }

        personalBtn.addEventListener('click', () => activateTab('personal'));
        workBtn.addEventListener('click', () => activateTab('work'));
    });

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

fetch('https://psgc.gitlab.io/api/provinces/')
    .then(res => res.json())
    .then(data => {
        data
            .sort((a, b) => a.name.localeCompare(b.name))
            .forEach(province => {
                provinceSelect.add(new Option(province.name, province.code));
            });

        if (oldProvince) {
            provinceSelect.value = oldProvince;
            provinceSelect.dispatchEvent(new Event('change'));
        }
    });

provinceSelect.addEventListener('change', () => {
    const provCode = provinceSelect.value;
    citySelect.innerHTML = '<option value="">-- Select City --</option>';
    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
    citySelect.disabled = true;
    barangaySelect.disabled = true;
    if (!provCode) return;

    Promise.all([
        fetch(`https://psgc.gitlab.io/api/provinces/${provCode}/cities/`).then(res => res.json()),
        fetch(`https://psgc.gitlab.io/api/provinces/${provCode}/municipalities/`).then(res => res.json())
    ]).then(([cities, municipalities]) => {
        [...cities, ...municipalities]
            .sort((a, b) => a.name.localeCompare(b.name))
            .forEach(loc => citySelect.add(new Option(loc.name, loc.code)));

        citySelect.disabled = false;
        if (oldCity) {
            citySelect.value = oldCity;
            citySelect.dispatchEvent(new Event('change'));
        }
    });
});

citySelect.addEventListener('change', () => {
    const cityCode = citySelect.value;
    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
    barangaySelect.disabled = true;
    if (!cityCode) return;

    fetch(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`)
        .then(res => res.json())
        .then(barangays => {
            barangays
                .sort((a, b) => a.name.localeCompare(b.name))
                .forEach(brgy => barangaySelect.add(new Option(brgy.name, brgy.name)));
            barangaySelect.disabled = false;
            if (oldBarangay) {
                barangaySelect.value = oldBarangay;
            }
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

@endsection
