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
                  <li class="breadcrumb-item" aria-current="page"><a href="#">atribut</a></li>
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
                <h3>Data atribut</h3>
                <a href="{{ route('atribut.create') }}" class="btn btn-shadow btn-primary">Tambah atribut</a>
                {{-- <button class="btn btn-shadow btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#create">Tambah atribut</button> --}}
              </div>
              <div class="card-body">
                <div class="dt-responsive table-responsive">
                  <table id="new-cons" class="display table table-striped table-hover dt-responsive nowrap" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Label</th>
                            <th>Type</th>
                            <th>Used By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atributs as $attr)
                            <tr>
                                <td>{{ $attr->key }}</td>
                                <td>{{ $attr->label }}</td>
                                <td>{{ $attr->data_type }}</td>
                                <td>{{ $attr->identitas_count }} identitas</td>
                                <td class="flex flex-column align-items-center gap-2 justify-content-center">
                                    <a class="btn btn-sm btn-warning" href="{{ route('atribut.edit', $attr) }}">Edit</a>
                                    <form method="POST" onclick="return confirm('Yakin ingin menghapus {{ $attr->label }} ?')" action="{{ route('atribut.destroy', $attr) }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
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
