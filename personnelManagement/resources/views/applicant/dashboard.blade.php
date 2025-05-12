@extends('layouts.applicantHome')

@section('content')
    <section class="w-full">
       

        {{-- Show banner only if profile is incomplete --}}
        @if (!auth()->user()->is_profile_complete)
            <div class="bg-[#BD9168] text-white p-6 text-center">
                <h2 class="text-xl mb-2 tracking-wide">Profile Incomplete</h2>
                <p class="text-sm mb-4">Just one more thing! Finish your profile so you can start applying to jobs and get matched faster.</p>
                <a href="{{ route('applicant.profile') }}" class="inline-block bg-white text-[#BD9168]  px-6 py-2 rounded-full hover:bg-gray-100 transition">
                    Update Profile
                </a>
            </div>
        @endif

        <div class="p-6 bg-white">
             <h2 class="text-xl font-semibold text-[#BD6F22] mb-4">Home</h2>
            <div class="flex gap-4 mb-6">
                <input type="text" placeholder="Search..." class="border px-4 py-2 rounded-lg w-full">
                <button class="bg-[#BD9168] text-white px-4 py-2 rounded-lg">Search</button>
            </div>

            <div class="flex flex-col gap-6">
                <x-applicant.job-card 
                    title="Production Operator"
                    company="Yazaki - Torres Manufacturing, Inc."
                    location="Makiling, Calamba City, Laguna"
                    :qualifications="['18 years and above', 'With or without experience']"
                    lastPosted="3 days ago"
                    deadline="June 20, 2025"
                />
            </div>
        </div>
    </section>
@endsection
