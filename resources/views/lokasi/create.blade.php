@extends('app')
@section('content')
<div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                  <li class="breadcrumb-item"><a href="#">Lokasi Ruang</a></li>
                  <li class="breadcrumb-item" aria-current="page">Tambah Lokasi Ruang</li>
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
                <h3>Tambah Lokasi Ruang</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('lokasi.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unitkerja_id" class="form-label">Unit Kerja</label>
                                            <select
                                                class="form-control"
                                                data-unitkerja
                                                name="unitkerja_id"
                                                id="unitkerjaSelect"
                                                >
                                                <option value="" selected disabled>--Pilih Unit Kerja--</option>
                                                @foreach ($unitkerja as $keyunitkerja)

                                                    <option value="{{ $keyunitkerja->id }}" {{ old('unit_kerja_id') == $keyunitkerja->id ? 'selected' : '' }}>{{ $keyunitkerja->name }}</option>
                                                @endforeach
                                            </select>
                                </div>

                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  old('name') }}" type="text" class="form-control" id="floatingName" placeholder="Nama lokasi" name="name">
                                        <label for="floatingName">Nama Lokasi</label>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <i class="ti ti-location font-size-icon text-blue-500"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Submit</button>
                    <a href="{{ route('lokasi.index') }}" class="btn btn-shadow btn-secondary">Kembali</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
</div>

<script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var UnitSelect = document.querySelectorAll('[data-unitkerja]');
            for (i = 0; i < UnitSelect.length; ++i) {
            var unitelement = UnitSelect[i];
            new Choices(unitelement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari unit kerja'
            });
            }
        });
    </script>
@endsection
