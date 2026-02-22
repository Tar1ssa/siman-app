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
                  <li class="breadcrumb-item"><a href="#">unit kerja</a></li>
                  <li class="breadcrumb-item" aria-current="page">Edit Unit Kerja</li>
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
                <h3>Edit Unit Kerja</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('unitkerja.update', $unitkerja->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  $unitkerja->name }}" type="text" class="form-control" id="floatingName" placeholder="Nama Unit Kerja" name="name">
                                        <label for="floatingName">Nama Unit Kerja</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating ">
                                        <input value="{{  $unitkerja->nameId }}" type="text" class="form-control" id="floatingName" placeholder="Kode Unit Kerja" name="nameId">
                                        <label for="floatingName">Kode Unit Kerja</label>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <i class="ti ti-building font-size-icon text-blue-500"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Submit</button>
                    <a href="{{ route('unitkerja.index') }}" class="btn btn-shadow btn-secondary">Kembali</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
</div>
@endsection
