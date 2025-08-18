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
    <!-- Line Chart -->
    <div class="lg:col-span-2 bg-white p-4 rounded-md shadow-md border border-gray-200 h-80 flex flex-col">
      <div class="flex items-center mb-4 space-x-4">
        <button class="chart-tab active-tab" data-type="job">Jobs Posted</button>
        <button class="chart-tab" data-type="applicants">Applicants</button>
        <button class="chart-tab" data-type="employee">Employees</button>
      </div>
      <div class="flex-1">
        <canvas id="lineChart"></canvas>
      </div>
    </div>

    <!-- Pie Chart -->
    <div class="bg-white p-4 rounded-md shadow-md border border-gray-200 flex flex-col">
      <h1 class="font-semibold text-lg mb-2">Leave forms</h1>
      <div class="flex-1 relative h-64">
        <canvas id="pieChart" class="absolute inset-0 w-full h-full"></canvas>
      </div>

      <!-- Custom Legend -->
      <div class="flex justify-center space-x-10 mt-6 text-sm">
        <div class="flex flex-col items-center">
          <span class="w-3 h-3 rounded-full mb-1" style="background-color: #e6c8a7;"></span>
          <span>Pending</span>
          <span class="font-bold text-base" id="pending-count">{{ $leaveData['pending'] ?? 0 }}</span>
        </div>
        <div class="flex flex-col items-center">
          <span class="w-3 h-3 rounded-full mb-1" style="background-color: #d6a15b;"></span>
          <span>Approved</span>
          <span class="font-bold text-base" id="approved-count">{{ $leaveData['approved'] ?? 0 }}</span>
        </div>
        <div class="flex flex-col items-center">
          <span class="w-3 h-3 rounded-full mb-1" style="background-color: #a85a18;"></span>
          <span>Rejected</span>
          <span class="font-bold text-base" id="rejected-count">{{ $leaveData['rejected'] ?? 0 }}</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

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
#pieChart {
  width: 100% !important;
  height: 100% !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let lineChart;

    // Debug: check what PHP sends
    const data = @json($chartData ?? []);
    const leaveData = @json($leaveData ?? ['pending'=>0, 'approved'=>0, 'rejected'=>0]);
    console.log("Chart Data:", data);
    console.log("Leave Data:", leaveData);

    /* ---------------- Line Chart ---------------- */
    const ctx = document.getElementById('lineChart')?.getContext('2d');

    function generateDatasets(type) {
        return Object.keys(data[type] ?? {}).map((company, index) => ({
            label: company,
            data: data[type][company],
            tension: 0.4,
            fill: false,
            borderWidth: 2,
            borderColor: `hsl(${index * 70}, 70%, 50%)`,
            borderDash: index % 2 === 1 ? [6, 4] : [],
            pointRadius: 3,
            pointHoverRadius: 6
        }));
    }

    function initLineChart(type = 'job') {
        return new Chart(ctx, {
            type: 'line',
            data: { labels: data.labels ?? [], datasets: generateDatasets(type) },
            options: {
                responsive: true,
                plugins: { 
                    legend: { 
                        display: true, 
                        labels: { usePointStyle: true, pointStyle: 'line', color: '#374151', font: { size: 13 } }, 
                        onClick: (e) => e.stopPropagation() 
                    },
                    tooltip: { 
                        backgroundColor: '#111827', 
                        titleColor: '#f9fafb', 
                        bodyColor: '#f9fafb', 
                        padding: 10, 
                        borderColor: '#374151', 
                        borderWidth: 1 
                    }
                },
                interaction: { intersect: false, mode: 'index' },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#6b7280' } },
                    y: { beginAtZero: true, grid: { color: '#e5e7eb' }, ticks: { color: '#6b7280' } }
                }
            }
        });
    }

    if (ctx) {
        lineChart = initLineChart();

        function updateChart(type) {
            document.querySelectorAll('.chart-tab').forEach(b => b.classList.remove('active-tab'));
            document.querySelector(`.chart-tab[data-type="${type}"]`)?.classList.add('active-tab');
            lineChart.destroy();
            lineChart = initLineChart(type);
        }

        document.querySelectorAll('.chart-tab').forEach(btn =>
            btn.addEventListener('click', () => updateChart(btn.dataset.type))
        );
        document.querySelectorAll('.stat-card').forEach(card =>
            card.addEventListener('click', () => updateChart(card.dataset.type))
        );
    }

    /* ---------------- Pie Chart ---------------- */
    const pieCanvas = document.getElementById('pieChart');
    if (pieCanvas) {
        const pieCtx = pieCanvas.getContext('2d');
        const leaveLabels = ['Pending','Approved','Rejected'];
        const leaveCounts = [
            leaveData.pending ?? 0,
            leaveData.approved ?? 0,
            leaveData.rejected ?? 0
        ];

        // Update counts in DOM
        document.getElementById('pending-count').textContent = leaveCounts[0];
        document.getElementById('approved-count').textContent = leaveCounts[1];
        document.getElementById('rejected-count').textContent = leaveCounts[2];

        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: leaveLabels,
                datasets: [{
                    data: leaveCounts,
                    backgroundColor: ['#e6c8a7','#d6a15b','#a85a18'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { 
                        callbacks: {
                            label: function(ctx){
                                const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
                                const val = ctx.raw;
                                const pct = total ? ((val/total)*100).toFixed(1) : 0;
                                return `${ctx.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
