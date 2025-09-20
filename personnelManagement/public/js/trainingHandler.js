document.addEventListener('alpine:init', () => {
    Alpine.data('trainingHandler', () => ({
        trainingStartHour: '',
        trainingStartPeriod: '',
        trainingEndHour: '',
        trainingEndPeriod: '',
        trainingLocation: '',
        trainingMode: 'single',

        originalTrainingDateRange: '',
        originalTrainingTimeStart: '',
        originalTrainingTimeEnd: '',
        originalTrainingLocation: '',

        showTrainingModal: false,
        trainingApplicant: null,
        trainingPicker: null,

        // Function converter para sa time ng training schedule
        to12h(hour24) {
            let period = hour24 >= 12 ? 'PM' : 'AM';
            let hour12 = hour24 % 12;
            if (hour12 === 0) hour12 = 12;
            return { hour12, period };
        },

        formatTime(timeStr) {
            if (!timeStr) return null;
            const [hourStr, period] = timeStr.split(" ");
            let hour = parseInt(hourStr, 10);
            if (period.toUpperCase() === "PM" && hour !== 12) hour += 12;
            if (period.toUpperCase() === "AM" && hour === 12) hour = 0;
            return `${hour.toString().padStart(2, "0")}:00:00`; 
        },

        bulkSetTraining() {
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }

            const alreadyScheduled = this.selectedApplicants.filter(a => a.has_training);
            if (alreadyScheduled.length > 0) {
                const names = alreadyScheduled.map(a => a.name).join('<br>');
                return Swal.fire({
                    icon: "error",
                    title: "Invalid Selection",
                    html: `You cannot set training for applicants who already have a schedule:<br><br><strong>${names}</strong>`
                });
            }

            this.openSetTraining(null, null, '', null, 'bulk');
        },

        bulkReschedTraining() {
            if (!this.selectedApplicants.length) {
                Swal.fire("No applicants selected", "", "warning");
                return;
            }

            const selectedBoxes = Array.from(document.querySelectorAll('.applicant-checkbox:checked'));

            // Applicants na wala pang training
            const unscheduled = selectedBoxes.filter(cb => cb.dataset.hasTraining === "0");

            if (unscheduled.length > 0) {
                const names = unscheduled.map(cb => JSON.parse(cb.value).name).join('<br>');

                return Swal.fire({
                    icon: "error",
                    title: "Invalid Selection",
                    html: `You can only reschedule applicants who already have training.<br><br>These applicants have none:<br><strong>${names}</strong>`
                });
            }

            // ✅ lahat may training, proceed
            this.openSetTraining(null, null, '', null, 'bulk');
        },

        openSetTraining(applicantId = null, fullName = '', range = '', schedule = null, mode = 'single') {
            this.trainingMode = mode;

            if (mode === 'single') {
                this.selectedApplicantId = applicantId;
                this.selectedApplicantName = fullName;
            } else {
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

                // ✅ store original time for comparison
                this.originalTrainingTimeStart = `${start.hour12} ${start.period}`;
                this.originalTrainingTimeEnd   = `${end.hour12} ${end.period}`;
            } else {
                this.trainingStartHour = '8';
                this.trainingStartPeriod = 'AM';
                this.trainingEndHour = '5';
                this.trainingEndPeriod = 'PM';

                this.originalTrainingTimeStart = '8 AM';
                this.originalTrainingTimeEnd   = '5 PM';
            }

            // --- prefill location ✅
            this.trainingLocation = schedule?.location ?? '';
            this.originalTrainingLocation = schedule?.location ?? '';

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
            const isSameAsOriginal = (
                newStartStr       === (this.originalTrainingDateRange?.split(' - ')[0] || '') &&
                newEndStr         === (this.originalTrainingDateRange?.split(' - ')[1] || '') &&
                trainingTimeStart === this.originalTrainingTimeStart &&
                trainingTimeEnd   === this.originalTrainingTimeEnd &&
                trainingLocation  === this.originalTrainingLocation
            );

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
                        await this.saveBulkTraining(this.selectedApplicants, {
                            start_date: newStartStr,
                            end_date: newEndStr,
                            start_time: this.formatTime(trainingTimeStart),
                            end_time: this.formatTime(trainingTimeEnd),
                            location: trainingLocation
                        });
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
            const isReschedule = !!this.originalTrainingDateRange && !isSameAsOriginal;

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

        async saveBulkTraining(applicantIds, payload) {
            const response = await fetch(`/hrAdmin/training-schedule/bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    applicants: applicantIds,
                    ...payload
                })
            });

            if (!response.ok) throw new Error('Failed to bulk set training schedule');
            return await response.json();
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
