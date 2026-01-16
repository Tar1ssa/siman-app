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
                  <li class="breadcrumb-item"><a href="#">Satker</a></li>
                  <li class="breadcrumb-item" aria-current="page">Tambah Satker</li>
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
                <h3>Edit Satker</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('satker.update', $Satker->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  $Satker->kode_satker }}" type="text" class="form-control" id="floatingName" placeholder="Kode Satker" name="kode_satker">
                                        <label for="floatingName">Kode Satker</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  $Satker->nama_satker }}" type="text" class="form-control" id="floatingName" placeholder="Nama Satker" name="nama_satker">
                                        <label for="floatingName">Nama Satker</label>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <i class="ti ti-briefcase font-size-icon text-blue-500"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Submit</button>
                    <a href="{{ route('satker.index') }}" class="btn btn-shadow btn-secondary">Kembali</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
</div>
@endsection
