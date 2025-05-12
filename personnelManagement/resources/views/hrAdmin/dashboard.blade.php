@extends('layouts.hrAdmin')

@section('content')
<section class="bg-white font-sans text-gray-800 p-6 min-h-screen">

  <!-- Greeting -->
  <h1 class="text-xl font-semibold mb-4" style="color: #BD6F22;">Welcome back!</h1>

  <!-- Stat Cards (Centered) -->
  <div class="flex justify-center mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl w-full">
      <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200">
        <div class="text-3xl font-bold">2</div>
        <div class="text-sm mt-1" style="color: #BD6F22;">Total Job Posted</div>
      </div>
      <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200">
        <div class="text-3xl font-bold">5</div>
        <div class="text-sm mt-1" style="color: #BD6F22;">Applicants</div>
      </div>
      <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200">
        <div class="text-3xl font-bold">2</div>
        <div class="text-sm mt-1" style="color: #BD6F22;">Employees</div>
      </div>
    </div>
  </div>

  <!-- Calendar -->
  <div id="calendar" class="mt-6 flex justify-center">
    <!-- Calendar will be generated here by JavaScript -->
  </div>

</section>

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

    // First day of the month
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    // Last date of the month
    const lastDate = new Date(currentYear, currentMonth + 1, 0).getDate();

    let html = `
      <div class="calendar mx-auto">
        <div class="calendar-header">${now.toLocaleDateString('en-US', { weekday: 'short' })}, ${monthNames[currentMonth]} ${currentDate}</div>
        <div class="calendar-grid">
          ${dayNames.map(day => `<div class="calendar-day">${day}</div>`).join('')}
    `;

    // Blank days before first day
    for (let i = 0; i < firstDay; i++) {
      html += `<div></div>`;
    }

    // Fill the days
    for (let i = 1; i <= lastDate; i++) {
      let classList = 'calendar-date';
      if (i === currentDate) {
        classList += ' current-date today-outline';
      }

      html += `<div class="${classList}">${i}</div>`;
    }

    html += '</div></div>';
    calendar.innerHTML = html;
  });
</script>

@endsection
