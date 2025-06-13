@extends('layouts.employeeHome')

@section('content')
<!-- Greeting -->
<h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">
    Welcome back, {{ Auth::user()->first_name }}!
</h1>
<hr>

<input type="hidden" id="isProfileIncomplete" value="{{ auth()->user()->is_profile_complete ? '0' : '1' }}">

<!-- Main Section -->
<section class="bg-white font-sans text-gray-800 p-6 min-h-screen">

    <!-- Calendar -->
    <div id="calendar" class="mt-6 flex justify-center">
        <!-- Calendar will be generated here by JavaScript -->
    </div>

</section>

<!-- Styles (from HR admin) -->
<style>
    @keyframes pulse-outline {
        0% {
            box-shadow: 0 0 0 0 rgba(189, 111, 34, 0.4);
        }
        70% {
            box-shadow: 0 0 0 6px rgba(189, 111, 34, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(189, 111, 34, 0);
        }
    }

    .calendar {
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    .calendar-header {
        font-weight: bold;
        margin-bottom: 1rem;
        color: #BD6F22;
        font-size: 1.1rem;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.5rem;
        text-align: center;
    }

    .calendar-day,
    .calendar-date {
        font-size: 0.95rem;
    }

    .calendar-day {
        font-weight: bold;
        color: #555;
        padding: 0.5rem;
    }

    .calendar-date {
        width: 2.2rem;
        height: 2.2rem;
        border-radius: 50%;
        line-height: 2.2rem;
        margin: auto;
        transition: background-color 0.3s ease, color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .calendar-date:hover {
        background-color: #f2f2f2;
    }

    .calendar-date.current-date.today-outline {
        background-color: white;
        color: #BD6F22;
        border: 2px solid #BD6F22;
        font-weight: bold;
        animation: pulse-outline 2s infinite;
    }

    .current-date {
        background-color: #BD6F22;
        color: white;
    }

    .calendar-date.today-outline {
        border: 2px solid #BD6F22;
        color: #BD6F22;
    }
</style>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Calendar Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendar = document.getElementById('calendar');

        const now = new Date();
        const currentMonth = now.getMonth();
        const currentYear = now.getFullYear();
        const currentDate = now.getDate();

        const monthNames = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        const dayNames = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const lastDate = new Date(currentYear, currentMonth + 1, 0).getDate();

        let html = `
            <div class="calendar mx-auto">
                <div class="calendar-header">${now.toLocaleDateString('en-US', { weekday: 'short' })}, ${monthNames[currentMonth]} ${currentDate}</div>
                <div class="calendar-grid">
                    ${dayNames.map(day => `<div class="calendar-day">${day}</div>`).join('')}
        `;

        for (let i = 0; i < firstDay; i++) {
            html += `<div></div>`;
        }

        for (let i = 1; i <= lastDate; i++) {
            let classList = 'calendar-date';
            if (i === currentDate) {
                classList += ' current-date today-outline';
            }

            html += `<div class="${classList}">${i}</div>`;
        }

        html += '</div></div>';
        calendar.innerHTML = html;

        // Profile incomplete check
        const isProfileIncomplete = document.getElementById('isProfileIncomplete').value === '1';
        if (isProfileIncomplete) {
            Swal.fire({
                title: 'Complete Your Profile',
                text: "Please complete your profile to apply for jobs.",
                icon: 'warning',
                confirmButtonColor: '#BD6F22',
                confirmButtonText: 'Go to Profile',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('employee.profile') }}";
                }
            });
        }
    });
</script>
@endsection
