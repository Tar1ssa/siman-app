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
                  <li class="breadcrumb-item" aria-current="page"><a href="#">unit kerja</a></li>
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
          <div class="col-sm-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <h3>Data Unit Kerja</h3>
                <a href="{{ route('unitkerja.create') }}" class="btn btn-shadow btn-primary">Tambah Unit Kerja</a>
              </div>
              <div class="card-body">
                <div class="dt-responsive table-responsive">
                  <table id="new-cons" class="display table table-striped table-hover dt-responsive nowrap" style="width: 100%">
                    <thead>
                      <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Kode</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($UnitKerjas as $index => $data)
                      <tr>
                        <td>{{ $index +=1 }}</td>
                        <td>{{ $data->name}}</td>
                        <td>{{ $data->nameId}}</td>
                        <td>
                            <a href="{{ route('unitkerja.edit', $data->id) }}" class="btn btn-shadow btn-warning"><div class="d-flex justify-content-center align-items-center gap-2 text-center"><i class="ti ti-edit fs-5 text-white"></i>Edit</div> </a>
                            <form onclick="return confirm('Yakin ingin menghapus {{ $data->name }} ?')" action="{{ route('unitkerja.destroy', $data->id) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                <button class="btn btn-shadow btn-danger"><div class="d-flex justify-content-center align-items-center gap-2 text-center"><i class="ti ti-trash fs-5 text-white"></i>Hapus</div></button>

                            </form>
                        </td>
                      </tr>
                       @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
</div>
@endsection
