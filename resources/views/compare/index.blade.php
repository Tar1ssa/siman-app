@extends('app')
@section('title', $title)
@section('dependencies')
<link rel="stylesheet" href="{{ asset('/assets/asset/css/plugins/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/asset/css/plugins/responsive.bootstrap5.min.css') }}">
@endsection
@section('content')
    <div class="pc-content overflow-x-hidden">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item" aria-current="page"><a href="#">Data Internal</a></li>
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

        <div class="row">
          <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Tabel Komparasi</h3>
                    <a class="btn btn-primary px-3 " data-bs-toggle="collapse" href="#collapseAction" role="button" aria-expanded="false" aria-controls="collapseAction">
                        Menu
                    </a>
                </div>
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-center align-items-center gap-1 flex-wrap">
                                <div class="collapse"  id="collapseAction">
                                    <div class="card card-body d-flex gap-2 flex-row">
                                        <a
                                            href="{{ route('export.matchtgl', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="btn btn-info mb-3 text-white"
                                        >
                                            <i class="fa fa-file-excel"></i> Export Match Tanggal
                                        </a>


                                        <a
                                            href="{{ route('export.matchnilai', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="btn btn-dark mb-3 text-white"
                                        >
                                            <i class="fa fa-file-excel"></i> Export Match Nilai
                                        </a>

                                        <a
                                            href="{{ route('export.matchnup', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="btn btn-white mb-3 text-dark border border-dark"
                                        >
                                            <i class="fa fa-file-excel"></i> Export Match NUP
                                        </a>

                                        <a
                                            href="{{ route('export.match', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="btn btn-success mb-3"
                                        >
                                            <i class="fa fa-file-excel"></i> Export MATCH
                                        </a>


                                        <a
                                            href="{{ route('export.internal', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="btn btn-warning mb-3"
                                        >
                                            <i class="fa fa-file-excel"></i> Export Internal Only
                                        </a>
                                        <a
                                            href="{{ route('export.siman', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="btn btn-primary mb-3"
                                        >
                                            <i class="fa fa-file-excel"></i> Export SIMAN Only
                                        </a>

                                </div>
                    </div>
                </div>


                <div class="card-body">

                    <div class="row mb-3">
                        <div class="container">

                            <a class="btn btn-primary px-3 mb-3" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="false" aria-controls="collapseFilter">
                                Filter Data
                            </a>
                        </div>
                        <div class="collapse"  id="collapseFilter">
                            <div class="col-md-3 mb-3">
                                <select id="statusFilter" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="MATCH">MATCH</option>
                                    <option value="MATCH_NILAI">MATCH NILAI (Tanggal Tidak Sama)</option>
                                    <option value="MATCH_TGL">MATCH TGL (Nilai Tidak Sama)</option>
                                    <option value="MATCH_NUP">MATCH NUP (Tanggal & Nilai Tidak Sama)</option>
                                    <option value="INTERNAL_ONLY">INTERNAL ONLY</option>
                                    <option value="SIMAN_ONLY">SIMAN ONLY</option>
                                </select>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>Batch Internal</label>
                                    <select id="batchInternal" class="form-select">
                                        <option value="">Semua</option>
                                        @foreach($batchInternal as $b)
                                            <option value="{{ $b->batch }}">{{ $b->batch }} - {{ $b->label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Batch SIMAN</label>
                                    <select id="batchSiman" class="form-select">
                                        <option value="">Semua</option>
                                        @foreach($batchSiman as $id)
                                            <option value="{{ $id->id }}">{{ $id->id }} - {{ $id->label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>




                            <table id="reconciliationTable" class="table table-bordered table-striped " style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>NUP</th>
                                        <th>Merk/tipe Internal</th>
                                        <th>Merk/tipe SIMAN</th>
                                        <th>Tgl Internal</th>
                                        <th>Tgl SIMAN</th>
                                        <th>Nilai Internal</th>
                                        <th>Nilai SIMAN</th>
                                        <th>Selisih Nilai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        <div class="row">
                            <div class="col-md-3 d-flex flex-column justify-content-around">




                            </div>
                        </div>

                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Hapus Batch data</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form  action="{{ route('internal.destroyBatch') }}" method="post" class="d-inline">
            <div class="modal-body" id="modal-content">
                        @csrf
                        @method('DELETE')
                        <label for="number" class="form-label">Masukkan batch number</label>
                        <input type="number" name="batch" class="form-control">

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-shadow btn-danger" >Hapus</button>
                <button type="button" class="btn btn-shadow btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </form>
        </div>
    </div>
    </div>


     <!-- datatable Js -->
    <script src="{{ asset('/assets/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/responsive.bootstrap5.min.js') }}"></script>

    <script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        var genericExamples = document.querySelectorAll('[data-trigger]');
        for (i = 0; i < genericExamples.length; ++i) {
          var element = genericExamples[i];
          new Choices(element, {
            placeholderValue: 'This is a placeholder set in the config',
            searchPlaceholderValue: 'Cari kode barang'
          });
        }
    })
</script>
<script>
    const item = document.getElementById('itemSelect');
    const itemSearch = document.getElementById('itemSearch');

    item.addEventListener('change', function () {
        itemSearch.value = item.value; //  sync select → input
        newcs.draw();
    });
</script> --}}
<script>
    function getQueryParam(name) {
        return new URLSearchParams(window.location.search).get(name);
}

if (getQueryParam('status')) {
    document.getElementById('statusFilter').value = getQueryParam('status');
}


$(function () {

    let table = $('#reconciliationTable').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        pageLength: 25,

        scrollX: true,
        scrollY: '60vh',

        scrollCollapse: true,

        autoWidth: false,

        // fixedHeader: true,
        ajax: {
            url: "{{ route('compare.datatable') }}",
            data: function (d) {
            d.status = getQueryParam('status') ?? $('#statusFilter').val();
            d.batch_internal  = $('#batchInternal').val();
            d.batch_siman     = $('#batchSiman').val();
        }
        },
        columns: [
            { data: 'kode_barang' },
            { data: 'nama_barang' },
            { data: 'nup' },
            { data: 'merk' },
            {   data: 'merktipe',
                width: '220px',
                // render: data =>
                // data ? `<span title="${data}">${data}</span>` : '-'
            },
            { data: 'tgl_internal' },
            { data: 'tgl_siman' },
            { data: 'nilai_internal', className: 'text-end' },
            { data: 'nilai_siman', className: 'text-end' },
            { data: 'selisih_nilai', className: 'text-end fw-bold' },
            { data: 'compare_status', orderable: false, searchable: false },
        ],

        order: [[0, 'asc']],
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
        rowCallback: function (row, data) {
            if (data.compare_status.includes('MISMATCH')) {
                $(row).addClass('table-warning');
            }
        }
    });

    $('#batchInternal, #batchSiman, #statusFilter').change(function () {
        table.draw();
    });

    $('#statusFilter').change(function () {
        table.draw();
    });

});
</script>


@endsection
