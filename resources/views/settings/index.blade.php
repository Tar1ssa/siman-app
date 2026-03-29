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
                <small class="form-text text-muted">Masukkan nomor WA admin tanpa + atau spasi, contoh. 6281234567890</small>
              </div>
              <hr>
              <h4>Generate PSP settings</h4>
              <div class="mb-3">
                <label for="biro" class="form-label">Kepala Biro Umum</label>
                <input type="text" class="form-control" id="biro" name="biro" value="{{ $settings['biro'] ?? '' }}" placeholder="e.g. John Smith">
                <small class="form-text text-muted">Masukkan nama kepala biro umum, contoh. John Smith</small>
              </div>
              <div class="mb-3">
                <label for="nip_biro" class="form-label">NIP Kepala Biro Umum</label>
                <input type="text" class="form-control" id="nip_biro" name="nip_biro" value="{{ $settings['nip_biro'] ?? '' }}" placeholder="e.g. 19690705 199603 1 001">
                <small class="form-text text-muted">Masukkan NIP kepala biro umum, contoh. 19690705 199603 1 001</small>
              </div>
              <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
