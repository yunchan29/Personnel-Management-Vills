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
    <div class="flex-1">

      <!-- Filter Status Indicator (hidden by default) -->
      <div id="filter-status-indicator" class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 hidden">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            <span class="text-sm font-medium text-amber-800">Filtered View Active:</span>
            <span id="filter-status-text" class="text-sm text-amber-700"></span>
          </div>
          <button id="clear-filters-btn" class="text-sm text-amber-600 hover:text-amber-800 font-medium underline">
            Clear Filters
          </button>
        </div>
      </div>

      <!-- Evaluation Pipeline -->
      <div class="bg-white p-6 rounded-md shadow-md border border-gray-200 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
          <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          Evaluation Pipeline
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Evaluated -->
          <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="flex items-baseline gap-2">
              <div class="text-2xl font-bold text-blue-600" id="pipeline-evaluated">0</div>
              <div class="text-xs text-gray-400 overall-value hidden">
                (Overall: <span id="pipeline-evaluated-overall">0</span>)
              </div>
            </div>
            <div class="text-sm text-gray-600 mt-1">Evaluated</div>
            <div class="text-xs text-gray-500 mt-1">Total evaluated</div>
          </div>

          <!-- Passed -->
          <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="flex items-baseline gap-2">
              <div class="text-2xl font-bold text-green-600" id="pipeline-passed">0</div>
              <div class="text-xs text-gray-400 overall-value hidden">
                (Overall: <span id="pipeline-passed-overall">0</span>)
              </div>
            </div>
            <div class="text-sm text-gray-600 mt-1">Passed</div>
            <div class="text-xs text-gray-500 mt-1">Ready for invitation</div>
          </div>

          <!-- Invited -->
          <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
            <div class="flex items-baseline gap-2">
              <div class="text-2xl font-bold text-amber-600" id="pipeline-invited">0</div>
              <div class="text-xs text-gray-400 overall-value hidden">
                (Overall: <span id="pipeline-invited-overall">0</span>)
              </div>
            </div>
            <div class="text-sm text-gray-600 mt-1">Invited</div>
            <div class="text-xs text-gray-500 mt-1">Signing scheduled</div>
          </div>

          <!-- Promoted -->
          <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <div class="flex items-baseline gap-2">
              <div class="text-2xl font-bold text-purple-600" id="pipeline-promoted">0</div>
              <div class="text-xs text-gray-400 overall-value hidden">
                (Overall: <span id="pipeline-promoted-overall">0</span>)
              </div>
            </div>
            <div class="text-sm text-gray-600 mt-1">Hired</div>
            <div class="text-xs text-gray-500 mt-1">Now employees</div>
          </div>
        </div>
      </div>

      <!-- Contract Invitation Statistics -->
      <div class="bg-white p-6 rounded-md shadow-md border border-gray-200 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
          <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          Contract Signing Invitations
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
          <!-- Today -->
          <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="text-2xl font-bold text-green-600">{{ $invitationStats['sent_today'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 mt-1">Sent Today</div>
            <div class="text-xs text-gray-500 mt-1">{{ now()->format('M d') }}</div>
          </div>

          <!-- This Week -->
          <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="text-2xl font-bold text-blue-600">{{ $invitationStats['sent_this_week'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 mt-1">This Week</div>
            <div class="text-xs text-gray-500 mt-1">{{ now()->startOfWeek()->format('M d') }} - {{ now()->endOfWeek()->format('M d') }}</div>
          </div>

          <!-- This Month -->
          <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <div class="text-2xl font-bold text-purple-600">{{ $invitationStats['sent_this_month'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 mt-1">This Month</div>
            <div class="text-xs text-gray-500 mt-1">{{ now()->format('F Y') }}</div>
          </div>

          <!-- Total -->
          <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
            <div class="text-2xl font-bold text-amber-600">{{ $invitationStats['total_sent'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 mt-1">Total Sent</div>
            <div class="text-xs text-gray-500 mt-1">All time</div>
          </div>
        </div>

        <!-- Email Delivery Stats -->
        <div class="grid grid-cols-2 gap-3 mb-4">
          <div class="bg-teal-50 p-3 rounded-lg border border-teal-200">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-xl font-bold text-teal-600">{{ $invitationStats['emails_sent'] ?? 0 }}</div>
                <div class="text-xs text-gray-600">Emails Delivered</div>
              </div>
              <svg class="w-8 h-8 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>

          <div class="bg-red-50 p-3 rounded-lg border border-red-200">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-xl font-bold text-red-600">{{ $invitationStats['emails_failed'] ?? 0 }}</div>
                <div class="text-xs text-gray-600">Email Failures</div>
              </div>
              <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Recent Invitations -->
        <div class="mt-4">
          <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
            <svg class="w-4 h-4 mr-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Recent Invitations (Last 7 Days)
          </h3>

          @if(isset($invitationStats['recent']) && count($invitationStats['recent']) > 0)
          <div class="bg-gray-50 rounded-lg p-3 max-h-64 overflow-y-auto">
            <div class="space-y-2">
              @foreach($invitationStats['recent'] as $invitation)
              <div class="bg-white p-3 rounded border border-gray-200 hover:shadow-sm transition-shadow">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-2">
                      <span class="font-semibold text-sm text-gray-800">{{ $invitation['applicant_name'] }}</span>
                      @if($invitation['email_sent'])
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">✓ Sent</span>
                      @else
                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">✗ Failed</span>
                      @endif
                    </div>
                    <div class="text-xs text-gray-600 mt-1">{{ $invitation['position'] }} • {{ $invitation['company'] }}</div>
                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                      <span>📅 {{ $invitation['scheduled_date'] }} at {{ $invitation['scheduled_time'] }}</span>
                    </div>
                  </div>
                  <div class="text-right ml-2">
                    <div class="text-xs text-gray-500">{{ $invitation['sent_at'] }}</div>
                    <div class="text-xs text-gray-400">by {{ $invitation['sent_by'] }}</div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          @else
          <div class="bg-gray-50 rounded-lg p-4 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <p class="text-sm text-gray-500">No invitations sent in the last 7 days</p>
          </div>
          @endif
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
              <div class="flex items-baseline gap-2">
                <span class="text-lg font-bold text-green-600" id="training-pass-rate">0%</span>
                <span class="text-xs text-gray-400 overall-value hidden">
                  (Overall: <span id="training-pass-rate-overall">0%</span>)
                </span>
              </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div class="bg-green-500 h-3 rounded-full transition-all duration-500" id="training-pass-bar" style="width: 0%"></div>
            </div>
          </div>

          <!-- Average Score -->
          <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-gray-700">Average Score</span>
              <div class="flex items-baseline gap-2">
                <span class="text-lg font-bold text-[#BD6F22]" id="avg-score">0</span>
                <span class="text-xs text-gray-400 overall-value hidden">
                  (Overall: <span id="avg-score-overall">0</span>)
                </span>
              </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div class="bg-[#BD6F22] h-3 rounded-full transition-all duration-500" id="avg-score-bar" style="width: 0%"></div>
            </div>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-2 gap-2 mt-4">
            <div class="bg-green-50 p-2 rounded text-center border border-green-200">
              <div class="flex items-baseline justify-center gap-1">
                <div class="text-xl font-bold text-green-600" id="training-passed">0</div>
                <div class="text-xs text-gray-400 overall-value hidden">
                  (<span id="training-passed-overall">0</span>)
                </div>
              </div>
              <div class="text-xs text-gray-600">Passed</div>
            </div>
            <div class="bg-red-50 p-2 rounded text-center border border-red-200">
              <div class="flex items-baseline justify-center gap-1">
                <div class="text-xl font-bold text-red-600" id="training-failed">0</div>
                <div class="text-xs text-gray-400 overall-value hidden">
                  (<span id="training-failed-overall">0</span>)
                </div>
              </div>
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
                <div class="flex items-baseline gap-1">
                  <span class="text-sm font-bold text-blue-600"><span id="knowledge-score">0</span>/30</span>
                  <span class="text-xs text-gray-400 overall-value hidden">
                    (<span id="knowledge-score-overall">0</span>)
                  </span>
                </div>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" id="knowledge-bar" style="width: 0%"></div>
              </div>
            </div>

            <!-- Skill Application -->
            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-medium text-gray-700">Skill Application</span>
                <div class="flex items-baseline gap-1">
                  <span class="text-sm font-bold text-indigo-600"><span id="skill-score">0</span>/30</span>
                  <span class="text-xs text-gray-400 overall-value hidden">
                    (<span id="skill-score-overall">0</span>)
                  </span>
                </div>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500" id="skill-bar" style="width: 0%"></div>
              </div>
            </div>

            <!-- Participation & Engagement -->
            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-medium text-gray-700">Participation & Engagement</span>
                <div class="flex items-baseline gap-1">
                  <span class="text-sm font-bold text-purple-600"><span id="participation-score">0</span>/20</span>
                  <span class="text-xs text-gray-400 overall-value hidden">
                    (<span id="participation-score-overall">0</span>)
                  </span>
                </div>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" id="participation-bar" style="width: 0%"></div>
              </div>
            </div>

            <!-- Professionalism & Attitude -->
            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-medium text-gray-700">Professionalism & Attitude</span>
                <div class="flex items-baseline gap-1">
                  <span class="text-sm font-bold text-pink-600"><span id="professionalism-score">0</span>/20</span>
                  <span class="text-xs text-gray-400 overall-value hidden">
                    (<span id="professionalism-score-overall">0</span>)
                  </span>
                </div>
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
              <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold text-[#BD6F22]"><span id="total-score-display">0</span>/100</span>
                <span class="text-xs text-gray-400 overall-value hidden">
                  (<span id="total-score-display-overall">0</span>)
                </span>
              </div>
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
        <div class="max-h-96 overflow-y-auto">
          <x-shared.notification-section :notifications="$allNotifications ?? []" :unreadCount="$unreadCount ?? 0" userRole="admin" />
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
                <h2 class="text-xl font-bold text-gray-900">Training Evaluation Report</h2>
                <p class="text-sm text-gray-600">Filter and generate report</p>
              </div>
            </div>
          </div>

          <div class="p-6">
            <form id="filterForm" class="space-y-4">
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

    const notifications = @json($allNotifications ?? []);

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
        ? Math.min(((trainingStats.avg_knowledge / 30) * 100), 100).toFixed(1)
        : 0;
    document.getElementById('knowledge-score').textContent = (trainingStats.avg_knowledge || 0).toFixed(1);
    document.getElementById('knowledge-bar').style.width = knowledgePercent + '%';

    // Skill Application (out of 30)
    const skillPercent = trainingStats.avg_skill > 0
        ? Math.min(((trainingStats.avg_skill / 30) * 100), 100).toFixed(1)
        : 0;
    document.getElementById('skill-score').textContent = (trainingStats.avg_skill || 0).toFixed(1);
    document.getElementById('skill-bar').style.width = skillPercent + '%';

    // Participation & Engagement (out of 20)
    const participationPercent = trainingStats.avg_participation > 0
        ? Math.min(((trainingStats.avg_participation / 20) * 100), 100).toFixed(1)
        : 0;
    document.getElementById('participation-score').textContent = (trainingStats.avg_participation || 0).toFixed(1);
    document.getElementById('participation-bar').style.width = participationPercent + '%';

    // Professionalism & Attitude (out of 20)
    const professionalismPercent = trainingStats.avg_professionalism > 0
        ? Math.min(((trainingStats.avg_professionalism / 20) * 100), 100).toFixed(1)
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

    // Notifications are now handled by the shared notification component

    // ============================================
    // UPDATE DASHBOARD STATS FUNCTION
    // ============================================
    function updateDashboardStats(filteredStats, overallStats, filterActive = false) {
        // Show/hide filter status indicator
        const filterIndicator = document.getElementById('filter-status-indicator');
        const filterStatusText = document.getElementById('filter-status-text');
        const overallValues = document.querySelectorAll('.overall-value');

        if (filterActive) {
            // Build filter text
            const company = document.getElementById('company')?.value;
            const position = document.getElementById('position')?.value;
            const evalStatus = document.getElementById('evaluation_status')?.value;
            const year = document.getElementById('year')?.value;
            const month = document.getElementById('month')?.value;

            let filterText = [];
            if (company) filterText.push(company);
            if (position) filterText.push(position);
            if (evalStatus) {
                const statusLabel = evalStatus === 'passed' ? 'Passed' :
                                  evalStatus === 'failed' ? 'Failed' : 'Pending';
                filterText.push(statusLabel);
            }
            if (year && month) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                filterText.push(`${months[month - 1]} ${year}`);
            } else if (year) {
                filterText.push(year);
            }

            filterStatusText.textContent = filterText.join(' • ');
            filterIndicator.classList.remove('hidden');
            overallValues.forEach(el => el.classList.remove('hidden'));
        } else {
            filterIndicator.classList.add('hidden');
            overallValues.forEach(el => el.classList.add('hidden'));
        }

        // Update Evaluation Pipeline
        document.getElementById('pipeline-evaluated').textContent = filteredStats.pipelineStats.evaluated;
        document.getElementById('pipeline-passed').textContent = filteredStats.pipelineStats.passed;
        document.getElementById('pipeline-invited').textContent = filteredStats.pipelineStats.invited;
        document.getElementById('pipeline-promoted').textContent = filteredStats.pipelineStats.promoted;

        if (filterActive && overallStats) {
            document.getElementById('pipeline-evaluated-overall').textContent = overallStats.pipelineStats.evaluated;
            document.getElementById('pipeline-passed-overall').textContent = overallStats.pipelineStats.passed;
            document.getElementById('pipeline-invited-overall').textContent = overallStats.pipelineStats.invited;
            document.getElementById('pipeline-promoted-overall').textContent = overallStats.pipelineStats.promoted;
        }

        // Update Training & Evaluation Overview
        document.getElementById('training-pass-rate').textContent = filteredStats.trainingStats.pass_rate + '%';
        document.getElementById('training-pass-bar').style.width = filteredStats.trainingStats.pass_rate + '%';
        document.getElementById('avg-score').textContent = filteredStats.trainingStats.avg_score;
        document.getElementById('avg-score-bar').style.width = filteredStats.trainingStats.avg_score + '%';
        document.getElementById('training-passed').textContent = filteredStats.trainingStats.passed;
        document.getElementById('training-failed').textContent = filteredStats.trainingStats.failed;

        if (filterActive && overallStats) {
            document.getElementById('training-pass-rate-overall').textContent = overallStats.trainingStats.pass_rate + '%';
            document.getElementById('avg-score-overall').textContent = overallStats.trainingStats.avg_score;
            document.getElementById('training-passed-overall').textContent = overallStats.trainingStats.passed;
            document.getElementById('training-failed-overall').textContent = overallStats.trainingStats.failed;
        }

        // Update Evaluation Score Breakdown
        const knowledgePercent = filteredStats.trainingStats.avg_knowledge > 0
            ? Math.min(((filteredStats.trainingStats.avg_knowledge / 30) * 100), 100).toFixed(1)
            : 0;
        document.getElementById('knowledge-score').textContent = (filteredStats.trainingStats.avg_knowledge || 0).toFixed(1);
        document.getElementById('knowledge-bar').style.width = knowledgePercent + '%';

        const skillPercent = filteredStats.trainingStats.avg_skill > 0
            ? Math.min(((filteredStats.trainingStats.avg_skill / 30) * 100), 100).toFixed(1)
            : 0;
        document.getElementById('skill-score').textContent = (filteredStats.trainingStats.avg_skill || 0).toFixed(1);
        document.getElementById('skill-bar').style.width = skillPercent + '%';

        const participationPercent = filteredStats.trainingStats.avg_participation > 0
            ? Math.min(((filteredStats.trainingStats.avg_participation / 20) * 100), 100).toFixed(1)
            : 0;
        document.getElementById('participation-score').textContent = (filteredStats.trainingStats.avg_participation || 0).toFixed(1);
        document.getElementById('participation-bar').style.width = participationPercent + '%';

        const professionalismPercent = filteredStats.trainingStats.avg_professionalism > 0
            ? Math.min(((filteredStats.trainingStats.avg_professionalism / 20) * 100), 100).toFixed(1)
            : 0;
        document.getElementById('professionalism-score').textContent = (filteredStats.trainingStats.avg_professionalism || 0).toFixed(1);
        document.getElementById('professionalism-bar').style.width = professionalismPercent + '%';

        document.getElementById('total-score-display').textContent = (filteredStats.trainingStats.avg_score || 0).toFixed(1);

        if (filterActive && overallStats) {
            document.getElementById('knowledge-score-overall').textContent = (overallStats.trainingStats.avg_knowledge || 0).toFixed(1);
            document.getElementById('skill-score-overall').textContent = (overallStats.trainingStats.avg_skill || 0).toFixed(1);
            document.getElementById('participation-score-overall').textContent = (overallStats.trainingStats.avg_participation || 0).toFixed(1);
            document.getElementById('professionalism-score-overall').textContent = (overallStats.trainingStats.avg_professionalism || 0).toFixed(1);
            document.getElementById('total-score-display-overall').textContent = (overallStats.trainingStats.avg_score || 0).toFixed(1);
        }
    }

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
            Updating...
        `;

        // Add loading state to dashboard sections
        const dashboardSections = document.querySelectorAll('#pipeline-evaluated, #pipeline-passed, #pipeline-invited, #pipeline-promoted, #training-pass-rate, #avg-score, #training-passed, #training-failed, #knowledge-score, #skill-score, #participation-score, #professionalism-score, #total-score-display');
        dashboardSections.forEach(el => el.style.opacity = '0.5');

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
                // Update dashboard with filtered stats
                updateDashboardStats(result.filtered, result.overall, true);

                showNotification('success', `Showing ${result.count} applications`);
                console.log('Filtered results:', result);
            } else {
                showNotification('error', 'Failed to filter applications');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'An error occurred while filtering');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            dashboardSections.forEach(el => el.style.opacity = '1');
        }
    });

    // Generate PDF Report
    document.getElementById('generatePdfBtn')?.addEventListener('click', function() {
        try {
            console.log('=== PDF Report Generation Started ===');

            // Hardcoded to training-evaluation report only
            const reportType = 'training-evaluation';
            const company = document.getElementById('company').value;
            const position = document.getElementById('position').value;
            const evaluationStatus = document.getElementById('evaluation_status').value;
            const year = document.getElementById('year').value;
            const month = document.getElementById('month').value;
            const minScore = document.getElementById('min_score').value;
            const maxScore = document.getElementById('max_score').value;

            console.log('Report Type:', reportType);
            console.log('Filters:', { company, position, evaluationStatus, year, month, minScore, maxScore });

            const params = new URLSearchParams();
            if (company) params.append('company', company);
            if (position) params.append('position', position);
            if (evaluationStatus) params.append('evaluation_status', evaluationStatus);
            if (year) params.append('year', year);
            if (month) params.append('month', month);
            if (minScore) params.append('min_score', minScore);
            if (maxScore) params.append('max_score', maxScore);

            // Report type is hardcoded as training-evaluation
            const url = `/hrStaff/reports/${reportType}/pdf?` + params.toString();

            console.log('Generated URL:', url);
            console.log('Query Parameters:', params.toString());

            // Show loading notification
            showNotification('info', 'Generating PDF report...');

            // Open PDF in new window and check if popup was blocked
            const newWindow = window.open(url, '_blank');

            if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                // Popup was blocked
                console.error('Popup blocked by browser');
                showNotification('error', 'Popup blocked! Please allow popups for this site and try again.');

                // Fallback: Try to open in same window
                console.log('Attempting fallback: opening in same window');
                window.location.href = url;
            } else {
                console.log('PDF window opened successfully');
                console.log('=== PDF Report Generation Completed ===');
            }
        } catch (error) {
            console.error('Error generating PDF report:', error);
            console.error('Error stack:', error.stack);
            showNotification('error', 'Failed to generate PDF report: ' + error.message);
            console.log('=== PDF Report Generation Failed ===');
        }
    });

    // Function to reset filters and restore overall stats
    function resetToOverallStats() {
        // Reset form
        document.getElementById('filterForm').reset();
        // Reset position dropdown to show all positions
        loadPositions('');

        // Get overall stats (from initial page load)
        const overallPipelineStats = {
            evaluated: {{ $pipelineStats['evaluated'] ?? 0 }},
            passed: {{ $pipelineStats['passed'] ?? 0 }},
            invited: {{ $pipelineStats['invited'] ?? 0 }},
            promoted: {{ $pipelineStats['promoted'] ?? 0 }}
        };

        const overallTrainingStats = {
            passed: {{ $trainingStats['passed'] ?? 0 }},
            failed: {{ $trainingStats['failed'] ?? 0 }},
            total_evaluations: {{ $trainingStats['total_evaluations'] ?? 0 }},
            avg_score: {{ $trainingStats['avg_score'] ?? 0 }},
            pass_rate: {{ $trainingStats['pass_rate'] ?? 0 }},
            avg_knowledge: {{ $trainingStats['avg_knowledge'] ?? 0 }},
            avg_skill: {{ $trainingStats['avg_skill'] ?? 0 }},
            avg_participation: {{ $trainingStats['avg_participation'] ?? 0 }},
            avg_professionalism: {{ $trainingStats['avg_professionalism'] ?? 0 }}
        };

        // Update dashboard back to overall stats (no filter active)
        updateDashboardStats(
            { pipelineStats: overallPipelineStats, trainingStats: overallTrainingStats },
            null,
            false
        );

        showNotification('info', 'Filters cleared - showing all data');
    }

    // Reset Filters button
    document.getElementById('resetFilters')?.addEventListener('click', resetToOverallStats);

    // Clear Filters button in filter status indicator
    document.getElementById('clear-filters-btn')?.addEventListener('click', resetToOverallStats);

    // ============================================
    // DYNAMIC POSITION DROPDOWN BASED ON COMPANY
    // ============================================
    const companySelect = document.getElementById('company');
    const positionSelect = document.getElementById('position');

    // Store all positions for reference
    const allPositions = @json($positions ?? []);

    // Function to load positions based on selected company
    function loadPositions(company) {
        // Clear current options except the "All Positions" option
        positionSelect.innerHTML = '<option value="">All Positions</option>';

        if (!company) {
            // If no company selected, show all positions
            allPositions.forEach(position => {
                const option = document.createElement('option');
                option.value = position;
                option.textContent = position;
                positionSelect.appendChild(option);
            });
        } else {
            // Fetch positions for the selected company
            fetch(`{{ route('hrStaff.getPositionsByCompany') }}?company=${encodeURIComponent(company)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.positions) {
                        data.positions.forEach(position => {
                            const option = document.createElement('option');
                            option.value = position;
                            option.textContent = position;
                            positionSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching positions:', error);
                    // Fallback to all positions on error
                    allPositions.forEach(position => {
                        const option = document.createElement('option');
                        option.value = position;
                        option.textContent = position;
                        positionSelect.appendChild(option);
                    });
                });
        }
    }

    // Listen for company selection change
    companySelect?.addEventListener('change', function() {
        const selectedCompany = this.value;
        loadPositions(selectedCompany);
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
