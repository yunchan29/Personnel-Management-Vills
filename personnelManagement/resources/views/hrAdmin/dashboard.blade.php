@extends('layouts.hrAdmin')

@section('content')
<section class="bg-white font-sans text-gray-800 p-6 min-h-screen">
  <input type="hidden" id="isProfileIncomplete" value="{{ auth()->user()->is_profile_complete ? '0' : '1' }}">

  <!-- Greeting -->
  <h1 class="mb-2 text-2xl font-bold text-[#BD6F22]">
    Welcome back!
  </h1>
  <hr class="mb-6">

  <!-- Stat Cards -->
  <div class="flex justify-center mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl w-full">
      <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card cursor-pointer" data-type="job">
        <div class="text-3xl font-bold" id="total-jobs">{{ $stats['jobs'] ?? 0 }}</div>
        <div class="text-sm mt-1 text-[#BD6F22]">Total Job Posted</div>
      </div>
      <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card cursor-pointer" data-type="applicants">
        <div class="text-3xl font-bold" id="total-applicants">{{ $stats['applicants'] ?? 0 }}</div>
        <div class="text-sm mt-1 text-[#BD6F22]">Applicants</div>
      </div>
      <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card cursor-pointer" data-type="employee">
        <div class="text-3xl font-bold" id="total-employees">{{ $stats['employees'] ?? 0 }}</div>
        <div class="text-sm mt-1 text-[#BD6F22]">Employees</div>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white p-4 rounded-md shadow-md border border-gray-200">
      <!-- Chart Tabs -->
      <div class="flex items-center mb-4 space-x-4">
        <button class="chart-tab active-tab" data-type="job">Jobs Posted</button>
        <button class="chart-tab" data-type="applicants">Applicants</button>
        <button class="chart-tab" data-type="employee">Employees</button>
      </div>
      <canvas id="lineChart" height="100"></canvas>
    </div>

    <!-- Pie Chart -->
    <div class="bg-white p-4 rounded-md shadow-md border border-gray-200">
      <canvas id="pieChart" height="200"></canvas>
      <div class="flex justify-center space-x-6 mt-4 text-sm">
        <div class="flex items-center space-x-2">
          <span class="w-3 h-3 rounded-full" style="background-color: #d97706;"></span>
          <span>Pending</span>
          <span class="font-bold">20</span>
        </div>
        <div class="flex items-center space-x-2">
          <span class="w-3 h-3 rounded-full" style="background-color: #f59e0b;"></span>
          <span>Approved</span>
          <span class="font-bold">8</span>
        </div>
        <div class="flex items-center space-x-2">
          <span class="w-3 h-3 rounded-full" style="background-color: #b45309;"></span>
          <span>Rejected</span>
          <span class="font-bold">12</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let lineChart;

    // Chart data from Laravel
    const data = @json($chartData);

    const ctx = document.getElementById('lineChart').getContext('2d');

    // Generate datasets dynamically with style
    function generateDatasets(type) {
        return Object.keys(data[type]).map((company, index) => ({
            label: company,
            data: data[type][company],
            tension: 0.4,
            fill: false,
            borderWidth: 2,
            borderColor: `hsl(${index * 60}, 70%, 50%)`,
            borderDash: index === 1 ? [6, 4] : [], // dashed for 2nd dataset
            pointRadius: 0 // cleaner lines
        }));
    }

    // Default chart
    lineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: generateDatasets('job')
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { 
                    display: true,
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'line',
                        color: '#4b5563',
                        font: { size: 14 }
                    },
                    onClick: (e) => e.stopPropagation() // prevent crossing out datasets
                } 
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#9ca3af' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { color: '#9ca3af' }
                }
            }
        }
    });

    // Update chart
    function updateChart(type) {
        document.querySelectorAll('.chart-tab').forEach(b => b.classList.remove('active-tab'));
        document.querySelector(`.chart-tab[data-type="${type}"]`)?.classList.add('active-tab');

        lineChart.data.datasets = generateDatasets(type);
        lineChart.update();
    }

    document.querySelectorAll('.chart-tab').forEach(btn =>
        btn.addEventListener('click', () => updateChart(btn.dataset.type))
    );

    document.querySelectorAll('.stat-card').forEach(card =>
        card.addEventListener('click', () => updateChart(card.dataset.type))
    );

    // Static Pie Chart
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Rejected'],
            datasets: [{
                data: [20, 8, 12],
                backgroundColor: ['#d97706', '#f59e0b', '#b45309']
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
});
</script>

<style>
.chart-tab {
  background: none;
  border: none;
  font-weight: 500;
  cursor: pointer;
  color: #6b7280;
  padding-bottom: 0.25rem;
}
.chart-tab:hover { color: #BD6F22; }
.active-tab {
  color: #BD6F22;
  font-weight: 600;
  border-bottom: 2px solid #BD6F22;
}
.stat-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection
