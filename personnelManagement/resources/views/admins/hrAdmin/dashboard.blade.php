@extends('layouts.hrAdmin')

@section('content')
<section class="bg-white font-sans text-gray-800 p-6 min-h-screen">

  <!-- Greeting -->
  <h1 class="mb-2 text-2xl font-bold text-[#BD6F22]">
    Welcome back!
  </h1>
  <hr class="mb-6">

  <!-- Main Layout: Content + Right Sidebar -->
  <div class="flex flex-col lg:flex-row gap-6">

    <!-- Main Content Area (Left Side) -->
    <div class="flex-1 space-y-6">

  <!-- Quick Stats Section -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card">
      <div class="text-3xl font-bold" id="total-jobs">{{ $stats['jobs'] ?? 0 }}</div>
      <div class="text-sm mt-1 text-[#BD6F22]">Total Job Posted</div>
    </div>
    <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card">
      <div class="text-3xl font-bold" id="total-applicants">{{ $stats['applicants'] ?? 0 }}</div>
      <div class="text-sm mt-1 text-[#BD6F22]">Applicants</div>
    </div>
    <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card">
      <div class="text-3xl font-bold" id="total-employees">{{ $stats['employees'] ?? 0 }}</div>
      <div class="text-sm mt-1 text-[#BD6F22]">Employees</div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Application Funnel Chart (60%) -->
    <div class="bg-white p-6 rounded-md shadow-md border border-gray-200">
      <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>
        Application Pipeline
      </h2>
      <div class="h-64">
        <canvas id="funnelChart"></canvas>
      </div>
    </div>

    <!-- Interview Statistics (40%) -->
    <div class="bg-white p-6 rounded-md shadow-md border border-gray-200">
      <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        Interviews
      </h2>
      <div class="h-48 mb-4">
        <canvas id="interviewPieChart"></canvas>
      </div>
      @if($interviewStats['upcoming_7days'] > 0)
      <div class="p-3 bg-amber-50 border-l-4 border-amber-500 rounded text-sm">
        <p class="font-semibold text-amber-800">{{ $interviewStats['upcoming_7days'] }} upcoming interview(s)</p>
        <p class="text-amber-600 text-xs mt-1">in the next 7 days</p>
      </div>
      @endif
    </div>
  </div>

  <!-- Row 3: Monthly Trends (Full Width) -->
  <div class="mb-6">
    <div class="bg-white p-6 rounded-md shadow-md border border-gray-200">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800 flex items-center">
          <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          Monthly Trends
        </h2>
        <div class="flex space-x-2">
          <button class="chart-tab active-tab" data-type="job">Jobs</button>
          <button class="chart-tab" data-type="applicants">Applicants</button>
          <button class="chart-tab" data-type="employee">Employees</button>
        </div>
      </div>
      <div class="h-64">
        <canvas id="lineChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Row 4: Job Insights (Full Width) -->
  <div class="mb-6">
    <div class="bg-white p-6 rounded-md shadow-md border border-gray-200">
      <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        Job Insights
      </h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
        <!-- Job Status Grid -->
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
          <div class="text-3xl font-bold text-green-600">{{ $jobMetrics['active'] ?? 0 }}</div>
          <div class="text-sm text-gray-600 mt-1">Active</div>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
          <div class="text-3xl font-bold text-blue-600">{{ $jobMetrics['filled'] ?? 0 }}</div>
          <div class="text-sm text-gray-600 mt-1">Filled</div>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
          <div class="text-3xl font-bold text-gray-600">{{ $jobMetrics['expired'] ?? 0 }}</div>
          <div class="text-sm text-gray-600 mt-1">Expired</div>
        </div>
        <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
          <div class="text-3xl font-bold text-amber-600">{{ $jobMetrics['expiring_soon'] ?? 0 }}</div>
          <div class="text-sm text-gray-600 mt-1">Expiring Soon</div>
        </div>

        <!-- Fill Rate -->
        <div class="bg-gradient-to-r from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
          <div class="flex items-center justify-between h-full">
            <div>
              <p class="text-sm text-gray-600">Fill Rate</p>
              <p class="text-3xl font-bold text-[#BD6F22]">{{ $jobMetrics['fill_rate'] ?? 0 }}%</p>
            </div>
            <svg class="w-8 h-8 text-[#BD6F22] opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Row 5: Additional Analytics (2 columns) -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top Jobs Chart -->
    <div class="bg-white p-6 rounded-md shadow-md border border-gray-200">
      <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        Top Jobs
      </h2>
      <div class="h-64">
        <canvas id="topJobsChart"></canvas>
      </div>
    </div>

    <!-- Time-to-Hire Card -->
    <div class="bg-white p-6 rounded-md shadow-md border border-gray-200 flex flex-col justify-between">
      <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
          <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Time-to-Hire
        </h2>
        <div class="text-center py-8">
          <div class="text-6xl font-bold text-[#BD6F22] mb-2" id="time-to-hire-display">{{ $timeToHire }}</div>
          <p class="text-gray-600">Average Days</p>
          <p class="text-sm text-gray-500 mt-2">from application to hire</p>
        </div>
      </div>

      <!-- Hiring Efficiency -->
      <div class="mt-4 p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg border border-orange-200">
        <p class="text-sm text-gray-700 font-medium mb-1">Hiring Efficiency</p>
        <p class="text-2xl font-bold text-[#BD6F22]" id="conversion-rate">0%</p>
        <p class="text-xs text-gray-600">conversion rate</p>
      </div>
    </div>
  </div>

    </div>
    <!-- End Main Content Area -->

    <!-- Right Sidebar -->
    <div class="lg:w-96 space-y-6">

      <!-- Notification Section -->
      <div class="sticky top-6">
        <x-shared.notification-section :notifications="$notifications ?? []" userRole="admin" />

        <!-- Filter & Report Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 mt-6">
          <div class="bg-gradient-to-br from-orange-50 to-white px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
              <div class="bg-[#BD6F22] text-white rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-900">Filter & Reports</h2>
                <p class="text-sm text-gray-600">Generate insights</p>
              </div>
            </div>
          </div>

          <div class="p-6">
            <form id="filterForm" method="GET" action="{{ route('hrAdmin.dashboard') }}" class="space-y-4">
              <!-- Company Filter -->
              <div>
                <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                <select name="company" id="company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                  <option value="">All Companies</option>
                  @foreach($chartData['companies'] ?? [] as $company)
                    <option value="{{ $company }}" {{ request('company') == $company ? 'selected' : '' }}>
                      {{ $company }}
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- Start Date -->
              <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
              </div>

              <!-- End Date -->
              <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
              </div>

              <!-- Apply Filters Button -->
              <button type="submit" class="w-full bg-[#BD6F22] hover:bg-[#A55E1A] text-white font-semibold py-2.5 px-4 rounded-md transition-colors duration-200 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Apply Filters
              </button>

              <!-- Generate PDF Button -->
              <button type="button" id="generatePdfBtn" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-md transition-colors duration-200 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Generate PDF
              </button>
            </form>

            <!-- Active Filters Display -->
            @if(request('company') || request('start_date') || request('end_date'))
            <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
              <p class="text-xs font-medium text-blue-800 mb-1">Active Filters:</p>
              <p class="text-xs text-blue-600">
                @if(request('company'))
                  <span class="font-semibold">{{ request('company') }}</span>
                @endif
                @if(request('start_date'))
                  | {{ request('start_date') }}
                @endif
                @if(request('end_date'))
                  to {{ request('end_date') }}
                @endif
              </p>
              <a href="{{ route('hrAdmin.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium mt-2 inline-block">Clear All</a>
            </div>
            @endif
          </div>
        </div>

        <!-- Leave Management -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 mt-6">
          <div class="bg-gradient-to-br from-orange-50 to-white px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
              <div class="bg-[#BD6F22] text-white rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-900">Leave Management</h2>
                <p class="text-sm text-gray-600">Track requests</p>
              </div>
            </div>
          </div>

          <div class="p-6">
            <!-- Leave Status Breakdown -->
            <div class="space-y-4 mb-4">
              <div class="relative">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm font-medium text-gray-700">Pending</span>
                  <span class="text-sm font-bold text-amber-600">{{ $leaveData['pending'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                  <div class="bg-amber-500 h-3 rounded-full transition-all duration-500" id="pending-bar" style="width: 0%"></div>
                </div>
              </div>

              <div class="relative">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm font-medium text-gray-700">Approved</span>
                  <span class="text-sm font-bold text-green-600">{{ $leaveData['approved'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                  <div class="bg-green-500 h-3 rounded-full transition-all duration-500" id="approved-bar" style="width: 0%"></div>
                </div>
              </div>

              <div class="relative">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm font-medium text-gray-700">Rejected</span>
                  <span class="text-sm font-bold text-red-600">{{ $leaveData['rejected'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                  <div class="bg-red-500 h-3 rounded-full transition-all duration-500" id="rejected-bar" style="width: 0%"></div>
                </div>
              </div>
            </div>

            <!-- Total Leave Requests -->
            <div class="p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Total Requests</p>
                  <p class="text-3xl font-bold text-[#BD6F22]" id="total-leaves">0</p>
                </div>
                <svg class="w-10 h-10 text-[#BD6F22] opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <!-- End Right Sidebar -->

  </div>
  <!-- End Main Layout -->

</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<style>
.stat-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.chart-tab {
  background: none;
  border: none;
  font-weight: 500;
  cursor: pointer;
  color: #6b7280;
  padding: 0.25rem 0.75rem;
  font-size: 0.875rem;
  border-radius: 0.375rem;
  transition: all 0.2s;
}
.chart-tab:hover {
  background-color: #f3f4f6;
  color: #BD6F22;
}
.active-tab {
  color: #BD6F22;
  font-weight: 600;
  background-color: #FEF3E2;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ============================================
    // GET DATA FROM PHP
    // ============================================
    const stats = @json($stats ?? ['jobs'=>0, 'applicants'=>0, 'employees'=>0]);
    const leaveData = @json($leaveData ?? ['pending'=>0, 'approved'=>0, 'rejected'=>0]);
    const chartData = @json($chartData ?? ['job'=>[], 'applicants'=>[], 'employee'=>[]]);
    const pipelineFunnel = @json($pipelineFunnel ?? []);
    const interviewStats = @json($interviewStats ?? []);
    const topJobs = @json($topJobs ?? []);

    console.log("Pipeline Funnel:", pipelineFunnel);
    console.log("Interview Stats:", interviewStats);
    console.log("Top Jobs:", topJobs);

    // ============================================
    // ANALYTICS CALCULATIONS
    // ============================================

    // Calculate Hiring Efficiency
    const conversionRate = stats.applicants > 0
        ? ((stats.employees / stats.applicants) * 100).toFixed(1)
        : 0;
    document.getElementById('conversion-rate').textContent = conversionRate + '%';

    // Calculate Leave Statistics
    const totalLeaves = leaveData.pending + leaveData.approved + leaveData.rejected;
    document.getElementById('total-leaves').textContent = totalLeaves;

    if (totalLeaves > 0) {
        const pendingPct = ((leaveData.pending / totalLeaves) * 100).toFixed(1);
        const approvedPct = ((leaveData.approved / totalLeaves) * 100).toFixed(1);
        const rejectedPct = ((leaveData.rejected / totalLeaves) * 100).toFixed(1);

        document.getElementById('pending-bar').style.width = pendingPct + '%';
        document.getElementById('approved-bar').style.width = approvedPct + '%';
        document.getElementById('rejected-bar').style.width = rejectedPct + '%';
    }

    // ============================================
    // CHART 1: APPLICATION PIPELINE FUNNEL
    // ============================================
    const funnelCtx = document.getElementById('funnelChart')?.getContext('2d');
    if (funnelCtx) {
        // Define funnel stages in order
        const funnelStages = ['pending', 'approved', 'for_interview', 'interviewed', 'hired'];
        const funnelLabels = ['Pending', 'Approved', 'Interview', 'Interviewed', 'Hired'];
        const funnelData = funnelStages.map(stage => pipelineFunnel[stage] || 0);

        new Chart(funnelCtx, {
            type: 'bar',
            data: {
                labels: funnelLabels,
                datasets: [{
                    label: 'Applications',
                    data: funnelData,
                    backgroundColor: [
                        '#FEF3E2',
                        '#F8DDB4',
                        '#F2C686',
                        '#EBAF58',
                        '#D6A15B',
                        '#BD6F22'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return 'Applications: ' + ctx.raw;
                            }
                        }
                    }
                },
                scales: {
                    x: { beginAtZero: true, grid: { display: false } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    // ============================================
    // CHART 2: INTERVIEW PIE CHART
    // ============================================
    const interviewPieCtx = document.getElementById('interviewPieChart')?.getContext('2d');
    if (interviewPieCtx) {
        new Chart(interviewPieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Scheduled', 'Rescheduled', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        interviewStats.scheduled || 0,
                        interviewStats.rescheduled || 0,
                        interviewStats.completed || 0,
                        interviewStats.cancelled || 0
                    ],
                    backgroundColor: ['#3B82F6', '#F59E0B', '#10B981', '#EF4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 10, font: { size: 11 } }
                    }
                }
            }
        });
    }

    // ============================================
    // CHART 3: MONTHLY TRENDS LINE CHART
    // ============================================
    let lineChart;
    const lineCtx = document.getElementById('lineChart')?.getContext('2d');

    function generateDatasets(type) {
        return Object.keys(chartData[type] ?? {}).map((company, index) => ({
            label: company,
            data: chartData[type][company],
            tension: 0.4,
            fill: false,
            borderWidth: 2,
            borderColor: `hsl(${index * 70}, 70%, 50%)`,
            pointRadius: 3,
            pointHoverRadius: 6
        }));
    }

    function initLineChart(type = 'job') {
        return new Chart(lineCtx, {
            type: 'line',
            data: { labels: chartData.labels ?? [], datasets: generateDatasets(type) },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: { usePointStyle: true, pointStyle: 'circle', font: { size: 11 } }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#e5e7eb' } }
                }
            }
        });
    }

    if (lineCtx) {
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
    }

    // ============================================
    // CHART 4: TOP JOBS BAR CHART
    // ============================================
    const topJobsCtx = document.getElementById('topJobsChart')?.getContext('2d');
    if (topJobsCtx) {
        const topJobLabels = topJobs.map(job => job.title.length > 20 ? job.title.substring(0, 20) + '...' : job.title);
        const topJobCounts = topJobs.map(job => job.count);

        new Chart(topJobsCtx, {
            type: 'bar',
            data: {
                labels: topJobLabels,
                datasets: [{
                    label: 'Applications',
                    data: topJobCounts,
                    backgroundColor: '#BD6F22',
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
                            title: function(ctx) {
                                return topJobs[ctx[0].dataIndex].title;
                            },
                            label: function(ctx) {
                                return 'Applications: ' + ctx.raw;
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#e5e7eb' } }
                }
            }
        });
    }

    // ============================================
    // ANIMATIONS
    // ============================================
    function animateValue(element, start, end, duration) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                element.textContent = Math.round(end);
                clearInterval(timer);
            } else {
                element.textContent = Math.round(current);
            }
        }, 16);
    }

    setTimeout(() => {
        animateValue(document.getElementById('total-leaves'), 0, totalLeaves, 1000);
    }, 200);

    // ============================================
    // PDF GENERATION
    // ============================================
    document.getElementById('generatePdfBtn')?.addEventListener('click', function() {
        // Get current filter values
        const company = document.getElementById('company').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        // Build query string
        const params = new URLSearchParams();
        if (company) params.append('company', company);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Open PDF in new tab
        const url = '{{ route("hrAdmin.dashboard.report") }}' + (params.toString() ? '?' + params.toString() : '');
        window.open(url, '_blank');
    });
});
</script>
@endsection
