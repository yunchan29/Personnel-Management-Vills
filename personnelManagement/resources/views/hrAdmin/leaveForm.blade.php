@extends('layouts.hrAdmin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8"
     x-data="draggableModal()"
     x-init="initDrag()"
>
    <h1 class="text-2xl font-semibold text-[#BD6F22] mb-6">Leave Form</h1>

    <!-- Tabs -->
    <div class="flex border-b mb-6">
        <template x-for="status in ['Pending', 'Approved', 'Declined']" :key="status">
            <button 
                class="px-4 py-2 font-medium transition"
                :class="{
                    'border-b-2 border-[#BD6F22] text-[#BD6F22]': selectedStatus === status,
                    'text-gray-500': selectedStatus !== status
                }"
                @click="selectedStatus = status; selectedForm = null; showModal = false"
                x-text="status"
            ></button>
        </template>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column -->
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

        <!-- Right Panel -->
        <div class="bg-white shadow rounded-md border p-6 min-h-[300px]" 
             x-show="selectedForm && !showModal" x-transition>
            <template x-if="selectedForm">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="text-[#BD6F22] font-semibold text-lg mb-1" 
                                x-text="selectedForm.user.first_name + ' ' + selectedForm.user.last_name">
                            </div>
                            <p class="text-sm text-gray-800">Position: <span x-text="selectedForm.user.position || 'N/A'"></span></p>
                            <p class="text-sm text-gray-800">ID Number: <span x-text="selectedForm.user.employee_id || 'N/A'"></span></p>
                        </div>
                        <button @click="openModal" title="Open as Modal">
                            <svg class="w-5 h-5 text-gray-500 hover:text-[#BD6F22]" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h6v6m0-6L10 14m-7 7h6v-6"></path>
                            </svg>
                        </button>
                    </div>

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
                        <button type="button"
                            class="text-blue-600 hover:underline text-sm"
                            @click="openAttachmentModal('/storage/' + selectedForm.file_path)">
                            View Attachment
                        </button>
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
        </div>
    </div>

    <!-- Leave Form Modal -->
    <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center"
         x-show="showModal"
         x-transition
         @click.self="closeModal">
        <div class="bg-white rounded-lg max-w-lg w-full p-6 absolute shadow-xl"
             :style="`transform: translate(${x}px, ${y}px)`">
            <div class="cursor-move mb-4 text-[#BD6F22] font-semibold text-lg flex justify-between items-center"
                 @mousedown.prevent="startDrag">
                Leave Form Details
                <button @click="closeModal" class="text-gray-500 hover:text-black text-xl">&times;</button>
            </div>

            <template x-if="selectedForm">
                <div>
                    <div class="text-[#BD6F22] font-semibold text-lg mb-2" 
                         x-text="selectedForm.user.first_name + ' ' + selectedForm.user.last_name"></div>
                    <p class="text-sm text-gray-800">Position: <span x-text="selectedForm.user.position || 'N/A'"></span></p>
                    <p class="text-sm text-gray-800 mb-4">ID Number: <span x-text="selectedForm.user.employee_id || 'N/A'"></span></p>

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
                        <button class="text-blue-600 hover:underline text-sm" @click="openAttachmentModal('/storage/' + selectedForm.file_path)">
                            View Attachment
                        </button>
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
        </div>
    </div>

    <!-- Attachment Modal -->
    <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center"
         x-show="showAttachmentModal"
         x-transition
         @click.self="closeAttachmentModal">
        <div class="bg-white rounded-lg max-w-3xl w-full p-4 absolute shadow-xl"
             :style="`transform: translate(${x}px, ${y}px)`">
            <div class="cursor-move mb-4 text-[#BD6F22] font-semibold text-lg flex justify-between items-center"
                 @mousedown.prevent="startDrag">
                Attachment Viewer
                <button @click="closeAttachmentModal" class="text-gray-500 hover:text-black text-xl">&times;</button>
            </div>
            <div class="border rounded overflow-hidden">
                <iframe 
                    :src="attachmentUrl" 
                    class="w-full h-[500px]" 
                    frameborder="0">
                </iframe>
            </div>
        </div>
    </div>
</div>

<!-- Alpine Logic -->
<script>
function draggableModal() {
    return {
        selectedStatus: 'Pending',
        selectedForm: null,
        showModal: false,
        showAttachmentModal: false,
        attachmentUrl: '',
        x: 0,
        y: 0,
        dragging: false,
        offsetX: 0,
        offsetY: 0,

        selectForm(event) {
            const formData = event.currentTarget.dataset.form;
            if (formData) {
                try {
                    this.selectedForm = JSON.parse(formData);
                    this.showModal = false;
                    this.showAttachmentModal = false;
                } catch (e) {
                    console.error('Invalid form data:', formData);
                }
            }
        },
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        openAttachmentModal(url) {
            this.attachmentUrl = url;
            this.showAttachmentModal = true;
        },
        closeAttachmentModal() {
            this.showAttachmentModal = false;
            this.attachmentUrl = '';
        },
        initDrag() {
            this.doDrag = this.doDrag.bind(this);
            this.stopDrag = this.stopDrag.bind(this);
        },
        startDrag(event) {
            this.dragging = true;
            this.offsetX = event.clientX - this.x;
            this.offsetY = event.clientY - this.y;
            document.addEventListener('mousemove', this.doDrag);
            document.addEventListener('mouseup', this.stopDrag);
        },
        doDrag(event) {
            if (!this.dragging) return;
            this.x = event.clientX - this.offsetX;
            this.y = event.clientY - this.offsetY;
        },
        stopDrag() {
            this.dragging = false;
            document.removeEventListener('mousemove', this.doDrag);
            document.removeEventListener('mouseup', this.stopDrag);
        }
    };
}
</script>
@endsection
