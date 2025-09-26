document.addEventListener("DOMContentLoaded", () => {
    // Contract Schedule Save
    document.querySelectorAll('.schedule-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            // Get values
            const dateField = form.querySelector('input[name="contract_date"]');
            const timeField = form.querySelector('input[name="contract_signing_time"]');

            const dateVal = dateField ? dateField.value.trim() : '';
            const timeVal = timeField ? timeField.value.trim() : '';

            // Validate
            if (!dateVal || !timeVal) {
                e.preventDefault();
                Swal.fire({
                    title: 'Missing Information',
                    text: 'Please select both a contract signing date and time before submitting.',
                    icon: 'warning',
                    confirmButtonColor: '#BD6F22',
                    confirmButtonText: 'Okay'
                });
            }
        });
    });

    // Success Toast
    if (window.contractScheduleSuccess) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: window.contractScheduleSuccess,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    }
});
