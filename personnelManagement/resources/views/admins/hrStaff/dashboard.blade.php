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
          <div class="text-3xl font-bold text-gray-800" id="avg-eval-score">{{ $averageEvaluationScore ?? 0 }}</div>
          <div class="text-sm mt-1 text-[#BD6F22] font-medium">Avg Evaluation Score</div>
          <div class="text-xs text-gray-500 mt-1">This Month</div>
        </div>

        <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card">
          <div class="flex items-center justify-center mb-2">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
          <div class="text-3xl font-bold text-gray-800" id="pass-rate-stat">{{ $passRateThisMonth ?? 0 }}<span class="text-xl">%</span></div>
          <div class="text-sm mt-1 text-[#BD6F22] font-medium">Pass Rate</div>
          <div class="text-xs text-gray-500 mt-1">This Month</div>
        </div>

        <div class="bg-white shadow-md rounded-md p-4 text-center border border-gray-200 stat-card">
          <div class="flex items-center justify-center mb-2">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div class="text-3xl font-bold text-gray-800" id="promotions-count">{{ $promotionsThisMonth ?? 0 }}</div>
          <div class="text-sm mt-1 text-[#BD6F22] font-medium">Promotions</div>
          <div class="text-xs text-gray-500 mt-1">This Month</div>
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
              <div class="flex-1">
                <div class="flex items-center gap-2">
                  <h2 class="text-xl font-bold text-gray-900">Notifications</h2>
                  @if(isset($notifications) && count($notifications) > 0)
                    <span class="bg-[#BD6F22] text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count($notifications) }}</span>
                  @endif
                </div>
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
              <div id="no-notifications" class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-gray-500 font-medium">All caught up!</p>
                <p class="text-xs text-gray-400 mt-1">No pending notifications at this time</p>
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
                  <option value="requirements_status">Requirements Status Report</option>
                </select>
              </div>

              <!-- Company Filter -->
              <div>
                <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                <select name="company" id="company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                  <option value="">All Companies</option>
                  @if(isset($companies))
                    @foreach($companies as $company)
                      <option value="{{ $company }}">{{ $company }}</option>
                    @endforeach
                  @endif
                </select>
              </div>

              <!-- Job Position Filter -->
              <div>
                <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Job Position</label>
                <select name="position" id="position" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                  <option value="">All Positions</option>
                  @if(isset($positions))
                    @foreach($positions as $position)
                      <option value="{{ $position }}">{{ $position }}</option>
                    @endforeach
                  @endif
                </select>
              </div>

              <!-- Evaluation Status Filter -->
              <div>
                <label for="evaluation_status" class="block text-sm font-medium text-gray-700 mb-2">Evaluation Status</label>
                <select name="evaluation_status" id="evaluation_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                  <option value="">All Statuses</option>
                  <option value="passed">Passed (≥70)</option>
                  <option value="failed">Failed (<70)</option>
                  <option value="pending">Pending Evaluation</option>
                </select>
              </div>

              <!-- Year & Month Selection -->
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                  <select name="year" id="year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                    <option value="">All Years</option>
                    @if(isset($years))
                      @foreach($years as $year)
                        <option value="{{ $year }}" {{ (isset($currentYear) && $year == $currentYear) ? 'selected' : '' }}>
                          {{ $year }}
                        </option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div>
                  <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                  <select name="month" id="month" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BD6F22] focus:border-transparent">
                    <option value="">All Months</option>
                    <option value="1" {{ (isset($currentMonth) && $currentMonth == 1) ? 'selected' : '' }}>January</option>
                    <option value="2" {{ (isset($currentMonth) && $currentMonth == 2) ? 'selected' : '' }}>February</option>
                    <option value="3" {{ (isset($currentMonth) && $currentMonth == 3) ? 'selected' : '' }}>March</option>
                    <option value="4" {{ (isset($currentMonth) && $currentMonth == 4) ? 'selected' : '' }}>April</option>
                    <option value="5" {{ (isset($currentMonth) && $currentMonth == 5) ? 'selected' : '' }}>May</option>
                    <option value="6" {{ (isset($currentMonth) && $currentMonth == 6) ? 'selected' : '' }}>June</option>
                    <option value="7" {{ (isset($currentMonth) && $currentMonth == 7) ? 'selected' : '' }}>July</option>
                    <option value="8" {{ (isset($currentMonth) && $currentMonth == 8) ? 'selected' : '' }}>August</option>
                    <option value="9" {{ (isset($currentMonth) && $currentMonth == 9) ? 'selected' : '' }}>September</option>
                    <option value="10" {{ (isset($currentMonth) && $currentMonth == 10) ? 'selected' : '' }}>October</option>
                    <option value="11" {{ (isset($currentMonth) && $currentMonth == 11) ? 'selected' : '' }}>November</option>
                    <option value="12" {{ (isset($currentMonth) && $currentMonth == 12) ? 'selected' : '' }}>December</option>
                  </select>
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
    // GET DATA FROM PHP
    // ============================================
    const trainingStats = {!! json_encode($trainingStats ?? [
        'passed' => 0,
        'failed' => 0,
        'total_evaluations' => 0,
        'avg_score' => 0,
        'pass_rate' => 0,
        'avg_knowledge' => 0,
        'avg_skill' => 0,
        'avg_participation' => 0,
        'avg_professionalism' => 0
    ]) !!};

    const pipelineStats = {!! json_encode($pipelineStats ?? [
        'evaluated' => 0,
        'passed' => 0,
        'invited' => 0,
        'promoted' => 0
    ]) !!};

    const notifications = @json($notifications ?? []);

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
    // TRAINING & EVALUATION OVERVIEW
    // ============================================
    const passRate = trainingStats.pass_rate || 0;

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
            let typeLabel = notif.type.replace(/_/g, ' ').toUpperCase();

            if (notif.priority === 'urgent') {
                badgeColor = 'bg-red-100 text-red-800';
                borderColor = 'border-red-300';
            } else if (notif.priority === 'important') {
                badgeColor = 'bg-amber-100 text-amber-800';
                borderColor = 'border-amber-300';
            }

            html += `
                <div class="p-3 bg-gray-50 rounded-lg border ${borderColor} hover:shadow-md transition-shadow cursor-pointer" onclick="window.location.href='${notif.action_url}'">
                    <div class="flex items-start gap-2">
                        <span class="${badgeColor} text-xs px-2 py-1 rounded font-medium">${typeLabel}</span>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-800">${notif.applicant_name} - ${notif.position}</p>
                            <p class="text-xs text-gray-500">${notif.company}</p>
                            <p class="text-xs text-gray-600 mt-1">${notif.message}</p>
                            ${notif.action_text ? `<button class="text-xs text-[#BD6F22] hover:underline mt-1 font-medium">${notif.action_text}</button>` : ''}
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

    // Apply Filters (AJAX)
    document.getElementById('filterForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');

        if (!submitBtn) return;

        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-4 w-4 inline-block mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 74 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading...
        `;

        try {
            const formData = new FormData(this);
            const response = await fetch('{{ route("hrStaff.filterApplications") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const result = await response.json();

            if (result.success) {
                showNotification('success', `Found ${result.count} applications matching the filters`);
                console.log('Filtered results:', result.data);
                // You can display the results in a modal or update a section of the page here
            } else {
                showNotification('error', 'Failed to filter applications');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'An error occurred while filtering');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Generate PDF Report
    document.getElementById('generatePdfBtn')?.addEventListener('click', function() {
        const reportType = document.getElementById('report_type').value;
        const company = document.getElementById('company').value;
        const position = document.getElementById('position').value;
        const evaluationStatus = document.getElementById('evaluation_status').value;
        const year = document.getElementById('year').value;
        const month = document.getElementById('month').value;
        const minScore = document.getElementById('min_score').value;
        const maxScore = document.getElementById('max_score').value;

        const params = new URLSearchParams();
        if (company) params.append('company', company);
        if (position) params.append('position', position);
        if (evaluationStatus) params.append('evaluation_status', evaluationStatus);
        if (year) params.append('year', year);
        if (month) params.append('month', month);
        if (minScore) params.append('min_score', minScore);
        if (maxScore) params.append('max_score', maxScore);

        // Convert report_type format: training_evaluation -> training-evaluation
        const reportTypeFormatted = reportType.replace(/_/g, '-');
        const url = `/hrStaff/reports/${reportTypeFormatted}/pdf?` + params.toString();

        // Show loading notification
        showNotification('info', 'Generating PDF report...');

        // Open PDF in new window
        window.open(url, '_blank');
    });

    // Reset Filters
    document.getElementById('resetFilters')?.addEventListener('click', function() {
        document.getElementById('filterForm').reset();
    });

    // ============================================
    // TOAST NOTIFICATION HELPER
    // ============================================
    function showNotification(type, message) {
        // Create toast notification element
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out translate-x-0`;

        // Set colors based on type
        let bgColor, textColor, icon;
        switch(type) {
            case 'success':
                bgColor = 'bg-green-500';
                textColor = 'text-white';
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>`;
                break;
            case 'error':
                bgColor = 'bg-red-500';
                textColor = 'text-white';
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>`;
                break;
            case 'info':
                bgColor = 'bg-blue-500';
                textColor = 'text-white';
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`;
                break;
            default:
                bgColor = 'bg-gray-700';
                textColor = 'text-white';
                icon = '';
        }

        toast.className += ` ${bgColor} ${textColor}`;
        toast.innerHTML = `
            <div class="flex items-center gap-3">
                ${icon}
                <span class="font-medium">${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        // Slide in animation
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 10);

        // Remove after 4 seconds
        setTimeout(() => {
            toast.style.transform = 'translateX(400px)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 4000);
    }
});
</script>

@endsection
