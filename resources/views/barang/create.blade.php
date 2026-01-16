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
                  <li class="breadcrumb-item"><a href="#">kode barang</a></li>
                  <li class="breadcrumb-item" aria-current="page">Tambah kode barang</li>
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
                <h3>Tambah kode barang</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('barang.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  old('kode_barang') }}" type="number" class="form-control" id="floatingName" placeholder="Kode barang" name="kode_barang">
                                        <label for="floatingName">Kode barang</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  old('nama_barang') }}" type="text" class="form-control" id="floatingName" placeholder="Nama barang" name="nama_barang">
                                        <label for="floatingName">Nama barang</label>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <i class="ti ti-briefcase font-size-icon text-blue-500"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Submit</button>
                    <a href="{{ route('barang.index') }}" class="btn btn-shadow btn-secondary">Kembali</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
</div>
@endsection
