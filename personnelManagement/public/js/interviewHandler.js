document.addEventListener('alpine:init', () => {
    Alpine.data('interviewHandler', (parent) => ({
            get selectedApplicants() {
            return parent.selectedApplicants;
            },
            set selectedApplicants(val) {
            parent.selectedApplicants = val;
            },
            get applicants() {
            return parent.applicants || [];
            },
            get removedApplicants() {
            return parent.removedApplicants || [];
            },

        // 🔹 State
        showInterviewModal: false,
        interviewMode: 'single', // single | bulk | bulk-reschedule
        interviewApplicant: null,
        interviewDate: '',
        interviewTime: '',
        interviewPeriod: 'PM',
        originalNormalizedDT: '',
        loading: false,
        feedbackMessage: '',
        feedbackVisible: false,

        init() {
            this.$watch('interviewTime', (val) => {
                // 8–11 → AM
                if ([8,9,10,11].includes(val)) {
                    this.interviewPeriod = 'AM';
                }
                // 12–4 → PM
                if ([12,1,2,3,4].includes(val)) {
                    this.interviewPeriod = 'PM';
                }
            });
            // Ensure master checkbox sync on init
            this.$nextTick(() => this.updateMasterCheckbox());
        },

        // 🔹 Helpers - using TimeUtils
        to24h(hour, period) {
            return TimeUtils.to24h(hour, period);
        },

        to12h(hour24) {
            return TimeUtils.to12h(hour24);
        },

        formatDisplay(hour, period) {
            return TimeUtils.formatDisplay(hour, period);
        },

        // Status for interview component
        statusMap: {
        interviewed: { label: 'Passed', class: 'bg-green-200 text-green-800' },
        declined: { label: 'Failed', class: 'bg-red-200 text-red-800' },
        for_interview: { label: 'For Interview', class: 'bg-yellow-200 text-yellow-800' },
        default: { label: 'Pending', class: 'bg-gray-200 text-gray-800' },
        },


        // 🔹 Toggle all visible checkboxes - using CheckboxUtils
        toggleSelectAll(event) {
            this.selectedApplicants = CheckboxUtils.toggleSelectAll(
                event,
                this.selectedApplicants,
                '.applicant-checkbox',
                'application_id'
            );
            this.updateMasterCheckbox();
        },

        // 🔹 Update master checkbox visual state - using CheckboxUtils
        updateMasterCheckbox() {
            CheckboxUtils.updateMasterCheckbox(
                this.$root,
                this.selectedApplicants,
                '.applicant-checkbox:not(:disabled)',
                'masterCheckbox',
                'application_id'
            );
        },

        // 🔹 Toggle a single checkbox - using CheckboxUtils
        toggleItem(event, id) {
            this.selectedApplicants = CheckboxUtils.toggleItem(
                event,
                this.selectedApplicants,
                'application_id'
            );
            this.updateMasterCheckbox();
        },

        // 🔹 Helpers - using CheckboxUtils
        getLocalCheckboxes() {
            return Array.from(document.querySelectorAll('.applicant-checkbox'))
                .filter(cb => cb.offsetParent !== null);
        },

        getApplicantId(applicant) {
            return CheckboxUtils.getApplicantId(applicant);
        },

        isAllSelected() {
            const total = this.getLocalCheckboxes().length;
            return (
                this.selectedApplicants.length > 0 &&
                this.selectedApplicants.length === total
            );
        },

        isIndeterminate() {
            const total = this.getLocalCheckboxes().length;
            return (
                this.selectedApplicants.length > 0 &&
                this.selectedApplicants.length < total
            );
        },


        // 🔹 Bulk status change
        async submitBulkStatusChange() {
            console.log("🚀 [submitBulkStatusChange] triggered");

            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }
            
            this.loading = true;

            const ids = this.selectedApplicants.map(app => app.application_id);

            try {
                // Validate CSRF token exists
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    throw new Error('CSRF token not found. Please refresh the page.');
                }

                const res = await fetch('/hrAdmin/applications/bulk-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        ids,
                        status: this.bulkStatusAction
                    })
                }).then(r => r.json());

                if (res.success) {
                    this.feedbackMessage = res.message || `Applicants ${this.bulkStatusAction} successfully.`;
                    this.feedbackVisible = true;

                    setTimeout(() => {
                        this.feedbackVisible = false;
                        location.reload();
                    }, 3000);

                    this.fetchApplicants?.();
                    this.showBulkStatusModal = false;
                } else {
                    Swal.fire("Error", res.errors ? JSON.stringify(res.errors) : "Something went wrong", "error");
                }
            } catch (err) {
                Swal.fire("Error", "Request failed", "error");
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        // 🔹 Open single interview modal
        openSetInterview(applicationId, name, userId, scheduledAt = '') {
            this.interviewMode = 'single';
            this.interviewApplicant = { application_id: applicationId, user_id: userId, name };

            if (scheduledAt) {
                const [datePart, timePartRaw = '00:00:00'] = scheduledAt.split(' ');
                const hour24 = parseInt(timePartRaw.split(':')[0], 10);
                let hour12 = hour24 % 12 || 12;
                const period = hour24 < 12 ? 'AM' : 'PM';

                this.interviewDate = datePart;
                this.interviewTime = hour12;
                this.interviewPeriod = period;
                this.originalNormalizedDT = `${datePart} ${String(hour24).padStart(2,'0')}:00:00`;
            } else {
                this.resetInterviewForm();
            }
            this.showInterviewModal = true;
        },

        // 🔹 Open bulk manage status modal
        openBulkManage() {
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }

            // 🔸 Validation for bulk-manage-status
            const withoutInterview = this.selectedApplicants.filter(app => !app.has_schedule);
            if (withoutInterview.length > 0) {
                Swal.fire({
                    icon: "warning",
                    title: "Some applicants have no interview set",
                    html: `Please schedule an interview first for: <br><b>${withoutInterview.map(a => a.name).join(', ')}</b>`,
                });
                return;
            }

            // ✅ Open correct modal (pass/fail manage)
            this.showBulkStatusModal = true;
        },

        async openBulk(type = 'bulk') {
                if (!this.selectedApplicants.length) {
                    Swal.fire("No applicants selected", "", "warning");
                    return;
                }

                // 🔸 Validation for bulk-reschedule
                if (type === 'bulk-reschedule') {
                    const unscheduled = this.selectedApplicants.filter(a => !a.has_schedule);
                    if (unscheduled.length) {
                        Swal.fire({
                            icon: 'warning',
                            html: `Please set an interview date first for Applicants: <br><b>${unscheduled.map(a => a.name).join(', ')}</b>`,
                        });
                        return;
                    }
                } 
                // 🔸 Validation for bulk-schedule
                else if (type === 'bulk') {
                    const alreadyScheduled = this.selectedApplicants.filter(a => a.has_schedule);
                    if (alreadyScheduled.length) {
                        Swal.fire({
                            icon: 'warning',
                            html: `These Applicants already have interview schedules: <br><b>${alreadyScheduled.map(a => a.name).join(', ')}</b>`,
                        });
                        return;
                    }
                } 

                // ✅ If passed all validations, open modal
                this.interviewMode = type;
                this.interviewApplicant = null;
                this.resetInterviewForm();
                this.showInterviewModal = true;
        },

        // 🔹 Reset interview form
        resetInterviewForm() {
            this.interviewDate = '';
            this.interviewTime = 8;
            this.interviewPeriod = 'AM';
            this.originalNormalizedDT = '';
        },

        // 🔹 Submit single interview
        async submitSingleInterview(datetime) {
            // Validate CSRF token exists
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page.');
            }

            const response = await fetch(`/hrAdmin/interviews`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    application_id: this.interviewApplicant.application_id,
                    user_id: this.interviewApplicant.user_id,
                    scheduled_at: datetime,
                }),
            });
            if (!response.ok) throw new Error("Failed single save");
            return response.json();
        },

        // 🔹 Submit bulk interviews
        async submitBulk(newNormalizedDT, mode) {
            // Filter only applicants whose scheduled_at is different
            const applicantsToUpdate = this.selectedApplicants.filter(a => a.scheduled_at !== newNormalizedDT);

            if (!applicantsToUpdate.length) {
                Swal.fire({
                    icon: 'info',
                    text: 'No changes detected for the selected applicants.'
                });
                this.showInterviewModal = false;
                return;
            }

            const url = mode === 'bulk-reschedule'
                ? '/hrAdmin/interviews/bulk-reschedule'
                : '/hrAdmin/interviews/bulk';

            const payload = {
                applicants: applicantsToUpdate.map(a => ({
                    application_id: a.application_id,
                    user_id: a.user_id
                })),
                scheduled_at: newNormalizedDT,
            };

            // Validate CSRF token exists
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page.');
            }

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) throw new Error("Bulk save failed");
            return response.json();
        },

        // 🔹 Submit interview date
        async submitInterviewDate() {
            if (!this.interviewDate || !this.interviewTime || !this.interviewPeriod) {
                Swal.fire({ icon: 'warning', text: 'Select both date and time.' });
                return;
            }

            // 🚫 Block same-day or past interviews
            const today = new Date().toISOString().split('T')[0];
            if (this.interviewDate <= today) {
                Swal.fire({ icon: 'warning', text: 'Interview must be scheduled for a future date.' });
                return;
            }

            const hour24 = Number(this.to24h(this.interviewTime, this.interviewPeriod));

            // ✅ Only allow 8 AM – 4 PM (08–16)
            if (hour24 < 8 || hour24 > 16) {
                alert('Interview must be between 8 AM and 4 PM.');
                return;
            }

            const newNormalizedDT = `${this.interviewDate} ${String(hour24).padStart(2,'0')}:00:00`;

            // 🔹 Check for changes before sending
            if (this.interviewMode === 'single' && this.originalNormalizedDT === newNormalizedDT) {
                Swal.fire({
                    icon: 'info',
                    text: 'No changes detected. Interview schedule remains the same.'
                });
                this.showInterviewModal = false;
                return;
            }

            // 🔹 For bulk, optionally skip if all selected applicants have same datetime
            if (this.interviewMode !== 'single') {
                const unchanged = this.selectedApplicants.every(a => a.scheduled_at === newNormalizedDT);
                if (unchanged) {
                    Swal.fire({
                        icon: 'info',
                        text: 'No changes detected for the selected applicants.'
                    });
                    this.showInterviewModal = false;
                    return;
                }
            }

            this.loading = true;

            try {
                if (this.interviewMode === 'single') {
                    await this.submitSingleInterview(newNormalizedDT);
                } else {
                    await this.submitBulk(newNormalizedDT, this.interviewMode);
                }

                this.feedbackMessage = "Interview schedule saved!";
                this.feedbackVisible = true;
                setTimeout(() => {
                    this.feedbackVisible = false;
                    location.reload();
                }, 2000);
                this.showInterviewModal = false;
            } catch (e) {
                alert("Error: " + e.message);
            } finally {
                this.loading = false;
            }
        },

    }));
});
