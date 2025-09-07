
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


        filteredApplicants() {
            return this.applicants.filter(applicant => this.showAll || !applicant.training);
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedApplicants = Array.from(
                    document.querySelectorAll('tbody input[type=checkbox]')
                ).map(cb => cb.value);
            } else {
                this.selectedApplicants = [];
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

                const row = document.querySelector(`tr[data-applicant-id='${result.application_id}']`);
                if (row) row.setAttribute('data-status', this.statusAction);

                const index = this.applicants.findIndex(a => a.id === result.application_id);
                if (index !== -1) {
                    this.applicants[index].status = this.statusAction;
                    this.applicants = [...this.applicants];
                }

                const label = {
                    approved: 'Approved',
                    declined: 'Failed',
                    for_interview: 'Scheduled for Interview',
                    interviewed: 'Interview Passed',
                    fail_interview: 'Interview Failed',
                    trained: 'Trained',
                }[this.statusAction] || 'Updated';

               this.feedbackMessage = `Applicant ${label} successfully.`;
               this.feedbackVisible = true;

                // Wait 2.5 seconds, then reload the page
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

        openSetInterview(applicationId, name, userId, scheduledAt = '') {
              console.log("Scheduled At received:", scheduledAt); // 👈 check this
            this.interviewApplicant = { application_id: applicationId, user_id: userId, name: name };

            if (scheduledAt) {
                const [datePart, timePartRaw = '00:00:00'] = scheduledAt.split(' ');
                const timePart = timePartRaw.split(/[.\+Z]/)[0]; // strip fractions/timezone
                const hour24Str = timePart.split(':')[0];
                const hour24 = parseInt(hour24Str, 10);

                let hour12 = hour24 % 12;
                hour12 = hour12 === 0 ? 12 : hour12;
                const period = hour24 < 12 ? 'AM' : 'PM';

                this.interviewDate = datePart;
                this.interviewTime = hour12;     // number
                this.interviewPeriod = period;

                this.originalNormalizedDT = `${this.interviewDate} ${String(hour24).padStart(2,'0')}:00:00`;
         } else {
                this.interviewDate = '';       // still no date pre-selected
                this.interviewTime = 8;        // default to 8
                this.interviewPeriod = 'AM';   // default to AM
                this.originalNormalizedDT = '';
        }
            this.showInterviewModal = true;
        },

       async submitInterviewDate() {
        if (!this.interviewDate || !this.interviewTime || !this.interviewPeriod) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please select both date and time before continuing.',
                confirmButtonColor: '#BD6F22',
            });
            return;
        }
    
       const hour24 = this.to24h(this.interviewTime, this.interviewPeriod);

        // ✅ Restrict strictly to 8 AM – 5 PM
        if (hour24 < 8 || hour24 > 17) {
         alert('Interview must be scheduled between 8:00 AM and 5:00 PM.');
        return;
        }

        const newNormalizedDT = `${this.interviewDate} ${hour24}:00:00`;
        const originalNormalizedDT = this.originalNormalizedDT || '';

        console.log('Original:', originalNormalizedDT);
        console.log('New:', newNormalizedDT);

        // ✅ No-change guard (compare normalized strings)
        if (originalNormalizedDT && newNormalizedDT === originalNormalizedDT) {
            this.feedbackMessage = 'No changes were made to the interview schedule.';
            this.feedbackVisible = true;
            setTimeout(() => (this.feedbackVisible = false), 3000);
            this.showInterviewModal = false;
            return; // 🚫 Skip API call
        }

        const isReschedule = !!originalNormalizedDT;
        this.loading = true;

        const proceed = async () => {
            try {
            const response = await fetch(`/hrAdmin/interviews`, {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
                },
                body: JSON.stringify({
                application_id: this.interviewApplicant.application_id,
                user_id: this.interviewApplicant.user_id,
                scheduled_at: newNormalizedDT,   // send full normalized datetime
                is_reschedule: isReschedule,     // optional; backend derives anyway
                }),
            });

            if (!response.ok) throw new Error('Failed to set interview date');
            await response.json();

            // update DOM row
            const row = document.querySelector(
                `tr[data-applicant-id='${this.interviewApplicant.application_id}']`
            );
            if (row) {
                row.setAttribute('data-interview-date', newNormalizedDT);
                row.setAttribute('data-status', 'for_interview');
            }

            // update local list
            const index = this.applicants.findIndex(
                (a) => a.id === this.interviewApplicant.application_id
            );
            if (index !== -1) {
                this.applicants[index].status = 'for_interview';
                this.applicants = [...this.applicants];
            }

            // feedback
            this.feedbackMessage = 'Interview date set successfully!';
            this.feedbackVisible = true;
            setTimeout(() => {
                this.feedbackVisible = false;
                location.reload();
            }, 3000);

            this.showInterviewModal = false;
            } catch (error) {
            alert('Error: ' + error.message);
            } finally {
            this.loading = false;
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
            confirmButtonText: "Yes, reschedule",
            }).then((result) => {
            if (result.isConfirmed) {
                proceed();
            } else {
                this.loading = false;
            }
            });
        } else {
            proceed();
        }
        },

        openSetTraining(applicantId, fullName, range = '', schedule = null) {
            this.selectedApplicantId = applicantId;
            this.selectedApplicantName = fullName;

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
            const response = await fetch(`/hrAdmin/training-schedule/${this.selectedApplicantId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    start_date : newStartStr,
                    end_date   : newEndStr,
                    start_time : this.formatTime(trainingTimeStart),
                    end_time   : this.formatTime(trainingTimeEnd),
                    location   : trainingLocation
                })
            });

            if (!response.ok) throw new Error('Failed to set training schedule');
            const result = await response.json();

            // update DOM row
            const row = document.querySelector(`tr[data-applicant-id='${this.selectedApplicantId}']`);
            if (row) {
                row.setAttribute('data-training-range', selectedRange);
                row.setAttribute('data-training-time', `${trainingTimeStart} - ${trainingTimeEnd}`);
                row.setAttribute('data-training-location', trainingLocation);
            }

            // update reactive applicants list
            const index = this.applicants.findIndex(a => a.id === this.selectedApplicantId);
            if (index !== -1) {
                this.applicants[index] = {
                    ...this.applicants[index],
                    training: selectedRange,
                    training_time: `${trainingTimeStart} - ${trainingTimeEnd}`,
                    training_location: trainingLocation,
                    status: 'scheduled_for_training'
                };
                this.applicants = [...this.applicants];
            }

            // ✅ Show toast for both first-time and reschedule
            this.feedbackMessage = result.message || 'Training schedule saved successfully!';
            this.feedbackVisible = true;
            this.showTrainingModal = false;

            // let toast display first, then reload
            setTimeout(() => this.feedbackVisible = false, 3000); // hide after 3s
            setTimeout(() => location.reload(), 3500); // reload after toast fades out

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



    }));
});
