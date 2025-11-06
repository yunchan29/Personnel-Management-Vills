/**
 * Checkbox Utilities
 * Shared checkbox management functions for bulk selection
 */

export const CheckboxUtils = {
    /**
     * Toggle all visible checkboxes
     * @param {Event} event - The checkbox change event
     * @param {Array} selectedArray - The array to store selected items
     * @param {string} checkboxSelector - CSS selector for checkboxes
     * @param {string} idField - The field name to use for comparing items (default: 'application_id')
     * @returns {Array} Updated selected array
     */
    toggleSelectAll(event, selectedArray, checkboxSelector = '.applicant-checkbox', idField = 'application_id') {
        const isChecked = event.target.checked;

        // Only get visible checkboxes
        const visibleCheckboxes = Array.from(document.querySelectorAll(checkboxSelector))
            .filter(cb => cb.offsetParent !== null);

        visibleCheckboxes.forEach(cb => {
            cb.checked = isChecked;
            const data = JSON.parse(cb.value);

            if (isChecked) {
                // Add if not already selected
                if (!selectedArray.some(a => a[idField] === data[idField])) {
                    selectedArray.push(data);
                }
            } else {
                // Remove if deselected
                selectedArray = selectedArray.filter(a => a[idField] !== data[idField]);
            }
        });

        return selectedArray;
    },

    /**
     * Update master checkbox state (checked, unchecked, or indeterminate)
     * @param {HTMLElement} rootElement - The root element to search within
     * @param {Array} selectedArray - The array of selected items
     * @param {string} checkboxSelector - CSS selector for checkboxes
     * @param {string} masterRefName - The x-ref name of the master checkbox (default: 'masterCheckbox')
     * @param {string} idField - The field name to use for comparing items
     */
    updateMasterCheckbox(rootElement, selectedArray, checkboxSelector = '.applicant-checkbox:not(:disabled)', masterRefName = 'masterCheckbox', idField = 'application_id') {
        const master = rootElement.querySelector(`[x-ref="${masterRefName}"]`);
        if (!master) return;

        const visibleCheckboxes = Array.from(rootElement.querySelectorAll(checkboxSelector))
            .filter(cb => cb.offsetParent !== null);
        const total = visibleCheckboxes.length;

        const selected = visibleCheckboxes.filter(cb => {
            const value = JSON.parse(cb.value);
            return selectedArray.some(a => a[idField] === value[idField]);
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
     * Toggle a single checkbox item
     * @param {Event} event - The checkbox change event
     * @param {Array} selectedArray - The array of selected items
     * @param {string} idField - The field name to use for comparing items
     * @returns {Array} Updated selected array
     */
    toggleItem(event, selectedArray, idField = 'application_id') {
        const checked = event.target.checked;
        const value = JSON.parse(event.target.value);
        const itemId = value[idField] ?? value.id;

        if (checked) {
            // Add if not already selected
            if (!selectedArray.some(a => (a[idField] ?? a.id) === itemId)) {
                selectedArray.push(value);
            }
        } else {
            // Remove if deselected
            selectedArray = selectedArray.filter(a => (a[idField] ?? a.id) !== itemId);
        }

        return selectedArray;
    },

    /**
     * Get applicant ID (handles both application_id and id fields)
     * @param {Object} applicant - The applicant object
     * @returns {number} The applicant ID
     */
    getApplicantId(applicant) {
        return applicant.application_id ?? applicant.id;
    }
};
