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
        applicants: [],

        init() {
            this.applicants = Array.from(document.querySelectorAll('tr[data-applicant-id]')).map(row => ({
                id: parseInt(row.dataset.applicantId),
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

        confirmStatus(action, id, name) {
            this.statusAction = action;
            this.selectedApplicant = { id, name };
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

                const applicantIndex = this.applicants.findIndex(app => app.id === result.application_id);
                const applicantRow = this.applicants[applicantIndex]?.element;

                if (applicantRow) {
                    applicantRow.classList.add('transition', 'opacity-0');
                    setTimeout(() => {
                        applicantRow.remove();
                    }, 300);
                }

                if (applicantIndex !== -1) {
                    this.applicants.splice(applicantIndex, 1);
                }

                this.selectedApplicant = null;
                this.statusAction = '';
            } catch (error) {
                alert('Error: ' + error.message);
            }
        },

        openSetInterview(id, name) {
            this.interviewApplicant = { id, name };

            // ✅ Get the interview date from the row attribute if it exists
            const row = document.querySelector(`tr[data-applicant-id='${id}']`);
            const dateStr = row?.getAttribute('data-interview-date');
            this.interviewDate = dateStr || '';

            this.showInterviewModal = true;
        },

        async submitInterviewDate() {
            if (!this.interviewDate) {
                alert('Please select a date.');
                return;
            }

            try {
                const response = await fetch(`/hrAdmin/applications/${this.interviewApplicant.id}/interview-date`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ interview_date: this.interviewDate })
                });

                if (!response.ok) throw new Error('Failed to set interview date');
                const result = await response.json();

                // ✅ Update the data attribute in the row for future usage
                const row = document.querySelector(`tr[data-applicant-id='${this.interviewApplicant.id}']`);
                if (row) {
                    row.setAttribute('data-interview-date', this.interviewDate);
                }

                this.showInterviewModal = false;
                alert('Interview date set successfully!');

            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    }));
});
