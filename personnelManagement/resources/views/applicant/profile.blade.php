@extends('layouts.applicantHome')

@section('content')

<!-- Profile Picture + Form Row -->
<div class="flex flex-col md:flex-row gap-6">

    <!-- Profile Picture -->
    <div class="w-full md:w-1/4 flex justify-center md:justify-start">
        <x-applicant.profile-picture :user="$user" />
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
    <h2 class="text-lg font-semibold mb-4 text-[#BD6F22]">Work Experience</h2>
    <div class="flex flex-col items-center justify-center p-6 bg-orange-50 rounded-xl border border-dashed border-[#BD6F22] shadow-sm">
        
        <!-- Capybara with construction hat -->
    <img src="/images/capy.png" alt="..." class="border-4 border-dashed border-[#BD6F22] rounded-lg p-1" />


        <!-- Message -->
        <p class="text-center text-l text-gray-600 mb-1">
            Oops! This section is still under development.
        </p>
    
    </div>
</div>

    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            } else {
                personalTab.classList.add('hidden');
                workTab.classList.remove('hidden');
                workBtn.classList.add('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
                personalBtn.classList.remove('text-[#BD6F22]', 'border-b-2', 'border-[#BD6F22]');
                personalBtn.classList.add('text-gray-600');
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
            const sorted = data.sort((a, b) => a.name.common.localeCompare(b.name.common));
            sorted.forEach(country => {
                const demonym = country.demonyms?.eng?.m || country.name.common;
                const option = new Option(demonym, demonym);
                select.add(option);
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
            data.forEach(province => provinceSelect.add(new Option(province.name, province.code)));
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
            [...cities, ...municipalities].forEach(loc => {
                citySelect.add(new Option(loc.name, loc.code));
            });
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
                barangays.forEach(brgy => barangaySelect.add(new Option(brgy.name, brgy.name)));
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
