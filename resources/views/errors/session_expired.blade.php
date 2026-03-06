@extends('app')
@section('title', $title ?? 'Session Expired')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-clock text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="card-title">{{ $title }}</h4>
                    <p class="card-text">{{ $message }}</p>
                    <div class="mt-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Redirecting...</p>
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
        icon: 'warning',
        title: '{{ $title }}',
        text: '{{ $message }}',
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    }).then(() => {
        window.location.href = '{{ $redirect }}';
    });

    // Auto redirect after 3 seconds
    setTimeout(() => {
        window.location.href = '{{ $redirect }}';
    }, 3000);
});
</script>
@endsection
