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
                  <li class="breadcrumb-item" aria-current="page"><a href="#">BMN</a></li>
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
                <h3>Data BMN</h3>
                <a href="{{ route('bmn.create') }}" class="btn btn-shadow btn-primary">Tambah BMN</a>
                {{-- <button class="btn btn-shadow btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#create">Tambah BMN</button> --}}
              </div>
              <div class="card-body">
                <div class="dt-responsive table-responsive">
                  <table id="new-cons" class="display table table-striped table-hover dt-responsive nowrap" style="width: 100%">
                    <thead>
                      <tr>
                        <th>No.</th>
                        <th>Nama </th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($BMNs as $index => $dataBMN)
                      <tr>
                        <td>{{ $index +=1 }}</td>
                        <td>{{ $dataBMN->name}}</td>
                        <td>
                        {{--<a href="{{ route('BMN.index', ['edit' => $dataBMN->id]) }}" class="btn btn-sm btn-warning">
                            Edit
                            </a> --}}
                            <a href="{{ route('bmn.edit', $dataBMN->id) }}" class="btn btn-shadow btn-warning"><div class="d-flex justify-content-center align-items-center gap-2 text-center"><i class="ti ti-edit fs-5 text-white"></i>Edit</div> </a>
                            <form onclick="return confirm('Yakin ingin menghapus {{ $dataBMN->name }} ?')" action="{{ route('bmn.destroy', $dataBMN->id) }}" method="post" class="d-inline">
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
          <!-- `New` Constructor table end -->
        </div>
        <!-- [ Main Content ] end -->
      </div>
</div>
@endsection
