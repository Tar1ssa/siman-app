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
                  <li class="breadcrumb-item" aria-current="page">Edit User</li>
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
                <h3>Edit User</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('user.update', $user->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  $user->name }}" type="text" class="form-control" id="floatingName" placeholder="Nama" name="name">
                                        <label for="floatingName">Nama User</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  $user->email }}" type="email" class="form-control" id="floatingEmail" placeholder="Email" name="email">
                                        <label for="floatingEmail">Email</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="editPassword" class="form-label">Password (Opsional)</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="editPassword" placeholder="Password (kosongkan jika tidak dirubah)" name="password">
                                        <button type="button" class="btn btn-outline-secondary" id="toggleEditPassword" tabindex="-1">
                                            <i class="bi bi-eye-slash" id="editPasswordIcon"></i>
                                        </button>
                                    </div>
                                    <small>Masukkan password baru untuk mengubah password</small>
                                </div>

                                <div class="mb-3">
                                    <label for="editPasswordConfirm" class="form-label">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="editPasswordConfirm" placeholder="Konfirmasi Password" name="password_confirmation">
                                        <button type="button" class="btn btn-outline-secondary" id="toggleEditPasswordConfirm" tabindex="-1">
                                            <i class="bi bi-eye-slash" id="editPasswordConfirmIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <select class="form-control" id="floatingLevel" name="level_id">
                                            <option value="">Pilih Level</option>
                                            @foreach ($levels as $level)
                                                <option value="{{ $level->id }}" {{ $user->level_id == $level->id ? 'selected' : '' }}>
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
                                                <option value="{{ $unitkerja->id }}" {{ $user->unit_kerja_id == $unitkerja->id ? 'selected' : '' }}>
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
    var toggleEditPassword = document.getElementById('toggleEditPassword');
    var editPasswordInput = document.getElementById('editPassword');
    var editPasswordIcon = document.getElementById('editPasswordIcon');
    if (toggleEditPassword && editPasswordInput && editPasswordIcon) {
        toggleEditPassword.addEventListener('click', function() {
            if (editPasswordInput.type === 'password') {
                editPasswordInput.type = 'text';
                editPasswordIcon.classList.remove('bi-eye-slash');
                editPasswordIcon.classList.add('bi-eye');
            } else {
                editPasswordInput.type = 'password';
                editPasswordIcon.classList.remove('bi-eye');
                editPasswordIcon.classList.add('bi-eye-slash');
            }
        });
    }
    var toggleEditPasswordConfirm = document.getElementById('toggleEditPasswordConfirm');
    var editPasswordConfirmInput = document.getElementById('editPasswordConfirm');
    var editPasswordConfirmIcon = document.getElementById('editPasswordConfirmIcon');
    if (toggleEditPasswordConfirm && editPasswordConfirmInput && editPasswordConfirmIcon) {
        toggleEditPasswordConfirm.addEventListener('click', function() {
            if (editPasswordConfirmInput.type === 'password') {
                editPasswordConfirmInput.type = 'text';
                editPasswordConfirmIcon.classList.remove('bi-eye-slash');
                editPasswordConfirmIcon.classList.add('bi-eye');
            } else {
                editPasswordConfirmInput.type = 'password';
                editPasswordConfirmIcon.classList.remove('bi-eye');
                editPasswordConfirmIcon.classList.add('bi-eye-slash');
            }
        });
    }
});
</script>
