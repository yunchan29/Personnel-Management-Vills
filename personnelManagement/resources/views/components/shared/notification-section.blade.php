@props(['notifications' => [], 'userRole' => 'applicant'])

@if(count($notifications) > 0)
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 border-l-4 border-amber-500" x-data="{ expanded: false }">
    <!-- Header - Clickable to Toggle -->
    <button
        @click="expanded = !expanded"
        class="w-full bg-gradient-to-br from-amber-50 to-white px-6 py-4 hover:from-amber-100 hover:to-amber-50 transition-colors duration-200"
        :class="expanded ? 'border-b border-amber-100' : ''"
    >
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="bg-amber-500 text-white rounded-lg p-3 relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if(count($notifications) > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        {{ count($notifications) }}
                    </span>
                    @endif
                </div>
                <div class="text-left flex-1">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <span>Notifications</span>
                    </h2>
                    @php
                        $notificationCount = count($notifications);
                        $actionText = $notificationCount == 1 ? 'action' : 'actions';
                    @endphp
                    <p class="text-sm text-gray-600">
                        <span x-show="!expanded">
                            {{ $notificationCount }} pending {{ $actionText }}
                        </span>
                        <span x-show="expanded" x-cloak>Click to collapse</span>
                    </p>
                </div>
            </div>

            <!-- Chevron Icon -->
            <svg
                class="w-6 h-6 text-gray-600 transform transition-transform duration-200 flex-shrink-0"
                :class="{ 'rotate-180': expanded }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </button>

    <!-- Summary View - Shown when collapsed -->
    <div
        x-show="!expanded"
        class="px-6 py-3 bg-amber-50 border-t border-amber-100"
    >
        <div class="flex flex-wrap gap-2">
            @php
                $summaryItems = [];
                $urgentCount = 0;
                $typeCount = [
                    'interview' => 0,
                    'training' => 0,
                    'evaluation' => 0,
                    'application' => 0,
                ];

                foreach($notifications as $notification) {
                    $type = isset($notification['type']) ? $notification['type'] : 'application';
                    if (isset($typeCount[$type])) {
                        $typeCount[$type]++;
                    }

                    if (isset($notification['days_until']) && $notification['days_until'] <= 2) {
                        $urgentCount++;
                    }
                }
            @endphp

            @if($urgentCount > 0)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $urgentCount }} Urgent
                </span>
            @endif

            @if($typeCount['interview'] > 0)
                @php
                    $interviewText = $typeCount['interview'] == 1 ? 'Interview' : 'Interviews';
                @endphp
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    {{ $typeCount['interview'] }} {{ $interviewText }}
                </span>
            @endif

            @if($typeCount['training'] > 0)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ $typeCount['training'] }} Training
                </span>
            @endif

            @if($typeCount['evaluation'] > 0)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    {{ $typeCount['evaluation'] }} Evaluation
                </span>
            @endif

            @if($typeCount['application'] > 0)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ $typeCount['application'] }} Application
                </span>
            @endif
        </div>
    </div>

    <!-- Notifications List - Collapsible -->
    <div
        x-show="expanded"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="divide-y divide-gray-100"
    >
        @php
            $typeColors = [
                'interview' => ['icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-600', 'text' => 'text-blue-900'],
                'training' => ['icon_bg' => 'bg-purple-100', 'icon_color' => 'text-purple-600', 'text' => 'text-purple-900'],
                'evaluation' => ['icon_bg' => 'bg-indigo-100', 'icon_color' => 'text-indigo-600', 'text' => 'text-indigo-900'],
                'application' => ['icon_bg' => 'bg-green-100', 'icon_color' => 'text-green-600', 'text' => 'text-green-900'],
                'urgent' => ['icon_bg' => 'bg-red-100', 'icon_color' => 'text-red-600', 'text' => 'text-red-900'],
            ];
        @endphp

        @foreach($notifications as $notification)
            @php
                $notifType = isset($notification['type']) ? $notification['type'] : 'application';
                $isUrgent = isset($notification['days_until']) && $notification['days_until'] <= 2;
                $colorType = $isUrgent ? 'urgent' : $notifType;
                $color = isset($typeColors[$colorType]) ? $typeColors[$colorType] : $typeColors['application'];
            @endphp

            <div class="p-4 hover:bg-gray-50 transition-colors {{ $isUrgent ? 'animate-pulse-slow' : '' }}">
                <!-- Header Row -->
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2">
                        <!-- Smaller Icon -->
                        <div class="{{ $color['icon_bg'] }} {{ $color['icon_color'] }} rounded-lg p-2 flex-shrink-0">
                            @if($notifType == 'interview')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            @elseif($notifType == 'training')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            @elseif($notifType == 'evaluation')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            @endif
                        </div>
                        <h3 class="font-semibold {{ $color['text'] }} text-sm">
                            {{ $notification['title'] }}
                        </h3>
                    </div>

                    <!-- Time Badge -->
                    @if(isset($notification['days_until']))
                        @if($notification['days_until'] == 0)
                            <span class="px-2 py-0.5 bg-red-500 text-white rounded-full text-xs font-bold flex-shrink-0 animate-pulse">
                                TODAY
                            </span>
                        @elseif($notification['days_until'] == 1)
                            <span class="px-2 py-0.5 bg-orange-500 text-white rounded-full text-xs font-bold flex-shrink-0">
                                TOMORROW
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded-full text-xs font-semibold flex-shrink-0">
                                {{ $notification['days_until'] }}d
                            </span>
                        @endif
                    @endif
                </div>

                <!-- Message - More compact -->
                <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $notification['message'] }}</p>

                <!-- Details - Simplified for sidebar -->
                @if(!empty($notification['details']))
                <div class="space-y-1 mb-3">
                    @foreach($notification['details'] as $label => $value)
                        <div class="text-xs text-gray-600">
                            <span class="font-medium text-gray-700">{{ $label }}:</span>
                            <span class="ml-1">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
                @endif

                <!-- Action Button - Smaller -->
                @if(!empty($notification['action_url']))
                @php
                    $actionButtonText = isset($notification['action_text']) ? $notification['action_text'] : 'View Details';
                @endphp
                <a href="{{ $notification['action_url'] }}" class="inline-flex items-center gap-1 text-xs font-medium text-[#BD6F22] hover:text-[#a75e1c] transition-colors">
                    <span>{{ $actionButtonText }}</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
            </div>
        @endforeach
    </div>
</div>

<style>
@keyframes pulse-slow {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.95;
    }
}

.animate-pulse-slow {
    animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endif
