<div x-show="showInterviewModal" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative">
        <!-- Close button -->
        <button @click="showInterviewModal = false"
            class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>

        <!-- Dynamic Modal Title -->
        <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">
            <template x-if="interviewMode === 'single'">
                <span>Set Interview for <span x-text="interviewApplicant?.name"></span></span>
            </template>

            <template x-if="interviewMode === 'bulk'">
                <span>Set Interview for <span x-text="selectedApplicants.length"></span> applicants</span>
            </template>

            <template x-if="interviewMode === 'reschedule'">
                <span>Reschedule Interview for <span x-text="interviewApplicant?.name"></span></span>
            </template>

            <template x-if="interviewMode === 'bulk-reschedule'">
                <span>Mass Reschedule for <span x-text="selectedApplicants.length"></span> applicants</span>
            </template>
        </h2>

        <!-- Date -->
        <label class="block text-sm font-medium mb-1">
            <template x-if="interviewMode.includes('reschedule')">
                <span>Set New Date</span>
            </template>
            <template x-if="!interviewMode.includes('reschedule')">
                <span>Interview Date</span>
            </template>
        </label>
        <input
            type="date"
            x-model="interviewDate"
            :min="new Date().toISOString().split('T')[0]"
            class="w-full mb-4 p-2 border rounded"
        />

        <!-- Time -->
        <label class="block text-sm font-medium mb-1">
            <template x-if="interviewMode.includes('reschedule')">
                <span>Set New Time</span>
            </template>
            <template x-if="!interviewMode.includes('reschedule')">
                <span>Interview Start</span>
            </template>
        </label>
        
        <div class="flex gap-2 mb-4">
            <!-- Hours -->
            <select x-model.number="interviewTime" class="flex-1 p-2 border rounded">
                <template x-for="h in [8,9,10,11,]" :key="h">
                    <option :value="h" x-text="h"></option>
                </template>
            </select>

            <!-- Auto AM/PM -->
            <input 
                type="text" 
                x-model="interviewPeriod" 
                class="w-24 p-2 border rounded text-center bg-gray-100" 
                readonly
            />
        </div>

        <!-- Confirm button -->
        <div class="flex justify-end gap-3">
            <button @click="submitInterviewDate"
                :disabled="loading"
                class="px-4 py-2 text-sm rounded bg-[#BD6F22] text-white hover:bg-[#a95e1d] disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <template x-if="loading">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </template>
                <span 
                    x-text="loading 
                        ? 'Processing...' 
                        : (interviewMode.includes('reschedule') 
                            ? 'Reschedule' 
                            : 'Confirm')">
                </span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    // Philippine Holidays (2025-2027)
    const philippineHolidays = [
        // 2025
        '2025-01-01', // New Year's Day
        '2025-01-25', // Chinese New Year
        '2025-02-25', // EDSA Revolution Anniversary
        '2025-04-09', // Araw ng Kagitingan (Bataan Day)
        '2025-04-17', // Maundy Thursday
        '2025-04-18', // Good Friday
        '2025-04-19', // Black Saturday
        '2025-05-01', // Labor Day
        '2025-06-12', // Independence Day
        '2025-08-21', // Ninoy Aquino Day
        '2025-08-25', // National Heroes Day (Last Monday of August)
        '2025-11-01', // All Saints' Day
        '2025-11-02', // All Souls' Day
        '2025-11-30', // Bonifacio Day
        '2025-12-08', // Feast of the Immaculate Conception
        '2025-12-24', // Christmas Eve (Special Non-Working Day)
        '2025-12-25', // Christmas Day
        '2025-12-26', // Additional Special Day
        '2025-12-30', // Rizal Day
        '2025-12-31', // New Year's Eve (Special Non-Working Day)

        // 2026
        '2026-01-01', // New Year's Day
        '2026-02-14', // Chinese New Year
        '2026-02-25', // EDSA Revolution Anniversary
        '2026-04-02', // Maundy Thursday
        '2026-04-03', // Good Friday
        '2026-04-04', // Black Saturday
        '2026-04-09', // Araw ng Kagitingan (Bataan Day)
        '2026-05-01', // Labor Day
        '2026-06-12', // Independence Day
        '2026-08-21', // Ninoy Aquino Day
        '2026-08-31', // National Heroes Day (Last Monday of August)
        '2026-11-01', // All Saints' Day
        '2026-11-02', // All Souls' Day
        '2026-11-30', // Bonifacio Day
        '2026-12-08', // Feast of the Immaculate Conception
        '2026-12-24', // Christmas Eve (Special Non-Working Day)
        '2026-12-25', // Christmas Day
        '2026-12-26', // Additional Special Day
        '2026-12-30', // Rizal Day
        '2026-12-31', // New Year's Eve (Special Non-Working Day)

        // 2027
        '2027-01-01', // New Year's Day
        '2027-02-06', // Chinese New Year
        '2027-02-25', // EDSA Revolution Anniversary
        '2027-03-25', // Maundy Thursday
        '2027-03-26', // Good Friday
        '2027-03-27', // Black Saturday
        '2027-04-09', // Araw ng Kagitingan (Bataan Day)
        '2027-05-01', // Labor Day
        '2027-06-12', // Independence Day
        '2027-08-21', // Ninoy Aquino Day
        '2027-08-30', // National Heroes Day (Last Monday of August)
        '2027-11-01', // All Saints' Day
        '2027-11-02', // All Souls' Day
        '2027-11-30', // Bonifacio Day
        '2027-12-08', // Feast of the Immaculate Conception
        '2027-12-24', // Christmas Eve (Special Non-Working Day)
        '2027-12-25', // Christmas Day
        '2027-12-26', // Additional Special Day
        '2027-12-30', // Rizal Day
        '2027-12-31'  // New Year's Eve (Special Non-Working Day)
    ];

    // Function to check if a date is a holiday
    window.isPhilippineHoliday = function(dateString) {
        return philippineHolidays.includes(dateString);
    };

    // Add validation to date input on change and blur
    document.addEventListener('DOMContentLoaded', () => {
        // Use MutationObserver to watch for the date input
        const observer = new MutationObserver(() => {
            const dateInput = document.querySelector('input[type="date"][x-model="interviewDate"]');
            if (dateInput) {
                observer.disconnect();

                const validateDate = function(e) {
                    const selectedDate = e.target.value;
                    if (selectedDate && isPhilippineHoliday(selectedDate)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Holiday Selected',
                            text: 'The selected date is a Holiday. Please choose another date.',
                            confirmButtonColor: '#BD6F22'
                        });
                        e.target.value = '';
                        // Trigger Alpine.js update
                        e.target.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                };

                dateInput.addEventListener('change', validateDate);
                dateInput.addEventListener('blur', validateDate);
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
});
</script>
