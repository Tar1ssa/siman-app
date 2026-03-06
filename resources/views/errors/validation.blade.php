@extends('app')
@section('title', $title ?? 'Validation Error')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="card-title text-center">{{ $title }}</h4>
                    <p class="card-text text-center">{{ $message }}</p>

                    @if(isset($errors) && count($errors) > 0)
                    <div class="alert alert-danger mt-4">
                        <h6>Please correct the following errors:</h6>
                        <ul class="mb-0">
                            @foreach($errors as $field => $fieldErrors)
                                @foreach($fieldErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary" onclick="goBack()">
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
function goBack() {
    window.history.back();
}

document.addEventListener('DOMContentLoaded', function() {
    // Show SweetAlert
    Swal.fire({
        icon: 'error',
        title: '{{ $title }}',
        html: `{{ $message }}
               @if(isset($errors) && count($errors) > 0)
               <br><br><small>Please check the errors below and try again.</small>
               @endif`,
        confirmButtonText: 'OK',
        allowOutsideClick: false
    });
});
</script>
@endsection
