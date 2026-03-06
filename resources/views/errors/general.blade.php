@extends('app')
@section('title', $title ?? 'Error')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="card-title">{{ $title }}</h4>
                    <p class="card-text">{{ $message }}</p>

                    @if(config('app.debug') && isset($trace))
                    <div class="text-left mt-4">
                        <details>
                            <summary class="btn btn-outline-secondary btn-sm">Show Technical Details</summary>
                            <pre class="mt-3 p-3 bg-light rounded small" style="font-size: 0.75rem; max-height: 300px; overflow-y: auto;">{{ $trace }}</pre>
                        </details>
                    </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ $redirect }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Go to {{ auth()->check() ? 'Dashboard' : 'Login' }}
                        </a>
                        <button type="button" class="btn btn-secondary ms-2" onclick="window.history.back()">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </button>
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
        icon: 'error',
        title: '{{ $title }}',
        text: '{{ $message }}',
        confirmButtonText: 'OK',
        allowOutsideClick: false
    });
});
</script>
@endsection
