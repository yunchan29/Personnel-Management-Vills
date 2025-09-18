
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

        showBulkStatusModal: false,
        bulkStatusAction: '',

        showInterviewModal: false,
        interviewMode: 'single',
        interviewApplicant: null,
        interviewDate: '',
        interviewTime: '',
        interviewPeriod: 'PM',

        originalNormalizedDT: '',

        showTrainingModal: false,
        trainingApplicant: null,
        trainingPicker: null,

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


        // 🟢 Applicant name/id defaults so bindings won’t error
        selectedApplicantId: null,
        selectedApplicantName: '',

        applicants: [],
        removedApplicants: [],
        selectedApplicants: [],
        feedbackMessage: '',
        feedbackVisible: false,
        showAll: false,


        // Convert interview set time to 12-hour format
        to24h(hour12, period) {
            let h = parseInt(hour12, 10);
            if (period === 'AM') {
                if (h === 12) h = 0;      // 12 AM -> 00
            } else { // PM
                if (h !== 12) h += 12;    // 1 PM -> 13 ... 11 PM -> 23
            }
            return h;
        },

        // Function converter para sa time ng training schedule
        to12h(hour24) {
            let period = hour24 >= 12 ? 'PM' : 'AM';
            let hour12 = hour24 % 12;
            if (hour12 === 0) hour12 = 12;
            return { hour12, period };
        },

        formatDisplay(hour, period) {
            return `${hour}:00 ${period}`;
        },

        //Converts Training Start/End time to 24-hour format for backend
        formatTime(timeStr) {
            // expects something like "7 AM" or "12 PM"
            if (!timeStr) return null;

            const [hourStr, period] = timeStr.split(" ");
            let hour = parseInt(hourStr, 10);

            if (period.toUpperCase() === "PM" && hour !== 12) {
                hour += 12;
            }
            if (period.toUpperCase() === "AM" && hour === 12) {
                hour = 0;
            }

            return `${hour.toString().padStart(2, "0")}:00:00`; 
        },


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

        // Status for interview component
        statusMap: {
        interviewed: { label: 'Passed', class: 'bg-green-200 text-green-800' },
        declined: { label: 'Failed', class: 'bg-red-200 text-red-800' },
        for_interview: { label: 'For Interview', class: 'bg-yellow-200 text-yellow-800' },
        default: { label: 'Pending', class: 'bg-gray-200 text-gray-800' },
        },

        filteredApplicants() {
            return this.applicants.filter(applicant => this.showAll || !applicant.training);
        },

        // Master checkbox visual states (scoped)
        getLocalCheckboxes(checkboxClass = '.applicant-checkbox') {
            return Array.from(this.$root.querySelectorAll(checkboxClass));
        },

toggleSelectAll(event) {
    const masterChecked = event.target.checked;

    // Get all non-disabled checkboxes in table
    const checkboxes = document.querySelectorAll('.applicant-checkbox:not(:disabled)');

    if (masterChecked) {
        // Add all to selectedApplicants
        this.selectedApplicants = [...checkboxes].map(cb => JSON.parse(cb.value));
    } else {
        // Clear all
        this.selectedApplicants = [];
    }

    this.$nextTick(() => this.syncMasterCheckbox());
},

updateMasterCheckbox() {
    const master = this.$refs.masterCheckbox;
    if (!master) return;

    if (this.selectedApplicants.length === 0) {
        master.indeterminate = false;
        master.checked = false;
    } else if (this.selectedApplicants.length === this.applicants.length) {
        master.indeterminate = false;
        master.checked = true;
    } else {
        master.indeterminate = true;
        master.checked = false;
    }
},

        // Individual checkbox click
