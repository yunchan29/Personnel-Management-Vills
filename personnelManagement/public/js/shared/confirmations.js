/**
 * Shared SweetAlert2 Confirmation Utilities
 * Provides standardized confirmation dialogs across the application
 */

/**
 * Show a delete confirmation dialog
 * @param {string} title - Dialog title
 * @param {string} text - Dialog text
 * @param {Function} onConfirm - Callback function when confirmed
 */
function confirmDelete(title = 'Are you sure?', text = 'This action cannot be undone.', onConfirm) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed && onConfirm) {
            onConfirm();
        }
    });
}

/**
 * Show an action confirmation dialog
 * @param {string} title - Dialog title
 * @param {string} text - Dialog text
 * @param {Function} onConfirm - Callback function when confirmed
 * @param {string} confirmText - Custom confirm button text
 */
function confirmAction(title, text, onConfirm, confirmText = 'Yes, proceed!') {
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#BD6F22',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText
    }).then((result) => {
        if (result.isConfirmed && onConfirm) {
            onConfirm();
        }
    });
}

/**
 * Show a success message
 * @param {string} title - Success title
 * @param {string} text - Success message
 * @param {number} timer - Auto-close timer (ms), set to 0 for manual close
 */
function showSuccess(title = 'Success', text = 'Operation completed successfully.', timer = 2500) {
    const options = {
        icon: 'success',
        title: title,
        text: text,
        showConfirmButton: timer === 0
    };

    if (timer > 0) {
        options.timer = timer;
    }

    Swal.fire(options);
}

/**
 * Show an error message
 * @param {string} title - Error title
 * @param {string} text - Error message
 */
function showError(title = 'Error', text = 'Something went wrong.') {
    Swal.fire({
        icon: 'error',
        title: title,
        text: text,
        confirmButtonColor: '#BD6F22'
    });
}

/**
 * Initialize delete buttons with confirmation
 * Automatically attaches to elements with class 'confirm-delete'
 */
function initDeleteConfirmations() {
    document.querySelectorAll('.confirm-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const title = this.dataset.title || 'Are you sure?';
            const text = this.dataset.text || 'This action cannot be undone.';

            confirmDelete(title, text, () => {
                if (form) form.submit();
            });
        });
    });
}

/**
 * Initialize form submit confirmations
 * Automatically attaches to forms with class 'confirm-submit'
 */
function initSubmitConfirmations() {
    document.querySelectorAll('.confirm-submit').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const title = this.dataset.title || 'Confirm Submission';
            const text = this.dataset.text || 'Are you sure you want to submit this form?';
            const confirmText = this.dataset.confirmText || 'Yes, submit!';

            confirmAction(title, text, () => {
                this.submit();
            }, confirmText);
        });
    });
}

// Auto-initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initDeleteConfirmations();
    initSubmitConfirmations();
});
