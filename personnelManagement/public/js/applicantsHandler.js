
document.addEventListener('alpine:init', () => {
    Alpine.data('applicantsHandler', () => ({
        loading: false,
        pageContext: '',
        showModal: false,
        resumeUrl: '',
        showProfile: false,
        activeProfileId: null,

        showStatusModal: false,
        statusAction: '',
        selectedApplicant: null,

        showInterviewModal: false,
        interviewApplicant: null,
        interviewDate: '',
        interviewTime: '',

        showTrainingModal: false,
        trainingApplicant: null,
        trainingPicker: null,

        applicants: [],
        removedApplicants: [],
        feedbackMessage: '',
        feedbackVisible: false,
        showAll: false,

        init() {
            setTimeout(() => {
                this.applicants = Array.from(document.querySelectorAll('tr[data-applicant-id]')).map(row => ({
                    id: parseInt(row.dataset.applicantId),
                    name: row.querySelector('[data-name]')?.textContent.trim() || '',
                    position: row.querySelector('[data-position]')?.textContent.trim() || '',
                    company: row.querySelector('[data-company]')?.textContent.trim() || '',
                    applied_on: row.querySelector('[data-applied-on]')?.textContent.trim() || '',
                    resume: row.querySelector('[data-resume-url]')?.dataset.resumeUrl || '',
                    status: row.dataset.status || '',
                    training: row.dataset.trainingRange || '',
                    element: row
                }));

                const ref = this.$refs.trainingDateRange;
                if (ref) {
                    this.trainingPicker = new Litepicker({
                        element: ref,
                        singleMode: false,
                        format: 'MM/DD/YYYY',
                        numberOfMonths: 2,
                        numberOfColumns: 2,
                    });
                }
                
            }, 50);
        },

        filteredApplicants() {
            return this.applicants.filter(applicant => this.showAll || !applicant.training);
        },

        getApplicationById(id) {
            return this.applicants.find(applicant => applicant.id === id);
        },

        openResume(url) {
            this.resumeUrl = url;
            this.showModal = true;
        },

        openProfile(id) {
            this.activeProfileId = id;
            this.showProfile = true;
        },

        openStatusModal(id, name) {
            const found = this.applicants.find(a => a.id === id);
            this.selectedApplicant = { id, name, status: found?.status || '' };
            this.showStatusModal = true;
        },

        confirmStatus(action, id, name) {
            this.statusAction = action;
            this.selectedApplicant = { id, name, status: action };
            this.showStatusModal = true;
        },

        async submitStatusChange() {
            try {
                const response = await fetch(`/hrAdmin/applications/${this.selectedApplicant.id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: this.statusAction })
                });

                if (!response.ok) throw new Error('Failed to update status');
                const result = await response.json();

                const row = document.querySelector(`tr[data-applicant-id='${result.application_id}']`);
                if (row) row.setAttribute('data-status', this.statusAction);

                const index = this.applicants.findIndex(a => a.id === result.application_id);
                if (index !== -1) {
                    this.applicants[index].status = this.statusAction;
                    this.applicants = [...this.applicants];
                }

                const label = {
                    approved: 'Approved',
                    interviewed: 'Passed',
                    declined: 'Failed',
                    for_interview: 'Scheduled for Interview',
                    trained: 'Trained'
                }[this.statusAction] || 'Updated';

               this.feedbackMessage = `Applicant ${label} successfully.`;
               this.feedbackVisible = true;

                // Wait 2.5 seconds, then reload the page
                setTimeout(() => {
                    this.feedbackVisible = false;
                    location.reload();
                }, 2500);

                if (['interviewed', 'declined', 'trained'].includes(this.statusAction)) {
                    setTimeout(() => this.removedApplicants.push(result.application_id), 300);
                }

                this.selectedApplicant = null;
                this.statusAction = '';
                this.showStatusModal = false;

            } catch (error) {
                alert('Error: ' + error.message);
            }
        },

        openSetInterview(applicationId, name, userId, rawInterviewDate = '') {
            this.interviewApplicant = {
                application_id: applicationId,
                user_id: userId,
                name: name
            };

            const [date, time] = rawInterviewDate.split(' ');
            this.interviewDate = date || '';
            this.interviewTime = time || '';

            // ✅ Store original for comparison
            this.originalDate = date || '';
            this.originalTime = time || '';

            this.showInterviewModal = true;
        },

        async submitInterviewDate() {
            if (!this.interviewDate || !this.interviewTime) {
                alert('Please select both date and time.');
                return;
            }

            const interviewDateTime = `${this.interviewDate} ${this.interviewTime}`;
            const originalDateTime = `${this.originalDate} ${this.originalTime}`;

            console.log('Original:', originalDateTime);
            console.log('New:', interviewDateTime);

            // ✅ Check if no changes were made
            if (this.originalDate && this.originalTime && interviewDateTime === originalDateTime) {
                this.feedbackMessage = 'No changes were made to the interview schedule.';
                this.feedbackVisible = true;
                setTimeout(() => this.feedbackVisible = false, 3000);
                this.showInterviewModal = false;
                return; // 🚫 Skip API call
            }

            const isReschedule = this.originalDate && this.originalTime;

            this.loading = true;

            const proceed = async () => {
                try {
                    const response = await fetch(`/hrAdmin/interviews`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            application_id: this.interviewApplicant.application_id,
                            user_id: this.interviewApplicant.user_id,
                            scheduled_at: interviewDateTime,
                            is_reschedule: isReschedule,
                        })
                    });

                    if (!response.ok) throw new Error('Failed to set interview date');
                    const result = await response.json();

                    const row = document.querySelector(`tr[data-applicant-id='${this.interviewApplicant.application_id}']`);
                    if (row) {
                        row.setAttribute('data-interview-date', interviewDateTime);
                        row.setAttribute('data-status', 'for_interview');
                    }

                    const index = this.applicants.findIndex(a => a.id === this.interviewApplicant.application_id);
                    if (index !== -1) {
                        this.applicants[index].status = 'for_interview';
                        this.applicants = [...this.applicants];
                    }

                    this.feedbackMessage = 'Interview date set successfully!';
                    this.feedbackVisible = true;
                    setTimeout(() => this.feedbackVisible = false, 3000);

                    this.showInterviewModal = false;

                } catch (error) {
                    alert('Error: ' + error.message);
                } finally {
                    this.loading = false; // ✅ Stop loading
                }
            };

            if (isReschedule) {
                Swal.fire({
                    title: "You're about to reschedule",
                    text: "Do you want to continue?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#BD6F22",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, reschedule"
                }).then(result => {
                    if (result.isConfirmed) {
                        proceed();
                    } else {
                        this.loading = false; // ✅ Stop loading if cancelled
                    }
                });
            } else {
                proceed();
            }
        },

        openSetTraining(id, name) {
            const applicant = this.applicants.find(a => a.id === id);
            const existingRange = applicant?.training || null;

            this.trainingApplicant = { id, name };
            this.initialTrainingRange = existingRange;
            this.selectedTrainingRange = existingRange || ''; // store selected range
            this.showTrainingModal = true;

            this.$nextTick(() => {
                const input = this.$refs.trainingDateRange;

                // Clear previous value
                input.value = '';

                // Destroy existing picker if any
                if (this.trainingPicker) {
                    this.trainingPicker.destroy();
                    this.trainingPicker = null;
                }

                // Recreate picker
                this.trainingPicker = new Litepicker({
                    element: input,
                    singleMode: false,
                    format: 'MM/DD/YYYY',
                    setup: (picker) => {
                        picker.on('selected', (start, end) => {
                            this.selectedTrainingRange = `${start.format('MM/DD/YYYY')} - ${end.format('MM/DD/YYYY')}`;
                        });
                    }
                });

                // Set range if exists
                if (existingRange && existingRange.includes(' - ')) {
                    const [start, end] = existingRange.split(' - ');
                    this.trainingPicker.setDateRange(start, end);
                    input.value = existingRange;
                }
            });
        },


        async submitTrainingSchedule() {
            const selectedRange = this.$refs.trainingDateRange.value;

            if (!selectedRange || !selectedRange.includes(' - ')) {
                alert('Please select a valid training date range.');
                return;
            }

            const isReschedule = this.initialTrainingRange && this.initialTrainingRange !== selectedRange;

            try {
                const response = await fetch(`/hrAdmin/applications/${this.trainingApplicant.id}/training-date`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        training_schedule: selectedRange,
                        reschedule: isReschedule // 👈 send flag
                    })
                });

                if (!response.ok) throw new Error('Failed to set training schedule');
                const result = await response.json();

                const row = document.querySelector(`tr[data-applicant-id='${this.trainingApplicant.id}']`);
                if (row) row.setAttribute('data-training-range', selectedRange);

                const index = this.applicants.findIndex(a => a.id === this.trainingApplicant.id);
                if (index !== -1) {
                    this.applicants[index].training = selectedRange;
                    this.applicants = [...this.applicants];
                }

                this.feedbackMessage = result.message || 'Training schedule set successfully!';
                this.feedbackVisible = true;
                setTimeout(() => this.feedbackVisible = false, 3000);

                this.showTrainingModal = false;

            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    }));
});
