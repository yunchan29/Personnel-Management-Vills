@extends('layouts.hrAdmin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8"
    x-data="{
        selectedStatus: 'Pending',
        selectedForm: null,
        selectForm(event) {
            const formData = event.currentTarget.dataset.form;
            if (formData) {
                try {
                    this.selectedForm = JSON.parse(formData);
                } catch (e) {
                    console.error('Invalid form data:', formData);
                }
            }
        }
    }"
>
    <h1 class="text-2xl font-semibold text-[#BD6F22] mb-6">Leave Form</h1>

    {{-- Tabs --}}
    <div class="flex border-b mb-6">
        <template x-for="status in ['Pending', 'Approved', 'Declined']" :key="status">
            <button 
                class="px-4 py-2 font-medium transition"
                :class="{
                    'border-b-2 border-[#BD6F22] text-[#BD6F22]': selectedStatus === status,
                    'text-gray-500': selectedStatus !== status
                }"
                @click="selectedStatus = status; selectedForm = null"
                x-text="status"
            ></button>
        </template>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="md:col-span-2 space-y-4 max-h-[600px] overflow-y-auto pr-2">
            @foreach ($leaveForms as $form)
                <div 
                    x-show="selectedStatus === '{{ $form->status }}'" 
                    @click="selectForm($event)"
                    data-form='@json($form)'
                    class="cursor-pointer bg-white shadow-sm rounded-md p-4 border relative hover:shadow-md transition"
                >
                    <div class="text-sm text-gray-500 absolute top-2 right-3">
                        Submitted: {{ \Carbon\Carbon::parse($form->created_at)->format('M d, Y') }}
                    </div>
                    <div class="text-[#BD6F22] font-semibold text-lg">
                        {{ $form->user->first_name }} {{ $form->user->last_name }}
                    </div>
                    <div class="text-gray-800 font-medium text-xl">{{ $form->date_range }}</div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="bg-[#BD6F22] text-white text-sm px-3 py-1 rounded">{{ $form->leave_type }}</span>
                        @if ($form->status !== 'Pending')
                            <span class="text-white text-sm px-3 py-1 rounded 
                                {{ $form->status === 'Approved' ? 'bg-green-600' : 'bg-red-600' }}">
                                {{ $form->status }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Right Column --}}
        <div class="bg-white shadow rounded-md border p-6 min-h-[300px]">
            <template x-if="selectedForm">
                <div>
                    <div class="text-[#BD6F22] font-semibold text-lg mb-2" 
                         x-text="selectedForm.user.first_name + ' ' + selectedForm.user.last_name">
                    </div>
                    <p class="text-sm text-gray-800">Position: <span x-text="selectedForm.user.position || 'N/A'"></span></p>
                    <p class="text-sm text-gray-800 mb-4">ID Number: <span x-text="selectedForm.user.employee_id || 'N/A'"></span></p>

                    <h3 class="text-[#BD6F22] font-semibold text-lg mb-3">Request Details</h3>

                    <div class="mb-3">
                        <label class="block text-sm text-gray-700 mb-1">Date Range:</label>
                        <input type="text" class="border px-2 py-1 rounded text-sm w-full" readonly x-bind:value="selectedForm.date_range">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm text-gray-700 mb-1">Leave Type:</label>
                        <input type="text" class="border px-2 py-1 rounded text-sm w-full" readonly x-bind:value="selectedForm.leave_type">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm text-gray-700 mb-1">About:</label>
                        <textarea class="w-full border rounded p-2 text-sm" rows="4" readonly x-text="selectedForm.about || 'N/A'"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-700 mb-1">Attachment:</label>
                        <a class="block text-blue-600 hover:underline text-sm" 
                           :href="'/storage/' + selectedForm.file_path" 
                           target="_blank">
                            View Attachment
                        </a>
                    </div>

                    <div class="flex justify-end gap-2" x-show="selectedForm.status === 'Pending'">
                        <form method="POST" :action="`/hrAdmin/leave-forms/${selectedForm.id}/approve`">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Approve</button>
                        </form>
                        <form method="POST" :action="`/hrAdmin/leave-forms/${selectedForm.id}/decline`">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">Decline</button>
                        </form>
                    </div>
                </div>
            </template>

            <template x-if="!selectedForm">
                <div class="text-gray-500 text-sm">Select a leave request to view its details.</div>
            </template>
        </div>
    </div>
</div>
@endsection
