document.addEventListener('alpine:init', () => {
    Alpine.data('interviewHandler', (parent) => ({
            get selectedApplicants() {
            return parent.selectedApplicants;
            },
            set selectedApplicants(val) {
            parent.selectedApplicants = val;
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

        // 🔹 Helpers
        to24h(hour, period) {
            hour = Number(hour);

            if (period === "AM") {
                if (hour === 12) {
                    return 0; // 12 AM → 00
                }
                return hour; // 1–11 AM → 1–11
            } else {
                if (hour === 12) {
                    return 12; // 12 PM → 12 (noon)
                }
                return hour + 12; // 1–11 PM → 13–23
            }
        },


        to12h(hour24) {
            let period = hour24 >= 12 ? 'PM' : 'AM';
            let hour12 = hour24 % 12;
            if (hour12 === 0) hour12 = 12;
            return { hour12, period };
        },

        formatDisplay(hour, period) {
            return `${hour}:00 ${period}`;
        },

        // Status for interview component
        statusMap: {
        interviewed: { label: 'Passed', class: 'bg-green-200 text-green-800' },
        declined: { label: 'Failed', class: 'bg-red-200 text-red-800' },
        for_interview: { label: 'For Interview', class: 'bg-yellow-200 text-yellow-800' },
        default: { label: 'Pending', class: 'bg-gray-200 text-gray-800' },
        },


        // 🔹 Toggle all visible checkboxes
        toggleSelectAll(event) {
            const isChecked = event.target.checked;

            // ✅ Only get visible checkboxes
            const visibleCheckboxes = Array.from(document.querySelectorAll('.applicant-checkbox'))
                .filter(cb => cb.offsetParent !== null); // only visible rows

            visibleCheckboxes.forEach(cb => {
                cb.checked = isChecked;
                const data = JSON.parse(cb.value);

                if (isChecked) {
                    // add if not already selected
                    if (!this.selectedApplicants.some(a => a.application_id === data.application_id)) {
                        this.selectedApplicants.push(data);
                    }
                } else {
                    // remove if deselected
                    this.selectedApplicants = this.selectedApplicants.filter(
                        a => a.application_id !== data.application_id
                    );
                }
            });

            this.updateMasterCheckbox();
        },

        // 🔹 Update master checkbox visual state (checked / indeterminate)
        updateMasterCheckbox() {
            const master = this.$root.querySelector('[x-ref="masterCheckbox"]');
            if (!master) return;

            const visibleCheckboxes = this.getLocalCheckboxes();
            const total = visibleCheckboxes.length;

            const selected = visibleCheckboxes.filter(cb => {
                const value = JSON.parse(cb.value);
                return this.selectedApplicants.some(a => a.application_id === value.application_id);
            }).length;

            if (selected === 0) {
                master.checked = false;
                master.indeterminate = false;
            } else if (selected === total) {
                master.checked = true;
                master.indeterminate = false;
            } else {
                master.checked = false;
                master.indeterminate = true;
            }
        },

        // 🔹 Toggle a single checkbox
        toggleItem(event, id) {
            const checked = event.target.checked;
            const value = JSON.parse(event.target.value);
            const applicantId = this.getApplicantId(value);

            if (checked) {
                if (!this.selectedApplicants.some(a => this.getApplicantId(a) === applicantId)) {
                    this.selectedApplicants.push(value);
                }
            } else {
                this.selectedApplicants = this.selectedApplicants.filter(
                    a => this.getApplicantId(a) !== applicantId
                );
            }

            this.updateMasterCheckbox();
        },

        // 🔹 Helpers
        getLocalCheckboxes() {
            return Array.from(document.querySelectorAll('.applicant-checkbox'))
                .filter(cb => cb.offsetParent !== null);
        },

        getApplicantId(applicant) {
            return applicant.application_id;
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
                const res = await fetch('/hrAdmin/applications/bulk-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
            const response = await fetch(`/hrAdmin/interviews`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
