@extends(auth()->user()->role === 'hrAdmin' ? 'layouts.hrAdmin' : 'layouts.hrStaff')

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
    });
</script>
@endsection
