
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
            console.log('Running init()...');

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

                console.log('Applicants loaded:', this.applicants);
                
                  const ref = this.$refs.trainingDateRange;
            if (ref) {
                // only one Litepicker here, stored on the component
                this.trainingPicker = new Litepicker({
                element: ref,
                singleMode: false,
                format: 'MM/DD/YYYY',
                numberOfMonths: 2,
                numberOfColumns: 2,
                // optional: auto-apply when you pick
                autoApply: true,
                });
                console.log('Training picker initialized', this.trainingPicker);
            }  else {
                    console.warn('Date range input ref not found');
                }
            }, 50);
        },


        filteredApplicants() {
            return this.applicants.filter(applicant => this.showAll || !applicant.training);
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
                    setTimeout(() => {
                        this.feedbackVisible = false;
                        location.reload(); // ✅ Reload the page after feedback disappears
                    }, 3000);

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

        openSetTraining(id, name, range) {
            console.log("Opening training modal...");
            console.log("Applicant ID:", id);
            console.log("Name:", name);
            console.log("Training Range:", range);

            this.trainingApplicant = { id, name };
            this.trainingRange = range;       // keep for your POST if you need it
            this.showTrainingModal = true;

        this.$nextTick(() => {
            // do nothing if there’s no picker yet
            if (!this.trainingPicker) return;

            // if we have a range string like "08/01/2025 - 08/07/2025"
            if (range && range.includes(' - ')) {
            const [start, end] = range.split(' - ');

            // set the input’s visible value
            this.$refs.trainingDateRange.value = range;

            // tell Litepicker to update its selected range
            this.trainingPicker.setDateRange(start, end, true);
            console.log('Picker dateRange set to:', start, end);
            } else {
            // clear it if there was no existing range
            this.$refs.trainingDateRange.value = '';
            this.trainingPicker.clearSelection();
            }
        });
        },



        async submitTrainingSchedule() {
            const selectedRange = this.$refs.trainingDateRange.value;

            if (!selectedRange || !selectedRange.includes(' - ')) {
                alert('Please select a valid training date range.');
                return;
            }

            try {
                const response = await fetch(`/hrAdmin/training-schedule/${this.trainingApplicant.id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    training_schedule: selectedRange
                })
            });

                if (!response.ok) throw new Error('Failed to set training schedule');

                const result = await response.json();

                // Update training schedule data in table row
                const row = document.querySelector(`tr[data-applicant-id='${this.trainingApplicant.id}']`);
                if (row) row.setAttribute('data-training-range', selectedRange);

                const index = this.applicants.findIndex(a => a.id === this.trainingApplicant.id);
                if (index !== -1) {
                    this.applicants[index].training = selectedRange;
                    this.applicants[index].status = 'scheduled_for_training'; // Update status in frontend too
                    this.applicants = [...this.applicants];
                }

                this.feedbackMessage = result.message || 'Training schedule set successfully!';
                this.feedbackVisible = true;
                setTimeout(() => location.reload(), 2000); // Reload after 2s delay

                this.showTrainingModal = false;

            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

    }));
});
