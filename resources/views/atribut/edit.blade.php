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
                  <li class="breadcrumb-item"><a href="#">atribut</a></li>
                  <li class="breadcrumb-item" aria-current="page">Tambah atribut</li>
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
                <h3>Edit atribut</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('atribut.update', $atribut->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">

                                <label class="form-label" for="key">Key (unique)</label>
                                <input class="form-control" type="text" name="key" value="{{ $atribut->key }}">
                            </div>
                            <div class="mb-3">

                                <label class="form-label" for="label">Label</label>
                                <input class="form-control" type="text" name="label" value="{{ $atribut->label }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="data_type">Tipe data</label>
                                <select class="form-control" name="data_type">
                                    <option value="string" @selected($atribut->data_type === 'string')>Text</option>
                                    <option value="number" @selected($atribut->data_type === 'number')>Number</option>
                                    <option value="date" @selected($atribut->data_type === 'date')>Date</option>
                                </select>
                            </div>

                        </div>
                        {{-- <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <i class="ti ti-tag font-size-icon text-blue-500"></i>
                        </div> --}}
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Submit</button>
                    <a href="{{ route('atribut.index') }}" class="btn btn-shadow btn-secondary">Kembali</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
</div>
@endsection
