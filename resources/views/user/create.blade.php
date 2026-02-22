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
                  <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                  <li class="breadcrumb-item"><a href="#">User</a></li>
                  <li class="breadcrumb-item" aria-current="page">Tambah User</li>
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

        <!-- [ Main Content ] start -->
        <div class="row">
          <!-- [ form-element ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3>Tambah User Baru</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('user.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  old('name') }}" type="text" class="form-control" id="floatingName" placeholder="Nama" name="name">
                                        <label for="floatingName">Nama User</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  old('email') }}" type="email" class="form-control" id="floatingEmail" placeholder="Email" name="email">
                                        <label for="floatingEmail">Email</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="createPassword" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="createPassword" placeholder="Password" name="password">
                                        <button type="button" class="btn btn-outline-secondary" id="toggleCreatePassword" tabindex="-1">
                                            <i class="bi bi-eye-slash" id="createPasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="createPasswordConfirm" class="form-label">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="createPasswordConfirm" placeholder="Konfirmasi Password" name="password_confirmation">
                                        <button type="button" class="btn btn-outline-secondary" id="toggleCreatePasswordConfirm" tabindex="-1">
                                            <i class="bi bi-eye-slash" id="createPasswordConfirmIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <select class="form-control" id="floatingLevel" name="level_id">
                                            <option value="">Pilih Level</option>
                                            @foreach ($levels as $level)
                                                <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                                    {{ $level->level_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="floatingLevel">Level User</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <select class="form-control" id="floatingUnitKerja" name="unit_kerja_id">
                                            <option value="">Pilih Unit Kerja (Opsional)</option>
                                            @foreach ($unitkerjas as $unitkerja)
                                                <option value="{{ $unitkerja->id }}" {{ old('unit_kerja_id') == $unitkerja->id ? 'selected' : '' }}>
                                                    {{ $unitkerja->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="floatingUnitKerja">Unit Kerja</label>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <i class="ti ti-user font-size-icon text-blue-500"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Submit</button>
                    <a href="{{ route('user.index') }}" class="btn btn-shadow btn-secondary">Kembali</a>
                  </form>
              </div>
            </div>
          </div>
        </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleCreatePassword = document.getElementById('toggleCreatePassword');
    var createPasswordInput = document.getElementById('createPassword');
    var createPasswordIcon = document.getElementById('createPasswordIcon');
    if (toggleCreatePassword && createPasswordInput && createPasswordIcon) {
        toggleCreatePassword.addEventListener('click', function() {
            if (createPasswordInput.type === 'password') {
                createPasswordInput.type = 'text';
                createPasswordIcon.classList.remove('bi-eye-slash');
                createPasswordIcon.classList.add('bi-eye');
            } else {
                createPasswordInput.type = 'password';
                createPasswordIcon.classList.remove('bi-eye');
                createPasswordIcon.classList.add('bi-eye-slash');
            }
        });
    }
    var toggleCreatePasswordConfirm = document.getElementById('toggleCreatePasswordConfirm');
    var createPasswordConfirmInput = document.getElementById('createPasswordConfirm');
    var createPasswordConfirmIcon = document.getElementById('createPasswordConfirmIcon');
    if (toggleCreatePasswordConfirm && createPasswordConfirmInput && createPasswordConfirmIcon) {
        toggleCreatePasswordConfirm.addEventListener('click', function() {
            if (createPasswordConfirmInput.type === 'password') {
                createPasswordConfirmInput.type = 'text';
                createPasswordConfirmIcon.classList.remove('bi-eye-slash');
                createPasswordConfirmIcon.classList.add('bi-eye');
            } else {
                createPasswordConfirmInput.type = 'password';
                createPasswordConfirmIcon.classList.remove('bi-eye');
                createPasswordConfirmIcon.classList.add('bi-eye-slash');
            }
        });
    }
});
</script>
