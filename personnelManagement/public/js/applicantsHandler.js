
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

        showTrainingModal: false,
        trainingApplicant: null,
        trainingPicker: null,

        applicants: [],
        removedApplicants: [],
        selectedApplicants: [],
        feedbackMessage: '',
        feedbackVisible: false,
        showAll: false,

    
        // Convert 12-hour format to 24-hour format
        to24h(hour12, period) {
        let h = parseInt(hour12, 10);
        if (period === 'AM') {
            if (h === 12) h = 0;      // 12 AM -> 00
        } else { // PM
            if (h !== 12) h += 12;    // 1..11 PM -> 13..23; 12 PM -> 12
        }
        return String(h).padStart(2, '0');
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

        async bulkApprove() {
            try {
                const response = await fetch("{{ route('applications.bulkUpdateStatus') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        ids: this.selectedApplicants,
                        status: 'approved'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.feedbackMessage = data.message;
                    this.feedbackVisible = true;

                    // remove approved applicants from table
                    this.selectedApplicants.forEach(id => {
                        document.querySelector(`[data-applicant-id="${id}"]`).remove();
                    });

                    this.selectedApplicants = [];
                }
            } catch (error) {
                console.error(error);
            }
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

openSetInterview(applicationId, name, userId, rawInterviewDate = '') {
  this.interviewApplicant = {
    application_id: applicationId,
    user_id: userId,
    name: name
  };

  if (rawInterviewDate) {
    // rawInterviewDate e.g. "2025-08-28 13:00:00"
    const [date, time] = rawInterviewDate.split(' ');
    const hour24Str = (time || '00:00:00').split(':')[0]; // "13"
    const hour24 = parseInt(hour24Str, 10);

    // convert 24h -> 12h + period
    let hour12 = hour24 % 12;
    hour12 = hour12 === 0 ? 12 : hour12;
    const period = hour24 < 12 ? 'AM' : 'PM';

    // set current fields
    this.interviewDate = date || '';
    this.interviewHour = hour12;
    this.interviewPeriod = period;

    // store normalized original for comparison
    const normalizedOriginal = `${this.interviewDate} ${hour24Str.padStart(2,'0')}:00:00`;
    this.originalNormalizedDT = normalizedOriginal;
  } else {
    // defaults when there is no existing schedule
    this.interviewDate = '';
    this.interviewHour = 1;
    this.interviewPeriod = 'PM';
    this.originalNormalizedDT = ''; // none set yet
  }

  this.showInterviewModal = true;
},



async submitInterviewDate() {
  if (!this.interviewDate || !this.interviewHour || !this.interviewPeriod) {
    alert('Please select both date and time.');
    return;
  }

  // build NEW normalized datetime: Y-m-d HH:00:00
  const hour24 = this.to24h(this.interviewHour, this.interviewPeriod);
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



        openSetTraining(applicantId, fullName, range = '') {
            this.selectedApplicantId = applicantId;
            this.selectedApplicantName = fullName;
            this.selectedTrainingDateRange = range;

            // Normalize the original training range to MM/DD/YYYY - MM/DD/YYYY format
            if (range && range.includes(' - ')) {
                const [startRaw, endRaw] = range.split(' - ').map(d => new Date(d.trim()));
                const formatDate = (date) => {
                    const mm = String(date.getMonth() + 1).padStart(2, '0');
                    const dd = String(date.getDate()).padStart(2, '0');
                    const yyyy = date.getFullYear();
                    return `${mm}/${dd}/${yyyy}`;
                };
                this.originalTrainingDateRange = `${formatDate(startRaw)} - ${formatDate(endRaw)}`;
            } else {
                this.originalTrainingDateRange = '';
            }

            this.showTrainingModal = true;
            console.log('Received range:', range);
            console.log('Normalized originalTrainingDateRange:', this.originalTrainingDateRange);

            this.$nextTick(() => {
                setTimeout(() => {
                    const ref = this.$refs.trainingDateRange;
                    if (!ref) {
                        console.warn("trainingDateRange input not found");
                        return;
                    }

                    const [start, end] = (range && range.includes(' - ')) ? range.split(' - ') : [null, null];

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
                        });
                        console.log("Picker initialized with:", start, end);
                    } else {
                        if (start && end) {
                            this.trainingPicker.setDateRange(start, end, true);
                            console.log("Date range set:", start, end);
                        } else {
                            this.trainingPicker.clearSelection();
                        }
                    }
                }, 50);
            });
        },


        async submitTrainingSchedule() {
            const selectedRange = this.$refs.trainingDateRange.value?.trim();

            if (!selectedRange || !selectedRange.includes(' - ')) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Invalid Input',
                    text: 'Please select a valid training date range.',
                });
                return;
            }

            const [newStartStr, newEndStr] = selectedRange.split(' - ').map(s => s.trim());
            const newStart = new Date(newStartStr);
            const newEnd = new Date(newEndStr);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (isNaN(newStart) || isNaN(newEnd) || newStart < today || newEnd < today) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date Range',
                    text: 'The training schedule must be set to future dates.',
                });
                return;
            }

            let isSameAsOriginal = false;
            if (this.originalTrainingDateRange && this.originalTrainingDateRange.includes(' - ')) {
                const [originalStartStr, originalEndStr] = this.originalTrainingDateRange.split(' - ').map(d => d.trim());
                isSameAsOriginal = (newStartStr === originalStartStr && newEndStr === originalEndStr);
            }

            if (isSameAsOriginal) {
                this.feedbackMessage = 'No changes were made to the training schedule.';
                this.feedbackVisible = true;
                setTimeout(() => this.feedbackVisible = false, 3000);
                this.showTrainingModal = false;
                return;
            }

            const isReschedule = !!this.originalTrainingDateRange;

            this.loading = true;

            const proceed = async () => {
                try {
                    const response = await fetch(`/hrAdmin/training-schedule/${this.selectedApplicantId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            training_schedule: selectedRange
                        })
                    });

                    if (!response.ok) throw new Error('Failed to set training schedule');

                    const result = await response.json();

                    const row = document.querySelector(`tr[data-applicant-id='${this.selectedApplicantId}']`);
                    if (row) row.setAttribute('data-training-range', selectedRange);

                    const index = this.applicants.findIndex(a => a.id === this.selectedApplicantId);
                    if (index !== -1) {
                        this.applicants[index].training = selectedRange;
                        this.applicants[index].status = 'scheduled_for_training';
                        this.applicants = [...this.applicants];
                    }

                    this.feedbackMessage = result.message || 'Training schedule set successfully!';
                    this.feedbackVisible = true;
                    setTimeout(() => location.reload(), 2000);
                    this.showTrainingModal = false;

                } catch (error) {
                    alert('Error: ' + error.message);
                } finally {
                    this.loading = false;
                }
            };

            if (isReschedule) {
                const result = await Swal.fire({
                    title: "You're about to reschedule",
                    html: `New training date range:<br><strong>${selectedRange}</strong><br><br>Do you want to continue?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#BD6F22',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, reschedule'
                });

                if (result.isConfirmed) {
                    this.loading = true; // 🔄 show loading after confirm click
                    proceed();
                }
            } else {
                this.loading = true; // normal schedule, show loading immediately
                proceed();
            }
        }
    }));
});
