@props([
    'title',
    'company',
    'location',
    'lastPosted',
    'deadline',
    'qualifications' => [],
    'additionalQualifications' => [],
    'benefits' => [],
])

<div 
    x-data="{ showDetails: false }" 
    class="w-full border rounded-lg shadow-sm p-6 bg-white space-y-4 transition-all duration-300 relative"
>
    <!-- Top Row -->
    <div class="flex justify-between items-start gap-4 flex-wrap">
        <!-- Date Info -->
        <div class="text-sm text-gray-500">
            <p>Last Posted: {{ $lastPosted }}</p>
            <p>Apply until: {{ $deadline }}</p>
        </div>

        <!-- Job Info -->
        <div class="flex-1 min-w-[200px]">
            <h3 class="text-[#BD6F22] text-xl font-bold">{{ $title }}</h3>
            <p class="text-gray-800 font-semibold">{{ $company }}</p>

            <!-- Basic Qualifications -->
            <div class="flex items-start mt-2 gap-2">
                <img src="/images/briefcaseblack.png" class="w-5 h-5 mt-1" alt="Qualification">
                <ul class="text-sm text-gray-700 list-disc list-inside">
                    @foreach ($qualifications as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Location -->
            <div class="flex items-center mt-2 text-gray-700">
                <img src="/images/location.png" class="w-5 h-5 mr-1" alt="Location">
                <p>{{ $location }}</p>
            </div>
        </div>

        <!-- Apply Button (top-right, visible only when expanded) -->
        <div x-show="showDetails" x-transition>
            <button class="bg-[#BD6F22] hover:bg-[#a75d1c] text-white px-6 py-2 rounded-md flex items-center gap-2 text-sm font-medium transition">
                <img src="/images/mousepointer.png" class="w-4 h-4" alt="Apply"> Apply Now
            </button>
        </div>
    </div>

    <!-- Apply Button (collapsed view, vertically centered on left) -->
    <div 
        x-show="!showDetails" 
        x-transition 
        class="absolute left-6 top-1/2 -translate-y-1/2"
    >
        <button class="bg-[#BD6F22] hover:bg-[#a75d1c] text-white px-6 py-2 rounded-md flex items-center gap-2 text-sm font-medium transition">
            <img src="/images/mousepointer.png" class="w-4 h-4" alt="Apply"> Apply Now
        </button>
    </div>

    <!-- See More Link -->
    <div class="text-center">
        <button 
            @click="showDetails = !showDetails" 
            class="text-[#BD6F22] hover:text-[#a75d1c] text-sm font-semibold underline transition"
            x-text="showDetails ? 'See Less' : 'See More'"
        ></button>
    </div>

    <!-- Expandable Section -->
    <div 
        x-show="showDetails"
        x-transition:enter="transition-all ease-out duration-300"
        x-transition:enter-start="opacity-0 max-h-0"
        x-transition:enter-end="opacity-100 max-h-screen"
        x-transition:leave="transition-all ease-in duration-200"
        x-transition:leave-start="opacity-100 max-h-screen"
        x-transition:leave-end="opacity-0 max-h-0"
        class="overflow-hidden text-gray-800 space-y-3 text-sm"
    >
        @if (!empty($additionalQualifications))
            <div>
                <p class="font-semibold">Additional Qualifications:</p>
                <ul class="list-disc list-inside">
                    @foreach ($additionalQualifications as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (!empty($benefits))
            <div>
                <p class="font-semibold">Benefits:</p>
                <ul class="list-disc list-inside">
                    @foreach ($benefits as $benefit)
                        <li>{{ $benefit }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
