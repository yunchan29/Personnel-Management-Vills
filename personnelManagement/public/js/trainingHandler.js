document.addEventListener('alpine:init', () => {
    // Philippine Holidays (2025-2027)
    const philippineHolidays = [
        // 2025
        '2025-01-01', // New Year's Day
        '2025-01-25', // Chinese New Year
        '2025-02-25', // EDSA Revolution Anniversary
        '2025-04-09', // Araw ng Kagitingan (Bataan Day)
        '2025-04-17', // Maundy Thursday
        '2025-04-18', // Good Friday
        '2025-04-19', // Black Saturday
        '2025-05-01', // Labor Day
        '2025-06-12', // Independence Day
        '2025-08-21', // Ninoy Aquino Day
        '2025-08-25', // National Heroes Day (Last Monday of August)
        '2025-11-01', // All Saints' Day
        '2025-11-02', // All Souls' Day
        '2025-11-30', // Bonifacio Day
        '2025-12-08', // Feast of the Immaculate Conception
        '2025-12-24', // Christmas Eve (Special Non-Working Day)
        '2025-12-25', // Christmas Day
        '2025-12-26', // Additional Special Day
        '2025-12-30', // Rizal Day
        '2025-12-31', // New Year's Eve (Special Non-Working Day)

        // 2026
        '2026-01-01', // New Year's Day
        '2026-02-14', // Chinese New Year
        '2026-02-25', // EDSA Revolution Anniversary
        '2026-04-02', // Maundy Thursday
        '2026-04-03', // Good Friday
        '2026-04-04', // Black Saturday
        '2026-04-09', // Araw ng Kagitingan (Bataan Day)
        '2026-05-01', // Labor Day
        '2026-06-12', // Independence Day
        '2026-08-21', // Ninoy Aquino Day
        '2026-08-31', // National Heroes Day (Last Monday of August)
        '2026-11-01', // All Saints' Day
        '2026-11-02', // All Souls' Day
        '2026-11-30', // Bonifacio Day
        '2026-12-08', // Feast of the Immaculate Conception
        '2026-12-24', // Christmas Eve (Special Non-Working Day)
        '2026-12-25', // Christmas Day
        '2026-12-26', // Additional Special Day
        '2026-12-30', // Rizal Day
        '2026-12-31', // New Year's Eve (Special Non-Working Day)

        // 2027
        '2027-01-01', // New Year's Day
        '2027-02-06', // Chinese New Year
        '2027-02-25', // EDSA Revolution Anniversary
        '2027-03-25', // Maundy Thursday
        '2027-03-26', // Good Friday
        '2027-03-27', // Black Saturday
        '2027-04-09', // Araw ng Kagitingan (Bataan Day)
        '2027-05-01', // Labor Day
        '2027-06-12', // Independence Day
        '2027-08-21', // Ninoy Aquino Day
        '2027-08-30', // National Heroes Day (Last Monday of August)
        '2027-11-01', // All Saints' Day
        '2027-11-02', // All Souls' Day
        '2027-11-30', // Bonifacio Day
        '2027-12-08', // Feast of the Immaculate Conception
        '2027-12-24', // Christmas Eve (Special Non-Working Day)
        '2027-12-25', // Christmas Day
        '2027-12-26', // Additional Special Day
        '2027-12-30', // Rizal Day
        '2027-12-31'  // New Year's Eve (Special Non-Working Day)
    ];

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

        // Function converter for training schedule time - using TimeUtils
        to12h(hour24) {
            return TimeUtils.to12h(hour24);
        },

        formatTime(timeStr) {
            return TimeUtils.formatTime(timeStr);
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

            // 🔹 Check if all selected applicants share the same schedule
            const trainingData = this.selectedApplicants.map(a => ({
                start_date: a.training_start_date,
                end_date: a.training_end_date,
                start_time: a.training_start_time,
                end_time: a.training_end_time,
                location: a.training_location
            }));

            // Create unique keys for comparison
            const uniqueSchedules = [...new Set(trainingData.map(t =>
                `${t.start_date}|${t.end_date}|${t.start_time}|${t.end_time}|${t.location}`
            ))];

            let sharedSchedule = null;
            let dateRange = '';

            if (uniqueSchedules.length === 1 && trainingData[0].start_date && trainingData[0].end_date) {
                // All share the same schedule - prepare data for pre-population
                const shared = trainingData[0];

                // Format date range
                const startDate = new Date(shared.start_date);
                const endDate = new Date(shared.end_date);
                const fmt = (d) => {
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    const yyyy = d.getFullYear();
                    return `${mm}/${dd}/${yyyy}`;
                };
                dateRange = `${fmt(startDate)} - ${fmt(endDate)}`;

                sharedSchedule = {
                    start_time: shared.start_time,
                    end_time: shared.end_time,
                    location: shared.location
                };
            }

            // ✅ lahat may training, proceed with pre-populated data if available
            this.openSetTraining(null, null, dateRange, sharedSchedule, 'bulk');
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
                        delimiter: ' - ',
                        numberOfMonths: 2,
                        numberOfColumns: 2,
                        autoApply: true,
                        allowRepick: true,
                        startDate: start,
                        endDate: end,
                        minDate: new Date(),
                        lockDaysFilter: (date) => {
                            // Litepicker passes a DateTime object, need to convert to native Date
                            const jsDate = date instanceof Date ? date : new Date(date.getTime());

                            // Check if date is a Philippine holiday
                            const year = jsDate.getFullYear();
                            const month = String(jsDate.getMonth() + 1).padStart(2, '0');
                            const dayOfMonth = String(jsDate.getDate()).padStart(2, '0');
                            const dateStr = `${year}-${month}-${dayOfMonth}`;

                            return philippineHolidays.includes(dateStr);
                        }
                    });
                } else {
                    if (start && end) {
                        this.trainingPicker.setDateRange(start, end, true);
                    } else {
                        this.trainingPicker.clearSelection();
                    }

                    this.trainingPicker.setOptions({
                        minDate: new Date(),
                        lockDaysFilter: (date) => {
                            // Litepicker passes a DateTime object, need to convert to native Date
                            const jsDate = date instanceof Date ? date : new Date(date.getTime());

                            // Check if date is a Philippine holiday
                            const year = jsDate.getFullYear();
                            const month = String(jsDate.getMonth() + 1).padStart(2, '0');
                            const dayOfMonth = String(jsDate.getDate()).padStart(2, '0');
                            const dateStr = `${year}-${month}-${dayOfMonth}`;

                            return philippineHolidays.includes(dateStr);
                        }
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

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Server response:', errorText);
                throw new Error('Failed to bulk set training schedule');
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Expected JSON but received:', text);
                throw new Error('Server returned invalid response format');
            }

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

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Server response:', errorText);
                throw new Error('Failed to set training schedule');
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Expected JSON but received:', text);
                throw new Error('Server returned invalid response format');
            }

            return await response.json();
        },
    }));
});
