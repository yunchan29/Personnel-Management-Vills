/**
 * Selection Mixin for Applicant Management
 * Provides common selection/checkbox functionality across multiple handlers
 */
export const selectionMixin = {
    selectedApplicants: [],

    /**
     * Get applicant ID from applicant object
     */
    getApplicantId(applicant) {
        return applicant.application_id ?? applicant.id;
    },

    /**
     * Get all local checkboxes (visible, not disabled)
     */
    getLocalCheckboxes(checkboxClass = '.applicant-checkbox:not(:disabled)') {
        return Array.from(this.$root.querySelectorAll(checkboxClass));
    },

    /**
     * Toggle select all checkboxes
     */
    toggleSelectAll(event) {
        const isChecked = event.target.checked;
        const visibleCheckboxes = Array.from(document.querySelectorAll('.applicant-checkbox'))
            .filter(cb => cb.offsetParent !== null);

        visibleCheckboxes.forEach(cb => {
            cb.checked = isChecked;
            const data = JSON.parse(cb.value);

            if (isChecked) {
                if (!this.selectedApplicants.some(a => a.application_id === data.application_id)) {
                    this.selectedApplicants.push(data);
                }
            } else {
                this.selectedApplicants = this.selectedApplicants.filter(
                    a => a.application_id !== data.application_id
                );
            }
        });

        this.updateMasterCheckbox();
    },

    /**
     * Update master checkbox state (checked/indeterminate/unchecked)
     */
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

    /**
     * Toggle individual item checkbox
     */
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

    /**
     * Clear all selections
     */
    clearSelections() {
        this.selectedApplicants = [];
        const checkboxes = this.getLocalCheckboxes();
        checkboxes.forEach(cb => cb.checked = false);
        this.updateMasterCheckbox();
    }
};
