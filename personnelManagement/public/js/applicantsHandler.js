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

        // ✅ Helper: get unique ID (application_id or id) - uses CheckboxUtils
        getApplicantId(applicant) {
            return CheckboxUtils.getApplicantId(applicant);
        },

        // ✅ Get all local checkboxes (not disabled)
        getLocalCheckboxes(checkboxClass = '.applicant-checkbox:not(:disabled)') {
            return Array.from(this.$root.querySelectorAll(checkboxClass));
        },

        // ✅ Master toggle (select all / deselect all) - uses CheckboxUtils
        toggleSelectAll(event) {
            this.selectedApplicants = CheckboxUtils.toggleSelectAll(
                event,
                this.selectedApplicants,
                '.applicant-checkbox',
                'application_id'
            );
            this.updateMasterCheckbox();
        },

        // ✅ Update master checkbox state - uses CheckboxUtils
        updateMasterCheckbox() {
            CheckboxUtils.updateMasterCheckbox(
                this.$root,
                this.selectedApplicants,
                '.applicant-checkbox:not(:disabled)',
                'masterCheckbox',
                'application_id'
            );
        },

        // ✅ Toggle single item - uses CheckboxUtils
        toggleItem(event, id) {
            this.selectedApplicants = CheckboxUtils.toggleItem(
                event,
                this.selectedApplicants,
                'application_id'
            );
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
                // Validate CSRF token exists
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    throw new Error('CSRF token not found. Please refresh the page.');
                }

                const response = await fetch('/hrAdmin/applications/bulk-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
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
                        // Validate CSRF token exists
                        const csrfToken = document.querySelector('meta[name=csrf-token]')?.content;
                        if (!csrfToken) {
                            throw new Error('CSRF token not found. Please refresh the page.');
                        }

                        const response = await fetch(window.bulkApproveUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken
                            },
                            body: JSON.stringify({
                                ids: this.selectedApplicants.map(app => app.application_id ?? app.id),
                                status: status
                            })
                        });

                        const data = await response.json();
                        if (!data.success) throw new Error(data.message || "Failed");

                        // ✅ remove rows
                        this.selectedApplicants.forEach(app => {
                            const id = app.application_id ?? app.id;
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
                // Validate CSRF token exists
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    throw new Error('CSRF token not found. Please refresh the page.');
                }

                const response = await fetch(`/hrAdmin/applications/${this.selectedApplicant.id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
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
