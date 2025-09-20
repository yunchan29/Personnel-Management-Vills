
document.addEventListener('alpine:init', () => {
    Alpine.data('applicantsHandler', () => ({
        loading: false,
        pageContext: '',
        showModal: false,
        resumeUrl: '',
        showProfile: false,
        activeProfileId: null,

        selectedApplicantId: null,
        selectedApplicantName: '',

        applicants: [],
        removedApplicants: [],
        selectedApplicants: [],
        feedbackMessage: '',
        feedbackVisible: false,
        showAll: false,

        showStatusModal: false,
        statusAction: '',
        selectedApplicant: null,

        showBulkStatusModal: false,
        bulkStatusAction: '',

        showInterviewModal: false,
        interviewMode: 'single',
        interviewApplicant: null,
        interviewDate: '',
        interviewTime: '',
        interviewPeriod: 'PM',

        originalNormalizedDT: '',

        // 🟢 Training schedule defaults
        trainingStartHour: '',
        trainingStartPeriod: '',
        trainingEndHour: '',
        trainingEndPeriod: '',
        trainingLocation: '',
        trainingMode: 'single',

        // originals to compare later
        originalTrainingDateRange: '',
        originalTrainingTimeStart: '',
        originalTrainingTimeEnd: '',
        originalTrainingLocation: '',

        showTrainingModal: false,
        trainingApplicant: null,
        trainingPicker: null,

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
            }, 50);
        },

        filteredApplicants() {
            return this.applicants.filter(applicant => this.showAll || !applicant.training);
        },

        // ✅ Helper: kunin unique ID (application_id or id)
        getApplicantId(applicant) {
            const id = applicant.application_id ?? applicant.id;
            return id;
        },

        // ✅ Kunin lahat ng local checkboxes (hindi kasama disabled)
        getLocalCheckboxes(checkboxClass = '.applicant-checkbox:not(:disabled)') {
            const cbs = Array.from(this.$root.querySelectorAll(checkboxClass));
            return cbs;
        },

        // ✅ Master toggle (select all / deselect all)
        toggleSelectAll(event) {
            const isChecked = event.target.checked;

            // ✅ Only get visible checkboxes
            const visibleCheckboxes = Array.from(document.querySelectorAll('.applicant-checkbox'))
                .filter(cb => cb.offsetParent !== null); // only visible rows

            visibleCheckboxes.forEach(cb => {
                cb.checked = isChecked;

                const data = JSON.parse(cb.value);
                if (isChecked) {
                    if (!this.selectedApplicants.some(a => a.application_id === data.application_id)) {
                        this.selectedApplicants.push(data);
                    }
                } else {
                    this.selectedApplicants = this.selectedApplicants.filter(a => a.application_id !== data.application_id);
                }
            });
        },

        // ✅ Update master checkbox state (per component)
        updateMasterCheckbox() {
            const master = this.$root.querySelector('[x-ref="masterCheckbox"]');
            if (!master) return;

            const visibleCheckboxes = this.getLocalCheckboxes(); // only visible rows
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


        // ✅ Toggle single item
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

        // ✅ Derived states
        isAllSelected() {
            const total = this.getLocalCheckboxes().length;
            const result = this.selectedApplicants.length > 0 &&
                        this.selectedApplicants.length === total;
            return result;
        },

        isIndeterminate() {
            const total = this.getLocalCheckboxes().length;
            const result = this.selectedApplicants.length > 0 &&
                        this.selectedApplicants.length < total;
            return result;
        },

        // For maramihag pass/fail sa interview modal
        openBulkStatusModal() {
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }
            this.showBulkStatusModal = true;
        },

        // Status change for mass pass/fail status change 
        async submitBulkStatusChange() {
            console.log("🚀 submitBulkStatusChange triggered");
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }

            this.loading = true;

            // map to application IDs, fallback to id if application_id is missing
            const ids = this.selectedApplicants.map(app => app.application_id);

            // debug payload
            console.log("🚀 Sending payload:", { ids, status: this.bulkStatusAction});

            // check if ids array is empty after mapping
            if (!ids.length) {
                Swal.fire("Invalid applicants selected", "No valid IDs found", "error");
                this.loading = false;
                return;
            }

            // validate status locally before sending
            const allowedStatuses = ['approved', 'declined', 'interviewed', 'fail_interview', 'for_interview', 'trained'];
            if (!allowedStatuses.includes(this.bulkStatusAction)) {
                Swal.fire("Invalid status", `Status must be one of: ${allowedStatuses.join(", ")}`, "error");
                this.loading = false;
                return;
            }

            try {
                const response = await fetch('/hrAdmin/applications/bulk-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        ids: ids,
                        status: this.bulkStatusAction
                    })
                });

                const res = await response.json();

                if (res.success) {
                    this.feedbackMessage = res.message || `Applicants ${this.bulkStatusAction} successfully.`;
                    this.feedbackVisible = true;

                    // auto-hide toast after 3s
                    setTimeout(() => {
                        this.feedbackVisible = false;
                        location.reload();
                    }, 3000);

                    this.fetchApplicants?.();
                    this.showBulkStatusModal = false;
                } else {
                    console.error("Bulk update errors:", res.errors);
                    Swal.fire("Error", res.errors ? JSON.stringify(res.errors) : "Something went wrong", "error");
                }
            } catch (err) {
                console.error(err);
                Swal.fire("Error", "Request failed", "error");
            } finally {
                this.loading = false;
            }
        },
        
        async bulkAction(status) {
            if (this.selectedApplicants.length === 0) return;

            const actionText = status === "approved" ? "Approve" : "Decline";
            const swalIcon   = status === "approved" ? "question" : "warning";

            Swal.fire({
                title: `${actionText} ${this.selectedApplicants.length} applicants?`,
                text: status === "approved"
                    ? "They will receive an approval email."
                    : "They will receive a decline email.",
                icon: swalIcon,
                showCancelButton: true,
                confirmButtonText: `
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 hidden" id="bulk-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Confirm
                    </span>
                `,
                cancelButtonText: "Cancel",
                preConfirm: async () => {
                    document.getElementById("bulk-spinner").classList.remove("hidden");

                    try {
                        const response = await fetch(window.bulkApproveUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                ids: this.selectedApplicants.map(app => app.id ?? app.application_id), // 🔥 FIXED
                                status: status
                            })
                        });

                        const data = await response.json();
                        if (!data.success) throw new Error(data.message || "Failed");

                        // ✅ remove rows
                        this.selectedApplicants.forEach(app => {
                            const id = app.id ?? app.application_id;
                            document.querySelector(`[data-applicant-id="${id}"]`)?.remove();
                        });
                        this.selectedApplicants = [];

                        this.feedbackMessage = data.message || `Applicants ${status} successfully.`;
                        this.feedbackVisible = true;
                        setTimeout(() => { 
                            this.feedbackVisible = false;
                            window.location.reload(); 
                        }, 3000);

                        return data;
                    } catch (err) {
                        Swal.showValidationMessage(err.message);
                    }
                }

            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        actionText + "d!",
                        `${result.value.message}`,
                        "success"
                    );
                }
            });
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

        // Single applicant status change for both approval and interview status updates
        async submitStatusChange() {
            this.loading = true;
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

                // Update DOM row attribute
                const row = document.querySelector(`tr[data-applicant-id='${result.application_id}']`);
                if (row) row.setAttribute('data-status', this.statusAction);

                // Update Alpine/JS array
                const index = this.applicants.findIndex(a => a.id === result.application_id);
                if (index !== -1) {
                    this.applicants[index].status = this.statusAction;
                    this.applicants = [...this.applicants];
                }

                // Human-friendly labels
                const statusLabels = {
                    interviewed: "marked as interviewed",
                    declined: "declined",
                    trained: "marked as trained",
                    fail_interview: "marked as failed in interview",
                    hired: "hired"
                };
                const label = statusLabels[this.statusAction] || this.statusAction;

                this.feedbackMessage = `Applicant ${label} successfully.`;
                this.feedbackVisible = true;

                // Auto-hide after 2.5s
                setTimeout(() => {
                    this.feedbackVisible = false;
                    location.reload();
                }, 2500);

                if (['interviewed', 'declined', 'trained', 'fail_interview'].includes(this.statusAction)) {
                    setTimeout(() => this.removedApplicants.push(result.application_id), 300);
                }

                this.selectedApplicant = null;
                this.statusAction = '';
                this.showStatusModal = false;

            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

    }));
});
