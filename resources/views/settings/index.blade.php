@extends('app')
@section('title', $title)
@section('content')
<div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a href="#">Settings</a></li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">{{ $title }}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <h3>Application Settings</h3>
          </div>
          <div class="card-body">
            <form action="{{ route('settings.update') }}" method="POST">
              @csrf
              <div class="mb-3">
                <label for="admin_phone" class="form-label">Admin WhatsApp Phone Number</label>
                <input type="text" class="form-control" id="admin_phone" name="admin_phone" value="{{ $settings['admin_phone'] ?? '' }}" placeholder="e.g. 6281234567890">
                <small class="form-text text-muted">Enter the phone number without + or spaces, e.g. 6281234567890</small>
              </div>
              <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
