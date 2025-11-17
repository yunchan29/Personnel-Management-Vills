@extends(auth()->user()->role === 'applicant' ? 'layouts.applicantHome' : 'layouts.employeeHome')

@section('content')
    <x-shared.settings />
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#BD6F22'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#DD6161'
            });
        @endif

        @if(auth()->user()->role === 'applicant')
        // Delete account confirmation
        document.getElementById('deleteAccountBtn').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "This action is permanent and cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#DD6161',
                cancelButtonColor: '#bbb',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form when confirmed
                    document.querySelector('form[action="{{ route('user.deleteAccount') }}"]').submit();
                }
            });
        });
        @endif
    });
</script>
@endsection
