document.addEventListener('alpine:init', () => {
    Alpine.data('applicantsHandler', () => ({
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
        applicants: [],
        removedApplicants: [],
        feedbackMessage: '',
        feedbackVisible: false,
        showAll: false,

        init() {
            this.applicants = Array.from(document.querySelectorAll('tr[data-applicant-id]')).map(row => ({
                id: parseInt(row.dataset.applicantId),
                status: row.dataset.status || '',
                element: row
            }));
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

                this.showStatusModal = false;

                const row = document.querySelector(`tr[data-applicant-id='${result.application_id}']`);
                if (row) {
                    row.setAttribute('data-status', this.statusAction);
                }

                const index = this.applicants.findIndex(a => a.id === result.application_id);
                if (index !== -1) {
                    this.applicants[index].status = this.statusAction;
                    this.applicants = [...this.applicants]; // trigger reactivity
                }

                const label = {
                    approved: 'approved',
                    interviewed: 'passed',
                    declined: 'failed',
                    for_interview: 'scheduled for interview'
                }[this.statusAction] || 'updated';

                this.feedbackMessage = `Applicant ${label} successfully.`;
                this.feedbackVisible = true;
                setTimeout(() => this.feedbackVisible = false, 3000);

                if (['interviewed', 'declined'].includes(this.statusAction)) {
                    setTimeout(() => {
                        this.removedApplicants.push(result.application_id);
                    }, 300);
                }

                this.selectedApplicant = null;
                this.statusAction = '';

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
        }
    }));
});
