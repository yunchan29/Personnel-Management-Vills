<div id="archiveDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Archived Application Details</h3>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600 text-2xl font-bold">
                &times;
            </button>
        </div>

        <div id="modalContent" class="mt-4">
            <!-- Loading State -->
            <div id="loadingState" class="flex justify-center items-center py-8">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-2 text-gray-600">Loading details...</span>
            </div>

            <!-- Content Container -->
            <div id="detailsContent" class="hidden">
                <!-- Applicant Information Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Applicant Information</h4>
                    <div class="flex items-start space-x-4 mb-4">
                        <img id="applicantPhoto" src="{{ asset('images/default.png') }}" alt="Applicant" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200" onerror="this.src='{{ asset('images/default.png') }}'">
                        <div class="flex-1">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <p class="text-sm text-gray-600">Name</p>
                                    <p id="applicantName" class="font-semibold text-gray-900"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Email</p>
                                    <p id="applicantEmail" class="font-semibold text-gray-900"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Job Applied For</p>
                                    <p id="jobTitle" class="font-semibold text-gray-900"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Company</p>
                                    <p id="company" class="font-semibold text-gray-900"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-gray-50 p-3 rounded">
                        <div>
                            <p class="text-sm text-gray-600">Applied On</p>
                            <p id="appliedOn" class="font-semibold text-gray-900"></p>
                        </div>
                        <div id="reviewedAtSection" class="hidden">
                            <p class="text-sm text-gray-600">Reviewed At</p>
                            <p id="reviewedAt" class="font-semibold text-gray-900"></p>
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Application Status</h4>
                    <div class="flex items-center space-x-3">
                        <span id="statusBadge" class="px-4 py-2 rounded-full text-sm font-semibold"></span>
                        <span id="statusDate" class="text-sm text-gray-600"></span>
                    </div>
                </div>

                <!-- Interview Details Section -->
                <div id="interviewSection" class="mb-6 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Interview Details</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <p class="text-sm text-gray-600">Scheduled Date</p>
                                <p id="interviewDate" class="font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <span id="interviewStatus" class="inline-block px-3 py-1 rounded-full text-sm font-semibold"></span>
                            </div>
                            <div id="interviewSchedulerSection" class="hidden">
                                <p class="text-sm text-gray-600">Scheduled By</p>
                                <p id="interviewScheduler" class="font-semibold text-gray-900"></p>
                            </div>
                            <div id="rescheduledSection" class="hidden">
                                <p class="text-sm text-gray-600">Rescheduled To</p>
                                <p id="rescheduledDate" class="font-semibold text-gray-900"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Training Details Section -->
                <div id="trainingSection" class="mb-6 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Training Schedule</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <p class="text-sm text-gray-600">Start Date</p>
                                <p id="trainingStartDate" class="font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">End Date</p>
                                <p id="trainingEndDate" class="font-semibold text-gray-900"></p>
                            </div>
                            <div id="trainingTimeSection" class="hidden">
                                <p class="text-sm text-gray-600">Time</p>
                                <p id="trainingTime" class="font-semibold text-gray-900"></p>
                            </div>
                            <div id="trainingLocationSection" class="hidden">
                                <p class="text-sm text-gray-600">Location</p>
                                <p id="trainingLocation" class="font-semibold text-gray-900"></p>
                            </div>
                            <div id="trainingSchedulerSection" class="hidden">
                                <p class="text-sm text-gray-600">Scheduled By</p>
                                <p id="trainingScheduler" class="font-semibold text-gray-900"></p>
                            </div>
                            <div id="trainingScheduledAtSection" class="hidden">
                                <p class="text-sm text-gray-600">Originally Scheduled</p>
                                <p id="trainingScheduledAt" class="font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <span id="trainingStatus" class="inline-block px-3 py-1 rounded-full text-sm font-semibold"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evaluation Details Section -->
                <div id="evaluationSection" class="mb-6 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Evaluation Results</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="bg-white p-3 rounded shadow-sm">
                                <p class="text-xs text-gray-600 mb-1">Knowledge Score</p>
                                <p id="knowledgeScore" class="text-lg font-bold text-gray-900"></p>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm">
                                <p class="text-xs text-gray-600 mb-1">Skill Score</p>
                                <p id="skillScore" class="text-lg font-bold text-gray-900"></p>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm">
                                <p class="text-xs text-gray-600 mb-1">Participation Score</p>
                                <p id="participationScore" class="text-lg font-bold text-gray-900"></p>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm">
                                <p class="text-xs text-gray-600 mb-1">Professionalism Score</p>
                                <p id="professionalismScore" class="text-lg font-bold text-gray-900"></p>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded shadow-sm border-2 border-blue-200 mb-4">
                            <p class="text-xs text-gray-600 mb-1">Total Score</p>
                            <p id="totalScore" class="text-2xl font-bold text-blue-600"></p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <p class="text-sm text-gray-600">Result</p>
                                <span id="evaluationResult" class="inline-block px-4 py-2 rounded-full text-sm font-semibold"></span>
                            </div>
                            <div id="evaluatedBySection" class="hidden">
                                <p class="text-sm text-gray-600">Evaluated By</p>
                                <p id="evaluatedBy" class="font-semibold text-gray-900"></p>
                            </div>
                            <div id="evaluatedAtSection" class="hidden">
                                <p class="text-sm text-gray-600">Evaluation Date</p>
                                <p id="evaluatedAt" class="font-semibold text-gray-900"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contract Information Section -->
                <div id="contractSection" class="mb-6 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Contract Information</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div id="contractSigningSection" class="hidden">
                                <p class="text-sm text-gray-600">Contract Signing Schedule</p>
                                <p id="contractSigning" class="font-semibold text-gray-900"></p>
                            </div>
                            <div id="contractStartSection" class="hidden">
                                <p class="text-sm text-gray-600">Contract Start Date</p>
                                <p id="contractStart" class="font-semibold text-gray-900"></p>
                            </div>
                            <div id="contractEndSection" class="hidden">
                                <p class="text-sm text-gray-600">Contract End Date</p>
                                <p id="contractEnd" class="font-semibold text-gray-900"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Archive Information Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Archive Information</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div>
                            <p class="text-sm text-gray-600">Archived On</p>
                            <p id="archivedDate" class="font-semibold text-gray-900"></p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" class="close-modal px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded">
                        Close
                    </button>
                    @if(auth()->user()->role === 'hrStaff')
                    <form id="restoreForm" method="POST" class="inline hidden">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                            Restore Application
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Error State -->
            <div id="errorState" class="hidden text-center py-8">
                <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-2 text-red-600 font-semibold">Failed to load details</p>
                <p id="errorMessage" class="text-gray-600 text-sm"></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('archiveDetailsModal');
    const loadingState = document.getElementById('loadingState');
    const detailsContent = document.getElementById('detailsContent');
    const errorState = document.getElementById('errorState');
    const restoreForm = document.getElementById('restoreForm');

    // Close modal handlers
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    // Function to get status badge class
    function getStatusBadgeClass(status) {
        const statusClasses = {
            'pending': 'bg-gray-100 text-gray-800',
            'approved': 'bg-green-100 text-green-800',
            'declined': 'bg-red-100 text-red-800',
            'for_interview': 'bg-purple-100 text-purple-800',
            'interviewed': 'bg-indigo-100 text-indigo-800',
            'failed_interview': 'bg-red-100 text-red-800',
            'scheduled_for_training': 'bg-blue-100 text-blue-800',
            'trained': 'bg-green-100 text-green-800',
            'for_evaluation': 'bg-purple-100 text-purple-800',
            'passed_evaluation': 'bg-green-100 text-green-800',
            'failed_evaluation': 'bg-red-100 text-red-800',
            'hired': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800',
            // Interview/Training specific statuses
            'scheduled': 'bg-blue-100 text-blue-800',
            'rescheduled': 'bg-yellow-100 text-yellow-800',
            'completed': 'bg-green-100 text-green-800',
            'cancelled': 'bg-gray-100 text-gray-800',
            'failed': 'bg-red-100 text-red-800'
        };
        return statusClasses[status] || 'bg-gray-100 text-gray-800';
    }

    // Function to format status text
    function formatStatus(status) {
        return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    // Function to format date
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Function to format date only
    function formatDateOnly(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Function to reset all conditional sections
    function resetModalSections() {
        // Hide all conditional sections
        document.getElementById('reviewedAtSection').classList.add('hidden');
        document.getElementById('interviewSection').classList.add('hidden');
        document.getElementById('trainingSection').classList.add('hidden');
        document.getElementById('evaluationSection').classList.add('hidden');
        document.getElementById('contractSection').classList.add('hidden');

        // Hide subsections
        document.getElementById('interviewSchedulerSection').classList.add('hidden');
        document.getElementById('rescheduledSection').classList.add('hidden');
        document.getElementById('trainingTimeSection').classList.add('hidden');
        document.getElementById('trainingLocationSection').classList.add('hidden');
        document.getElementById('trainingSchedulerSection').classList.add('hidden');
        document.getElementById('trainingScheduledAtSection').classList.add('hidden');
        document.getElementById('evaluatedBySection').classList.add('hidden');
        document.getElementById('evaluatedAtSection').classList.add('hidden');
        document.getElementById('contractSigningSection').classList.add('hidden');
        document.getElementById('contractStartSection').classList.add('hidden');
        document.getElementById('contractEndSection').classList.add('hidden');
    }

    // Function to populate modal with data
    function populateModal(data) {
        // Reset all sections first to clear previous data
        resetModalSections();

        // Applicant Information
        document.getElementById('applicantPhoto').src = data.user.profile_picture ? '{{ asset('storage') }}/' + data.user.profile_picture : '{{ asset('images/default.png') }}';
        document.getElementById('applicantName').textContent = data.user.name;
        document.getElementById('applicantEmail').textContent = data.user.email;
        document.getElementById('jobTitle').textContent = data.job.title;
        document.getElementById('company').textContent = data.job.company;

        // Application Dates
        document.getElementById('appliedOn').textContent = formatDate(data.created_at);
        if (data.reviewed_at) {
            document.getElementById('reviewedAtSection').classList.remove('hidden');
            document.getElementById('reviewedAt').textContent = formatDate(data.reviewed_at);
        }

        // Status
        const statusBadge = document.getElementById('statusBadge');
        statusBadge.textContent = formatStatus(data.status);
        statusBadge.className = 'px-4 py-2 rounded-full text-sm font-semibold ' + getStatusBadgeClass(data.status);
        document.getElementById('statusDate').textContent = 'as of ' + formatDate(data.updated_at);

        // Interview Details
        if (data.interview) {
            document.getElementById('interviewSection').classList.remove('hidden');
            document.getElementById('interviewDate').textContent = formatDate(data.interview.scheduled_at);

            // Map interview status based on application outcome
            let displayStatus = data.interview.status;
            if (data.status === 'failed_interview') {
                displayStatus = 'failed';
            } else if (['interviewed', 'scheduled_for_training', 'trained', 'for_evaluation', 'passed_evaluation', 'hired'].includes(data.status)) {
                displayStatus = 'completed';
            }

            const interviewStatus = document.getElementById('interviewStatus');
            interviewStatus.textContent = formatStatus(displayStatus);
            interviewStatus.className = 'inline-block px-3 py-1 rounded-full text-sm font-semibold ' + getStatusBadgeClass(displayStatus);

            if (data.interview.scheduled_by) {
                document.getElementById('interviewSchedulerSection').classList.remove('hidden');
                document.getElementById('interviewScheduler').textContent = data.interview.scheduled_by;
            }

            if (data.interview.rescheduled_at) {
                document.getElementById('rescheduledSection').classList.remove('hidden');
                document.getElementById('rescheduledDate').textContent = formatDate(data.interview.rescheduled_at);
            }
        }

        // Training Details
        if (data.training_schedule) {
            document.getElementById('trainingSection').classList.remove('hidden');
            document.getElementById('trainingStartDate').textContent = formatDateOnly(data.training_schedule.start_date);
            document.getElementById('trainingEndDate').textContent = formatDateOnly(data.training_schedule.end_date);

            if (data.training_schedule.start_time && data.training_schedule.end_time) {
                document.getElementById('trainingTimeSection').classList.remove('hidden');
                document.getElementById('trainingTime').textContent = data.training_schedule.start_time + ' - ' + data.training_schedule.end_time;
            }

            if (data.training_schedule.location) {
                document.getElementById('trainingLocationSection').classList.remove('hidden');
                document.getElementById('trainingLocation').textContent = data.training_schedule.location;
            }

            if (data.training_schedule.scheduled_by) {
                document.getElementById('trainingSchedulerSection').classList.remove('hidden');
                document.getElementById('trainingScheduler').textContent = data.training_schedule.scheduled_by;
            }

            if (data.training_schedule.scheduled_at) {
                document.getElementById('trainingScheduledAtSection').classList.remove('hidden');
                document.getElementById('trainingScheduledAt').textContent = formatDate(data.training_schedule.scheduled_at);
            }

            // Map training status based on application outcome
            let trainingDisplayStatus = data.training_schedule.status;
            if (data.status === 'failed_evaluation') {
                trainingDisplayStatus = 'failed';
            } else if (['trained', 'for_evaluation', 'passed_evaluation', 'hired'].includes(data.status)) {
                trainingDisplayStatus = 'completed';
            }

            const trainingStatus = document.getElementById('trainingStatus');
            trainingStatus.textContent = formatStatus(trainingDisplayStatus);
            trainingStatus.className = 'inline-block px-3 py-1 rounded-full text-sm font-semibold ' + getStatusBadgeClass(trainingDisplayStatus);
        }

        // Evaluation Details
        if (data.evaluation) {
            document.getElementById('evaluationSection').classList.remove('hidden');

            // Display individual scores
            document.getElementById('knowledgeScore').textContent = data.evaluation.knowledge + '/25';
            document.getElementById('skillScore').textContent = data.evaluation.skill + '/25';
            document.getElementById('participationScore').textContent = data.evaluation.participation + '/25';
            document.getElementById('professionalismScore').textContent = data.evaluation.professionalism + '/25';
            document.getElementById('totalScore').textContent = data.evaluation.total_score + '/100';

            const evaluationResult = document.getElementById('evaluationResult');
            evaluationResult.textContent = formatStatus(data.evaluation.result);
            evaluationResult.className = 'inline-block px-4 py-2 rounded-full text-sm font-semibold ' +
                (data.evaluation.result === 'passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');

            if (data.evaluation.evaluated_by) {
                document.getElementById('evaluatedBySection').classList.remove('hidden');
                document.getElementById('evaluatedBy').textContent = data.evaluation.evaluated_by;
            }

            if (data.evaluation.evaluated_at) {
                document.getElementById('evaluatedAtSection').classList.remove('hidden');
                document.getElementById('evaluatedAt').textContent = formatDate(data.evaluation.evaluated_at);
            }
        }

        // Contract Information (for hired applicants)
        if (data.contract_signing_schedule || data.contract_start || data.contract_end) {
            document.getElementById('contractSection').classList.remove('hidden');

            if (data.contract_signing_schedule) {
                document.getElementById('contractSigningSection').classList.remove('hidden');
                document.getElementById('contractSigning').textContent = formatDate(data.contract_signing_schedule);
            }

            if (data.contract_start) {
                document.getElementById('contractStartSection').classList.remove('hidden');
                document.getElementById('contractStart').textContent = formatDateOnly(data.contract_start);
            }

            if (data.contract_end) {
                document.getElementById('contractEndSection').classList.remove('hidden');
                document.getElementById('contractEnd').textContent = formatDateOnly(data.contract_end);
            }
        }

        // Archive Information
        document.getElementById('archivedDate').textContent = formatDate(data.updated_at);

        // Set restore form action (only for HR Staff with restorable statuses)
        const userRole = '{{ auth()->user()->role }}';
        if (userRole === 'hrStaff') {
            const restoreForm = document.getElementById('restoreForm');
            if (restoreForm) {
                // Define restorable statuses
                const restorableStatuses = ['declined', 'failed_interview', 'failed_evaluation'];

                // Only show restore button for restorable statuses
                if (restorableStatuses.includes(data.status)) {
                    restoreForm.classList.remove('hidden');
                    restoreForm.action = `/hrStaff/archive/${data.id}/restore`;

                    // Clear any existing method inputs to prevent duplicates
                    const existingMethod = restoreForm.querySelector('input[name="_method"]');
                    if (existingMethod) {
                        existingMethod.remove();
                    }

                    // Add PUT method for HR Staff
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    restoreForm.appendChild(methodInput);

                    // Store the status for the restore handler
                    restoreForm.dataset.applicationStatus = data.status;
                } else {
                    // Hide restore button for other statuses
                    restoreForm.classList.add('hidden');
                }
            }
        }
    }

    // Open modal function
    window.openArchiveDetailsModal = function(applicationId) {
        modal.classList.remove('hidden');
        loadingState.classList.remove('hidden');
        detailsContent.classList.add('hidden');
        errorState.classList.add('hidden');

        const userRole = '{{ auth()->user()->role }}';
        const routePrefix = userRole === 'hrAdmin' ? 'hrAdmin' : 'hrStaff';

        fetch(`/${routePrefix}/archive/${applicationId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch application details');
                }
                return response.json();
            })
            .then(data => {
                loadingState.classList.add('hidden');
                detailsContent.classList.remove('hidden');
                populateModal(data);
            })
            .catch(error => {
                loadingState.classList.add('hidden');
                errorState.classList.remove('hidden');
                document.getElementById('errorMessage').textContent = error.message;
            });
    };

    // Handle restore form submission (only for HR Staff)
    if (restoreForm) {
        restoreForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const applicationStatus = restoreForm.dataset.applicationStatus;

            // Handle DECLINED - auto-restore without modal
            if (applicationStatus === 'declined') {
                Swal.fire({
                    title: 'Restore Application?',
                    text: 'This will restore the application to Pending status for reconsideration.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, restore it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        restoreForm.submit();
                    }
                });
                return;
            }

            // Handle FAILED_INTERVIEW - show modal with choices
            if (applicationStatus === 'failed_interview') {
                Swal.fire({
                    title: 'Restore Application',
                    text: 'Where should this applicant be placed after restoration?',
                    icon: 'question',
                    input: 'radio',
                    inputOptions: {
                        'for_interview': 'For Interview (reschedule another interview)',
                        'approved': 'Approved (skip interview process)'
                    },
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Please select a status option!';
                        }
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Restore',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: (selectedStatus) => {
                        // Add the selected status to the form as a hidden input
                        const existingStatusInput = restoreForm.querySelector('input[name="status"]');
                        if (existingStatusInput) {
                            existingStatusInput.remove();
                        }

                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = selectedStatus;
                        restoreForm.appendChild(statusInput);

                        // Submit the form
                        restoreForm.submit();

                        // Return a promise that never resolves to keep the loader showing
                        return new Promise(() => {});
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                });
                return;
            }

            // Handle FAILED_EVALUATION - show modal with choices
            if (applicationStatus === 'failed_evaluation') {
                Swal.fire({
                    title: 'Restore Application',
                    text: 'Where should this applicant be placed after restoration?',
                    icon: 'question',
                    input: 'radio',
                    inputOptions: {
                        'for_evaluation': 'For Re-evaluation',
                        'scheduled_for_training': 'Redo training'
                    },
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Please select a status option!';
                        }
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Restore',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: (selectedStatus) => {
                        // Add the selected status to the form as a hidden input
                        const existingStatusInput = restoreForm.querySelector('input[name="status"]');
                        if (existingStatusInput) {
                            existingStatusInput.remove();
                        }

                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = selectedStatus;
                        restoreForm.appendChild(statusInput);

                        // Submit the form
                        restoreForm.submit();

                        // Return a promise that never resolves to keep the loader showing
                        return new Promise(() => {});
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                });
                return;
            }
        });
    }
});
</script>
