@extends('layouts.applicantHome')

@section('content')
    <section class="w-full">
        <div class="bg-[#BD9168] w-full h-16 flex items-center justify-center">
            <h2 class="text-white text-3xl font-bold">Job Listing</h2>
        </div>

        <div class="p-6 bg-white">
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
