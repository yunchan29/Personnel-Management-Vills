
document.addEventListener('alpine:init', () => {
    Alpine.data('applicantsHandler', () => ({
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
                    approved: 'approved',
                    interviewed: 'passed',
                    declined: 'failed',
                    for_interview: 'scheduled for interview',
                    trained: 'trained'
                }[this.statusAction] || 'updated';

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

        openSetInterview(id, name) {
            this.interviewApplicant = { id, name };
            const row = document.querySelector(`tr[data-applicant-id='${id}']`);
            const dateStr = row?.getAttribute('data-interview-date') || '';
            const [date, time] = dateStr.split(' ');

            this.interviewDate = date || '';
            this.interviewTime = time || '';
            this.showInterviewModal = true;
        },

        async submitInterviewDate() {
            if (!this.interviewDate || !this.interviewTime) {
                alert('Please select both date and time.');
                return;
            }

            const interviewDateTime = `${this.interviewDate} ${this.interviewTime}`;

            try {
                const response = await fetch(`/hrAdmin/applications/${this.interviewApplicant.id}/interview-date`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        interview_date: interviewDateTime,
                        status: 'for_interview'
                    })
                });

                if (!response.ok) throw new Error('Failed to set interview date');
                const result = await response.json();

                const row = document.querySelector(`tr[data-applicant-id='${this.interviewApplicant.id}']`);
                if (row) {
                    row.setAttribute('data-interview-date', interviewDateTime);
                    row.setAttribute('data-status', 'for_interview');
                }

                const index = this.applicants.findIndex(a => a.id === this.interviewApplicant.id);
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
            }
        },

        openSetTraining(id, name) {
            this.trainingApplicant = { id, name };

            const row = document.querySelector(`tr[data-applicant-id='${id}']`);
            const range = row?.getAttribute('data-training-range') || '';

            if (range && this.trainingPicker) {
                const [start, end] = range.split(' - ');
                this.trainingPicker.setDateRange(start, end);
            }

            this.showTrainingModal = true;
        },

        async submitTrainingSchedule() {
            const selectedRange = this.$refs.trainingDateRange.value;

            if (!selectedRange || !selectedRange.includes(' - ')) {
                alert('Please select a valid training date range.');
                return;
            }

            try {
                const response = await fetch(`/hrAdmin/applications/${this.trainingApplicant.id}/training-date`, {
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

                const row = document.querySelector(`tr[data-applicant-id='${this.trainingApplicant.id}']`);
                if (row) row.setAttribute('data-training-range', selectedRange);

                const index = this.applicants.findIndex(a => a.id === this.trainingApplicant.id);
                if (index !== -1) {
                    this.applicants[index].training = selectedRange;
                    this.applicants = [...this.applicants];
                }

                this.feedbackMessage = 'Training schedule set successfully!';
                this.feedbackVisible = true;
                setTimeout(() => this.feedbackVisible = false, 3000);

                this.showTrainingModal = false;

            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    }));
});
