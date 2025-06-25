@extends('layouts.hrAdmin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-[#BD6F22] mb-6">Leave Form</h1>

    {{-- Tabs --}}
    <div class="flex border-b mb-6">
        <button class="px-4 py-2 border-b-2 border-[#BD6F22] text-[#BD6F22] font-medium">Pending</button>
        <button class="px-4 py-2 text-gray-500">Approve</button>
        <button class="px-4 py-2 text-gray-500">Decline</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Left Column - Leave Cards --}}
        <div class="md:col-span-2 space-y-4">
            {{-- Pending Card --}}
            <div class="bg-white shadow-sm rounded-md p-4 border relative">
                <div class="text-sm text-gray-500 absolute top-2 right-3">Submitted: May 10, 2025</div>
                <div class="text-[#BD6F22] font-semibold text-lg">Charlene Manzanilla</div>
                <div class="text-gray-800 font-medium text-xl">05/14/2025 - 05/15/2025</div>
                <div class="mt-2 flex gap-2">
                    <span class="bg-[#BD6F22] text-white text-sm px-3 py-1 rounded">Others</span>
                </div>
            </div>

            {{-- Approved Card --}}
            <div class="bg-white shadow-sm rounded-md p-4 border">
                <div class="text-[#BD6F22] font-semibold text-lg">Charlene Manzanilla</div>
                <div class="text-gray-800 font-medium text-xl">05/14/2025 - 05/15/2025</div>
                <div class="mt-2 flex gap-2">
                    <span class="bg-[#BD6F22] text-white text-sm px-3 py-1 rounded">Others</span>
                    <span class="bg-green-600 text-white text-sm px-3 py-1 rounded">Approved</span>
                </div>
            </div>
        </div>

        {{-- Right Column - Request Details --}}
        <div class="bg-white shadow rounded-md border p-6">
            <div class="text-[#BD6F22] font-semibold text-lg mb-2">Charlene Manzanilla</div>
            <p class="text-sm text-gray-800">Position: Production Helper</p>
            <p class="text-sm text-gray-800 mb-4">ID Number: 2025-0001</p>

            <h3 class="text-[#BD6F22] font-semibold text-lg mb-3">Request Details</h3>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Total Days Requested:</label>
                <input type="text" value="1 day" readonly class="border px-2 py-1 rounded text-sm">
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Leave Type:</label>
                <button class="border rounded px-3 py-1 text-sm">Others</button>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">About:</label>
                <textarea readonly class="w-full border rounded p-2 text-sm" rows="4">BEBETIME :))</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-700 mb-1">See Attachments:</label>
                <input type="text" value="L@gN@t.pdf" readonly class="border w-full px-2 py-1 rounded text-sm">
            </div>

            <div class="flex justify-end gap-2">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Approve</button>
                <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">Decline</button>
            </div>
        </div>
    </div>
</div>
@endsection