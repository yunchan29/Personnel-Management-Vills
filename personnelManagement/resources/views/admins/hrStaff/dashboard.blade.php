@extends('layouts.hrStaff')

@section('content')
<section class="bg-white font-sans text-gray-800 p-6 min-h-screen">

  <!-- Greeting -->
  <h1 class="mb-2 text-2xl font-bold text-[#BD6F22]">
    Welcome back, {{ Auth::user()->first_name }}!
  </h1>
  <hr class="mb-6">

  <!-- Main Layout: Content + Right Sidebar -->
  <div class="flex flex-col lg:flex-row gap-6">

    <!-- Main Content Area (Left Side) -->
    <div class="flex-1 space-y-6">

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card">
          <div class="flex items-center justify-center mb-2">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <div class="text-3xl font-bold text-gray-800" id="pending-eval-count">{{ $pendingEvaluationCount ?? 0 }}</div>
          <div class="text-sm mt-1 text-[#BD6F22] font-medium">Pending Evaluations</div>
        </div>

        <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card">
          <div class="flex items-center justify-center mb-2">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="text-3xl font-bold text-gray-800" id="avg-eval-score">0</div>
          <div class="text-sm mt-1 text-[#BD6F22] font-medium">Avg Evaluation Score</div>
          <div class="text-xs text-gray-500 mt-1">This Month</div>
        </div>

        <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card">
          <div class="flex items-center justify-center mb-2">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
          <div class="text-3xl font-bold text-gray-800" id="pass-rate-stat">0<span class="text-xl">%</span></div>
          <div class="text-sm mt-1 text-[#BD6F22] font-medium">Pass Rate</div>
          <div class="text-xs text-gray-500 mt-1">This Month</div>
        </div>

        <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card">
          <div class="flex items-center justify-center mb-2">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div class="text-3xl font-bold text-gray-800" id="promotions-count">0</div>
          <div class="text-sm mt-1 text-[#BD6F22] font-medium">Promotions</div>
          <div class="text-xs text-gray-500 mt-1">This Month</div>
        </div>
      </div>

      <!-- Calendar -->
      <div class="bg-white p-6 rounded-md shadow-md border border-gray-200 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
          <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          Calendar
        </h2>
        <div id="calendar">
          <!-- Calendar will be generated here by JavaScript -->
        </div>
      </div>

      <!-- Training & Evaluation Overview and Score Breakdown -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Training & Evaluation Overview -->
        <div class="bg-white p-6 rounded-md shadow-md border border-gray-200">
          <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Training & Evaluation
          </h2>

          <!-- Pass Rate Gauge -->
          <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-gray-700">Pass Rate</span>
              <span class="text-lg font-bold text-green-600" id="training-pass-rate">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div class="bg-green-500 h-3 rounded-full transition-all duration-500" id="training-pass-bar" style="width: 0%"></div>
            </div>
          </div>

          <!-- Average Score -->
          <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-gray-700">Average Score</span>
              <span class="text-lg font-bold text-[#BD6F22]" id="avg-score">0</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div class="bg-[#BD6F22] h-3 rounded-full transition-all duration-500" id="avg-score-bar" style="width: 0%"></div>
            </div>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-2 gap-2 mt-4">
            <div class="bg-green-50 p-2 rounded text-center border border-green-200">
              <div class="text-xl font-bold text-green-600" id="training-passed">0</div>
              <div class="text-xs text-gray-600">Passed</div>
            </div>
            <div class="bg-red-50 p-2 rounded text-center border border-red-200">
              <div class="text-xl font-bold text-red-600" id="training-failed">0</div>
              <div class="text-xs text-gray-600">Failed</div>
            </div>
          </div>
        </div>

        <!-- Evaluation Score Breakdown -->
        <div class="bg-white p-6 rounded-md shadow-md border border-gray-200">
          <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Score Breakdown
          </h2>

          <!-- Score Categories -->
          <div class="space-y-3">
            <!-- Knowledge & Understanding -->
            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-medium text-gray-700">Knowledge & Understanding</span>
                <span class="text-sm font-bold text-blue-600"><span id="knowledge-score">0</span>/30</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" id="knowledge-bar" style="width: 0%"></div>
              </div>
            </div>

            <!-- Skill Application -->
            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-medium text-gray-700">Skill Application</span>
                <span class="text-sm font-bold text-indigo-600"><span id="skill-score">0</span>/30</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500" id="skill-bar" style="width: 0%"></div>
              </div>
            </div>

            <!-- Participation & Engagement -->
            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-medium text-gray-700">Participation & Engagement</span>
                <span class="text-sm font-bold text-purple-600"><span id="participation-score">0</span>/20</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" id="participation-bar" style="width: 0%"></div>
              </div>
            </div>

            <!-- Professionalism & Attitude -->
            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-medium text-gray-700">Professionalism & Attitude</span>
                <span class="text-sm font-bold text-pink-600"><span id="professionalism-score">0</span>/20</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-pink-500 h-2 rounded-full transition-all duration-500" id="professionalism-bar" style="width: 0%"></div>
              </div>
            </div>
          </div>

          <!-- Total Score Summary -->
          <div class="mt-4 p-3 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200">
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-gray-700">Average Total</span>
              <span class="text-2xl font-bold text-[#BD6F22]"><span id="total-score-display">0</span>/100</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Promotion Pipeline -->
      <div class="bg-white p-6 rounded-md shadow-md border border-gray-200 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
          <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          Promotion Pipeline
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Evaluated -->
          <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="text-2xl font-bold text-blue-600" id="pipeline-evaluated">0</div>
            <div class="text-sm text-gray-600 mt-1">Evaluated</div>
            <div class="text-xs text-gray-500 mt-1">Total evaluated</div>
          </div>

          <!-- Passed -->
          <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="text-2xl font-bold text-green-600" id="pipeline-passed">0</div>
            <div class="text-sm text-gray-600 mt-1">Passed</div>
            <div class="text-xs text-gray-500 mt-1">Ready for invitation</div>
          </div>

          <!-- Invited -->
          <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
            <div class="text-2xl font-bold text-amber-600" id="pipeline-invited">0</div>
            <div class="text-sm text-gray-600 mt-1">Invited</div>
            <div class="text-xs text-gray-500 mt-1">Signing scheduled</div>
          </div>

          <!-- Promoted -->
          <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <div class="text-2xl font-bold text-purple-600" id="pipeline-promoted">0</div>
            <div class="text-sm text-gray-600 mt-1">Promoted</div>
            <div class="text-xs text-gray-500 mt-1">Now employees</div>
          </div>
        </div>
      </div>

    </div>
    <!-- End Main Content Area -->

    <!-- Right Sidebar -->
    <div class="lg:w-96 space-y-6">

      <div class="sticky top-6">
        <!-- Notification Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
          <div class="bg-gradient-to-br from-orange-50 to-white px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
              <div class="bg-[#BD6F22] text-white rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-900">Notifications</h2>
                <p class="text-sm text-gray-600">Recent updates</p>
              </div>
            </div>
          </div>

          <div class="p-6 max-h-96 overflow-y-auto">
            <div class="space-y-3" id="notifications-container">
              <!--
                Notifications will be dynamically loaded here:
                - Urgent (Red): Training ended >7 days ago, not evaluated
                - Important (Orange): Training ending within 3 days
                - Info (Blue): Passed applicants awaiting invitation
              -->
              <div id="no-notifications" class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-center text-sm text-gray-500">
                No pending evaluations
              </div>
            </div>
          </div>
        </div>

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
            <form id="filterForm" class="space-y-4">
              <!-- Report Type -->
              <div>
                <label for="report_type" class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                <select name="report_type" id="report_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                  <option value="training_evaluation">Training Evaluation Report</option>
                  <option value="employee_promotion">Employee Promotion Report</option>
                  <option value="training_completion">Training Completion Report</option>
                  <option value="requirements_status">Requirements Status Report</option>
                </select>
              </div>

              <!-- Company Filter -->
              <div>
                <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                <select name="company" id="company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                  <option value="">All Companies</option>
                  <!-- TODO: Populate companies from database -->
                </select>
              </div>

              <!-- Job Position Filter -->
              <div>
                <label for="job_position" class="block text-sm font-medium text-gray-700 mb-2">Job Position</label>
                <select name="job_position" id="job_position" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                  <option value="">All Positions</option>
                  <!-- TODO: Populate positions from database -->
                </select>
              </div>

              <!-- Evaluation Status Filter -->
              <div>
                <label for="eval_status" class="block text-sm font-medium text-gray-700 mb-2">Evaluation Status</label>
                <select name="eval_status" id="eval_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                  <option value="">All Statuses</option>
                  <option value="passed">Passed (≥70)</option>
                  <option value="failed">Failed (<70)</option>
                  <option value="pending">Pending Evaluation</option>
                </select>
              </div>

              <!-- Date Range -->
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label for="start_date" class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                  <input type="date" name="start_date" id="start_date" class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                </div>
                <div>
                  <label for="end_date" class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                  <input type="date" name="end_date" id="end_date" class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                </div>
              </div>

              <!-- Score Range -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Score Range</label>
                <div class="grid grid-cols-2 gap-2">
                  <input type="number" name="min_score" id="min_score" min="0" max="100" placeholder="Min" class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                  <input type="number" name="max_score" id="max_score" min="0" max="100" placeholder="Max" class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                </div>
              </div>

              <!-- Quick Filters -->
              <div class="pt-2 border-t border-gray-200">
                <label class="block text-xs font-medium text-gray-700 mb-2">Quick Filters</label>
                <div class="flex flex-wrap gap-2">
                  <button type="button" class="quick-filter px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors" data-filter="this_week">This Week</button>
                  <button type="button" class="quick-filter px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors" data-filter="this_month">This Month</button>
                  <button type="button" class="quick-filter px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors" data-filter="last_month">Last Month</button>
                </div>
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
                Generate PDF Report
              </button>

              <!-- Reset Filters -->
              <button type="button" id="resetFilters" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md transition-colors duration-200 text-sm">
                Reset Filters
              </button>
            </form>
          </div>
        </div>

      </div>

    </div>
    <!-- End Right Sidebar -->

  </div>
  <!-- End Main Layout -->

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

  .stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .calendar {
    width: 100%;
    max-width: 100%;
    text-align: center;
  }

  .calendar-header {
    font-weight: bold;
    margin-bottom: 1rem;
    color: #BD6F22;
    font-size: 1rem;
  }

  .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.3rem;
    text-align: center;
  }

  .calendar-day,
  .calendar-date {
    font-size: 0.85rem;
  }

  .calendar-day {
    font-weight: bold;
    color: #555;
    padding: 0.3rem;
  }

  .calendar-date {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    line-height: 2rem;
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


<script>
document.addEventListener('DOMContentLoaded', function () {
    // ============================================
    // GET DATA FROM PHP (TODO: Pass from controller)
    // ============================================
    const trainingStats = {
        passed: 0,
        failed: 0,
        total_evaluations: 0,
        avg_score: 0,
        avg_knowledge: 0,
        avg_skill: 0,
        avg_participation: 0,
        avg_professionalism: 0
    };

    const pipelineStats = {
        evaluated: 0,
        passed: 0,
        invited: 0,
        promoted: 0
    };

    const notifications = []; // TODO: Pass from controller

    // ============================================
    // CALENDAR GENERATION
    // ============================================
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

    // ============================================
    // KPI STAT CARDS
    // ============================================
    // Average Evaluation Score (This Month)
    document.getElementById('avg-eval-score').textContent = trainingStats.avg_score || 0;

    // Pass Rate (This Month)
    const totalEvaluations = trainingStats.total_evaluations || 0;
    const passRate = totalEvaluations > 0
        ? ((trainingStats.passed / totalEvaluations) * 100).toFixed(1)
        : 0;
    document.getElementById('pass-rate-stat').innerHTML = passRate + '<span class="text-xl">%</span>';

    // Promotions Count (This Month)
    document.getElementById('promotions-count').textContent = pipelineStats.promoted || 0;

    // ============================================
    // TRAINING & EVALUATION OVERVIEW
    // ============================================
    document.getElementById('training-pass-rate').textContent = passRate + '%';
    document.getElementById('training-pass-bar').style.width = passRate + '%';

    document.getElementById('avg-score').textContent = trainingStats.avg_score || 0;
    document.getElementById('avg-score-bar').style.width = (trainingStats.avg_score || 0) + '%';

    document.getElementById('training-passed').textContent = trainingStats.passed || 0;
    document.getElementById('training-failed').textContent = trainingStats.failed || 0;

    // ============================================
    // EVALUATION SCORE BREAKDOWN
    // ============================================
    // Knowledge & Understanding (out of 30)
    const knowledgePercent = trainingStats.avg_knowledge > 0
        ? ((trainingStats.avg_knowledge / 30) * 100).toFixed(1)
        : 0;
    document.getElementById('knowledge-score').textContent = (trainingStats.avg_knowledge || 0).toFixed(1);
    document.getElementById('knowledge-bar').style.width = knowledgePercent + '%';

    // Skill Application (out of 30)
    const skillPercent = trainingStats.avg_skill > 0
        ? ((trainingStats.avg_skill / 30) * 100).toFixed(1)
        : 0;
    document.getElementById('skill-score').textContent = (trainingStats.avg_skill || 0).toFixed(1);
    document.getElementById('skill-bar').style.width = skillPercent + '%';

    // Participation & Engagement (out of 20)
    const participationPercent = trainingStats.avg_participation > 0
        ? ((trainingStats.avg_participation / 20) * 100).toFixed(1)
        : 0;
    document.getElementById('participation-score').textContent = (trainingStats.avg_participation || 0).toFixed(1);
    document.getElementById('participation-bar').style.width = participationPercent + '%';

    // Professionalism & Attitude (out of 20)
    const professionalismPercent = trainingStats.avg_professionalism > 0
        ? ((trainingStats.avg_professionalism / 20) * 100).toFixed(1)
        : 0;
    document.getElementById('professionalism-score').textContent = (trainingStats.avg_professionalism || 0).toFixed(1);
    document.getElementById('professionalism-bar').style.width = professionalismPercent + '%';

    // Total Score Display
    document.getElementById('total-score-display').textContent = (trainingStats.avg_score || 0).toFixed(1);

    // ============================================
    // PROMOTION PIPELINE
    // ============================================
    document.getElementById('pipeline-evaluated').textContent = pipelineStats.evaluated || 0;
    document.getElementById('pipeline-passed').textContent = pipelineStats.passed || 0;
    document.getElementById('pipeline-invited').textContent = pipelineStats.invited || 0;
    document.getElementById('pipeline-promoted').textContent = pipelineStats.promoted || 0;

    // ============================================
    // NOTIFICATIONS (Dynamic)
    // ============================================
    function renderNotifications() {
        const container = document.getElementById('notifications-container');
        const noNotif = document.getElementById('no-notifications');

        if (notifications.length === 0) {
            noNotif.style.display = 'block';
            return;
        }

        noNotif.style.display = 'none';
        let html = '';

        notifications.forEach(notif => {
            let badgeColor = 'bg-blue-100 text-blue-800';
            let borderColor = 'border-blue-200';

            if (notif.priority === 'urgent') {
                badgeColor = 'bg-red-100 text-red-800';
                borderColor = 'border-red-300';
            } else if (notif.priority === 'important') {
                badgeColor = 'bg-amber-100 text-amber-800';
                borderColor = 'border-amber-300';
            }

            html += `
                <div class="p-3 bg-gray-50 rounded-lg border ${borderColor} hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-2">
                        <span class="${badgeColor} text-xs px-2 py-1 rounded font-medium">${notif.type}</span>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-800">${notif.name} - ${notif.position}</p>
                            <p class="text-xs text-gray-600 mt-1">${notif.message}</p>
                            ${notif.action ? `<button class="text-xs text-[#BD6F22] hover:underline mt-1">${notif.action}</button>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    renderNotifications();

    // ============================================
    // FILTER & REPORT FUNCTIONALITY
    // ============================================

    // Quick Filter Buttons
    document.querySelectorAll('.quick-filter').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            const today = new Date();
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            if (filter === 'this_week') {
                const firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
                const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                startDate.value = firstDay.toISOString().split('T')[0];
                endDate.value = lastDay.toISOString().split('T')[0];
            } else if (filter === 'this_month') {
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                startDate.value = firstDay.toISOString().split('T')[0];
                endDate.value = lastDay.toISOString().split('T')[0];
            } else if (filter === 'last_month') {
                const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
                startDate.value = firstDay.toISOString().split('T')[0];
                endDate.value = lastDay.toISOString().split('T')[0];
            }
        });
    });

    // Apply Filters
    document.getElementById('filterForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }

        // TODO: Implement actual filter functionality
        console.log('Filters applied:', Object.fromEntries(params));
        alert('Filter functionality will be implemented in the backend.');
    });

    // Generate PDF Report
    document.getElementById('generatePdfBtn')?.addEventListener('click', function() {
        const reportType = document.getElementById('report_type').value;
        const company = document.getElementById('company').value;
        const position = document.getElementById('job_position').value;
        const status = document.getElementById('eval_status').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        const params = new URLSearchParams();
        if (company) params.append('company', company);
        if (position) params.append('position', position);
        if (status) params.append('status', status);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // TODO: Implement PDF generation route
        const url = `/hrStaff/reports/${reportType}/pdf?` + params.toString();
        console.log('Generating PDF:', url);
        alert('PDF generation will be implemented. Report type: ' + reportType);
        // window.open(url, '_blank');
    });

    // Reset Filters
    document.getElementById('resetFilters')?.addEventListener('click', function() {
        document.getElementById('filterForm').reset();
    });
});
</script>

@endsection