toggleItem(event, id) {
    const checked = event.target.checked;
    const value = JSON.parse(event.target.value);

    if (checked) {
        this.selectedApplicants.push(value);
    } else {
        this.selectedApplicants = this.selectedApplicants.filter(a => a.application_id !== id && a.id !== id);
    }

    this.updateMasterCheckbox(); // 👈 dito na siya
},


        // 🔹 Helper for master checkbox visual state
        isAllSelected() {
            return this.selectedApplicants.length > 0 &&
                   this.selectedApplicants.length === this.$root.querySelectorAll('.applicant-checkbox').length;
        },

        isIndeterminate() {
            const total = this.$root.querySelectorAll('.applicant-checkbox').length;
            return this.selectedApplicants.length > 0 &&
                   this.selectedApplicants.length < total;
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
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }
            
            this.loading = true;

            // extract application_id lang
            const ids = this.selectedApplicants.map(app => app.application_id);

            fetch('/hrAdmin/applications/bulk-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    ids: ids,
                    status: this.bulkStatusAction
                })
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    // ✅ Greenlist feedback
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
                    Swal.fire("Error", res.errors ? JSON.stringify(res.errors) : "Something went wrong", "error");
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire("Error", "Request failed", "error");
            })
            .finally(() => {
                this.loading = false;
            });
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
                                ids: this.selectedApplicants,
                                status: status
                            })
                        });

                        const data = await response.json();
                        if (!data.success) throw new Error(data.message || "Failed");

                        // ✅ remove rows
                        this.selectedApplicants.forEach(id => {
                            document.querySelector(`[data-applicant-id="${id}"]`).remove();
                        });
                        this.selectedApplicants = [];

                        // ✅ show your toast feedback
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
                    // optional: location.reload();
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

        async submitBulkStatusChange() {
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }

            // ✅ Check kung lahat may interview_date (o interview set field)
            const withoutInterview = this.selectedApplicants.filter(app => !app.interview_date);

            if (withoutInterview.length > 0) {
                Swal.fire({
                    icon: "warning",
                    title: "Some applicants have no interview set",
                    text: "Please schedule an interview before managing their status.",
                });
                return; // ⛔ stop execution
            }

            this.loading = true;

            // extract application_id lang
            const ids = this.selectedApplicants.map(app => app.application_id);

            fetch('/hrAdmin/applications/bulk-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    ids: ids,
                    status: this.bulkStatusAction
                })
            })
            .then(response => response.json())
            .then(res => {
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
            })
            .catch(err => {
                console.error(err);
                Swal.fire("Error", "Request failed", "error");
            })
            .finally(() => {
                this.loading = false;
            });
        },

        // ---- open for single applicant
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
                this.interviewDate = '';
                this.interviewTime = 8;
                this.interviewPeriod = 'AM';
                this.originalNormalizedDT = '';
            }
            this.showInterviewModal = true;
        },

        // ---- open for bulk (just passes selectedApplicants)
        async openBulk(type = 'bulk') {
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }

            if (type === 'bulk-reschedule') {
                // 🔹 Reschedule mode → block those without schedule
                const unscheduled = this.selectedApplicants.filter(a => !a.has_schedule);
                if (unscheduled.length) {
                    Swal.fire({
                        icon: 'warning',
                        html: `Please set an interview date first for Applicants: <br><b>${unscheduled.map(a => a.name).join(', ')}</b>`,
                    });
                    return;
                }
            } else if (type === 'bulk') {
                // 🔹 New schedule mode → block those who already have a schedule
                const alreadyScheduled = this.selectedApplicants.filter(a => a.has_schedule);
                if (alreadyScheduled.length) {
                    Swal.fire({
                        icon: 'warning',
                        html: `These Applicants already have interview schedules: <br><b>${alreadyScheduled.map(a => a.name).join(', ')}</b>`,
                    });
                    return;
                }
            }

            this.interviewMode = type; // 'bulk' or 'bulk-reschedule'
            this.interviewApplicant = null;
            this.resetInterviewForm();
            this.showInterviewModal = true;
        },

        resetInterviewForm() {
            this.interviewDate = '';
            this.interviewTime = 8;
            this.interviewPeriod = 'AM';
            this.originalNormalizedDT = '';
        },

        // ---- submit handles both
        async submitInterviewDate() {
        if (!this.interviewDate || !this.interviewTime || !this.interviewPeriod) {
            Swal.fire({ icon: 'warning', text: 'Select both date and time.' });
            return;
        }

        const hour24 = this.to24h(this.interviewTime, this.interviewPeriod);
        if (hour24 < 8 || hour24 > 17) {
            alert('Interview must be between 8 AM and 5 PM.');
            return;
        }

        const newNormalizedDT = `${this.interviewDate} ${String(hour24).padStart(2,'0')}:00:00`;
        this.loading = true;

        try {
            if (this.interviewMode === 'single') {
            await this.submitSingleInterview(newNormalizedDT);
            } else {
            await this.submitBulk(newNormalizedDT, this.interviewMode);
            }

            this.feedbackMessage = "Interview schedule saved!";
            this.feedbackVisible = true;
            setTimeout(() => { this.feedbackVisible = false; location.reload(); }, 2000);
            this.showInterviewModal = false;
        } catch (e) {
            alert("Error: " + e.message);
        } finally {
            this.loading = false;
        }
        },

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

        async submitBulk(datetime, mode) {
            const url = mode === 'bulk-reschedule'
                ? '/hrAdmin/interviews/bulk-reschedule'
                : '/hrAdmin/interviews/bulk';

            const payload = {
                applicants: this.selectedApplicants.map(a => ({
                    application_id: a.application_id,
                    user_id: a.user_id
                })),
                scheduled_at: datetime, // 🔥 at root level
            };

            console.log("Payload bulk:", JSON.stringify(payload, null, 2));

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

        bulkSetTraining() {
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }

            const unscheduled = this.selectedApplicants.filter(id => {
                const app = this.applicants.find(a => a.id === id);
                return !app?.training; // has no training
            });

            const alreadyScheduled = this.selectedApplicants.filter(id => {
                const app = this.applicants.find(a => a.id === id);
                return !!app?.training; // has training
            });

            if (alreadyScheduled.length > 0) {
                return Swal.fire({
                    icon: "error",
                    title: "Invalid Selection",
                    text: "All selected applicants must have no training yet to proceed with Set Training."
                });
            }

            // ✅ proceed to bulk set training
            this.openSetTraining(null, null, '', null, 'bulk');
        },

        bulkReschedTraining() {
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }

            const unscheduled = this.selectedApplicants.filter(id => {
                const app = this.applicants.find(a => a.id === id);
                return !app?.training;
            });

            if (unscheduled.length > 0) {
                return Swal.fire({
                    icon: "error",
                    title: "Invalid Selection",
                    text: "All selected applicants must already have training scheduled to proceed with Reschedule."
                });
            }

            // ✅ proceed to bulk resched training
            this.openSetTraining(null, null, '', null, 'bulk');
        },

        openSetTraining(applicantId = null, fullName = '', range = '', schedule = null, mode = 'single') {
            this.trainingMode = mode;

            if(mode === 'single'){
                this.selectedApplicantId = applicantId;
                this.selectedApplicantName = fullName;
            } else{
                this.selectedApplicantId = null;
                this.selectedApplicantName = `${this.selectedApplicants.length} applicants`;
            }


            // --- normalize date range
            this.selectedTrainingDateRange = '';
            this.originalTrainingDateRange = '';
            if (range && range.includes(' - ')) {
                this.selectedTrainingDateRange = range.trim();

                const [startRaw, endRaw] = range.split(' - ').map(d => new Date(d.trim()));
                const fmt = (d) => {
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    const yyyy = d.getFullYear();
                    return `${mm}/${dd}/${yyyy}`;
                };
                this.originalTrainingDateRange = `${fmt(startRaw)} - ${fmt(endRaw)}`;
            }

            // --- prefill time
            if (schedule?.start_time && schedule?.end_time) {
                const [startHour24] = schedule.start_time.split(':');
                const [endHour24]   = schedule.end_time.split(':');

                const start = this.to12h(parseInt(startHour24, 10));
                const end   = this.to12h(parseInt(endHour24, 10));

                this.trainingStartHour   = start.hour12;
                this.trainingStartPeriod = start.period;

                this.trainingEndHour     = end.hour12;
                this.trainingEndPeriod   = end.period;
            } else {
                this.trainingStartHour = '8';
                this.trainingStartPeriod = 'AM';
                this.trainingEndHour = '5';
                this.trainingEndPeriod = 'PM';
            }

            // --- prefill location ✅
            this.trainingLocation = schedule?.location ?? '';

            // --- show modal AFTER state is set
            this.showTrainingModal = true;

            // --- init/update Litepicker
            this.$nextTick(() => {
                const ref = this.$refs.trainingDateRange;
                if (!ref) return console.warn("trainingDateRange input not found");

                const [start, end] = (this.selectedTrainingDateRange && this.selectedTrainingDateRange.includes(' - '))
                    ? this.selectedTrainingDateRange.split(' - ')
                    : [null, null];

                if (!this.trainingPicker) {
                    this.trainingPicker = new Litepicker({
                        element: ref,
                        singleMode: false,
                        format: 'MM/DD/YYYY',
                        numberOfMonths: 2,
                        numberOfColumns: 2,
                        autoApply: true,
                        startDate: start,
                        endDate: end,
                        minDate: new Date(),
                    });
                } else {
                    if (start && end) {
                        this.trainingPicker.setDateRange(start, end, true);
                    } else {
                        this.trainingPicker.clearSelection();
                    }

                    this.trainingPicker.setOptions({
                        minDate: new Date(),
                    });
                }
            });
        },

        openBulkSetTraining() {
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }
            this.openSetTraining(null, null, '', null, 'bulk');
        },

        async submitTrainingSchedule() {
            const selectedRange = this.$refs.trainingDateRange.value?.trim();
            const trainingTimeStart = `${this.trainingStartHour} ${this.trainingStartPeriod}`;
            const trainingTimeEnd   = `${this.trainingEndHour} ${this.trainingEndPeriod}`;
            const trainingLocation  = (this.trainingLocation || '').trim();

            // -------------------------------
            // Basic validations
            // -------------------------------
            if (!selectedRange || !selectedRange.includes(' - ')) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Invalid Input',
                    text: 'Please select a valid training date range.'
                });
            }

            if (!trainingLocation) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Missing Location',
                    text: 'Please enter the training location.'
                });
            }

            // -------------------------------
            // Date parsing
            // -------------------------------
            const [newStartStr, newEndStr] = selectedRange.split(' - ').map(s => s.trim());
            const newStart = new Date(newStartStr);
            const newEnd   = new Date(newEndStr);
            const today    = new Date(); today.setHours(0, 0, 0, 0);

            // -------------------------------
            // Determine if date range actually changed
            // -------------------------------
            let datesChanged = true;
            if (this.originalTrainingDateRange && this.originalTrainingDateRange.includes(' - ')) {
                const [oStartStr, oEndStr] = this.originalTrainingDateRange.split(' - ').map(d => d.trim());
                const oStart = new Date(oStartStr);
                const oEnd   = new Date(oEndStr);

                datesChanged = (newStart.getTime() !== oStart.getTime()) || 
                            (newEnd.getTime()   !== oEnd.getTime());
            }

            // -------------------------------
            // Validate ONLY if dates changed
            // -------------------------------
            if (datesChanged) {
                if (isNaN(newStart) || isNaN(newEnd) || newStart < today || newEnd < today) {
                    return Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date Range',
                        text: 'The training schedule must be set to future dates.'
                    });
                }
            }

            // -------------------------------
            // Check if same as original
            // -------------------------------
            let isSameAsOriginal = false;
            if (this.originalTrainingDateRange && this.originalTrainingDateRange.includes(' - ')) {
                const [oStart, oEnd] = this.originalTrainingDateRange.split(' - ').map(d => d.trim());
                isSameAsOriginal = (
                    newStartStr       === oStart &&
                    newEndStr         === oEnd &&
                    trainingTimeStart === (this.originalTrainingTimeStart   || '') &&
                    trainingTimeEnd   === (this.originalTrainingTimeEnd     || '') &&
                    trainingLocation  === (this.originalTrainingLocation    || '')
                );
            }

            if (isSameAsOriginal) {
                this.feedbackMessage = 'No changes were made to the training schedule.';
                this.feedbackVisible = true;
                this.showTrainingModal = false;

                setTimeout(() => this.feedbackVisible = false, 3000);
                return;
            }

            // -------------------------------
            // Proceed function (save changes)
            // -------------------------------
            const proceed = async () => {
                try {
                    if (this.trainingMode === 'single') {
                        await this.saveTrainingForApplicant(this.selectedApplicantId, {
                            start_date: newStartStr,
                            end_date: newEndStr,
                            start_time: this.formatTime(trainingTimeStart),
                            end_time: this.formatTime(trainingTimeEnd),
                            location: trainingLocation
                        });
                    } else if (this.trainingMode === 'bulk') {
                        for (let applicantId of this.selectedApplicants) {
                            await this.saveTrainingForApplicant(applicantId, {
                                start_date: newStartStr,
                                end_date: newEndStr,
                                start_time: this.formatTime(trainingTimeStart),
                                end_time: this.formatTime(trainingTimeEnd),
                                location: trainingLocation
                            });
                        }
                    }

                    this.feedbackMessage = 'Training schedule saved successfully!';
                    this.feedbackVisible = true;
                    this.showTrainingModal = false;

                    setTimeout(() => this.feedbackVisible = false, 3000);
                    setTimeout(() => location.reload(), 3500);

                } catch (error) {
                    alert('Error: ' + error.message);
                } finally {
                    this.loading = false;
                }
            };

            // -------------------------------
            // Reschedule confirmation
            // -------------------------------
            this.loading = true;
            const isReschedule = !!this.originalTrainingDateRange;

            if (isReschedule) {
                const result = await Swal.fire({
                    title: "You're about to reschedule",
                    html: `
                        New training date range: <br><strong>${selectedRange}</strong><br>
                        Time: <strong>${trainingTimeStart} - ${trainingTimeEnd}</strong><br>
                        Location: <strong>${trainingLocation}</strong><br><br>
                        Do you want to continue?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#BD6F22',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, reschedule'
                });

                if (result.isConfirmed) proceed();
                else this.loading = false;
            } else {
                proceed();
            }
        },

        async saveTrainingForApplicant(applicantId, payload) {
            const response = await fetch(`/hrAdmin/training-schedule/${applicantId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });
            if (!response.ok) throw new Error('Failed to set training schedule');
            return await response.json();
        },

    }));
});
