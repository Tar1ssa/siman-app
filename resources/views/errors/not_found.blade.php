@extends('app')
@section('title', $title ?? 'Not Found')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-search text-info" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="card-title">{{ $title }}</h4>
                    <p class="card-text">{{ $message }}</p>
                    <div class="mt-4">
                        <a href="{{ $redirect }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Go to {{ auth()->check() ? 'Dashboard' : 'Login' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show SweetAlert
    Swal.fire({
        icon: 'info',
        title: '{{ $title }}',
        text: '{{ $message }}',
        confirmButtonText: 'OK',
        allowOutsideClick: false
    });
});
</script>
@endsection
