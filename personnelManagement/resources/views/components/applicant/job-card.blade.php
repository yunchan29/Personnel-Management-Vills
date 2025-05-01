<div class="p-6 border rounded-lg shadow-lg bg-white transform hover:scale-105 transition duration-300 flex flex-col md:flex-row gap-6">
    <div class="flex flex-col justify-between w-full md:w-1/3 gap-4">
        <div class="text-gray-500 text-sm">
            <p>Last Posted: <span class="font-medium">{{ $lastPosted }}</span></p>
            <p>Apply until: <span class="font-medium">{{ $deadline }}</span></p>
        </div>
        <div class="flex justify-center md:justify-start">
            <button class="bg-[#BD6F22] text-white px-8 py-3 text-lg rounded-md hover:bg-[#a75d1c] flex items-center gap-3">
                <img src="/images/mousepointer.png" class="w-6 h-6" alt="Apply Icon">
                Apply Now
            </button>
        </div>
    </div>
    <div class="flex flex-col w-full md:w-2/3 gap-2">
        <h3 class="text-[#BD6F22] text-2xl font-bold">{{ $title }}</h3>
        <p class="text-black font-semibold">{{ $company }}</p>
        <p class="text-black mt-2"><strong>Location:</strong> {{ $location }}</p>
        <p><strong>Qualification:</strong></p>
        <ul class="list-disc list-inside text-black">
            @foreach ($qualifications as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
        <button class="text-[#BD6F22] mt-2 font-semibold underline hover:text-[#a75d1c]">See More</button>
    </div>
</div>
