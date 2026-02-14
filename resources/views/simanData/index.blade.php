@extends('app')
@section('dependencies')
<link rel="stylesheet" href="{{ asset('/assets/asset/css/plugins/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/asset/css/plugins/responsive.bootstrap5.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@endsection
@section('content')
    <div class="pc-content overflow-x-hidden">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item" aria-current="page"><a href="#">Data Siman</a></li>
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
                <h3>Data Siman</h3>
                {{-- laundry trans button --}}
                <div>
                    <a href="{{ route('siman.create') }}" class="btn btn-shadow btn-primary">Import data SIMAN</a>
                    <button
                    type="button"
                    class="btn btn-shadow btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#exampleModal"
                    >
                    Hapus batch data
                </button>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-2">
                            <label for="numberSearch" class="form-label fw-bold">
                                Search by NUP
                            </label>
                            <input
                                type="text"
                                id="nupSearch"
                                class="form-control"
                                placeholder="Ketik Nomor NUP..."
                            >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label for="itemsSearch" class="form-label fw-bold">
                                Search by Kode Barang
                            </label>
                            <select
                                        class="form-control"
                                        data-trigger
                                        name=""
                                        id="itemSelect"
                                    >
                                        <option value="" selected disabled>--Pilih Kode Barang--</option>
                                        <option value="">Semua</option>
                                        @foreach ($barang as $keybarang)

                                        <option value="{{ $keybarang->kode_barang }}">{{ $keybarang->kode_barang }} - {{ $keybarang->nama_barang }}</option>
                                        @endforeach
                                    </select>
                            <input
                                type="hidden"
                                id="itemSearch"
                                placeholder=""
                            >
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="" class="form-label"></label>
                        <div class="accordion " id="accordionTgl">
                            <div class="accordion-item">
                                <h5 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Search by Tanggal Perolehan
                                </button>
                                </h5>
                                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionTgl">
                                    <div class="accordion-body">
                                        <div class="row">

                                            <div class="col-md-6">
                                                <label for="tglFrom" class="form-label">Dari tanggal</label>
                                                <input type="text" id="tglFrom" class="form-control mb-3" autocomplete="off">
                                                <button class="btn btn-sm btn-secondary" onclick="clearTgl()">Clear Filter</button>

                                            </div>
                                            <div class="col-md-6">
                                                <label for="tglTo" class="form-label">Sampai tanggal</label>
                                                <input type="text" id="tglTo" class="form-control mb-3" autocomplete="off">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- <img src="{{ $imageUrl }}" alt="Downloaded Image" style="max-width: 300px;"> --}}
                <div class="">
                    <table id="new-cons" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>BMN</th>
                                <th>Kode Satker</th>
                                <th>Nama Satker</th>
                                <th>Kode Barang</th>
                                <th>NUP</th>
                                <th>Nama Barang</th>
                                <th>Merk</th>
                                <th>Tipe</th>
                                <th>Kondisi</th>
                                <th>No Dokumen</th>
                                <th>No BPKP</th>
                                <th>No Polisi</th>
                                <th>No Sertifikat</th>
                                <th>Tgl Perolehan</th>
                                <th>Nilai Perolehan</th>
                                <th>Nilai Penyusutan</th>
                                <th>Nilai Buku</th>
                                <th>Kode Register</th>
                                <th>Lokasi Ruang</th>
                                <th>Kondisi Setelah Inventarisasi</th>
                                <th>Update Lokasi Ruang</th>
                                <th>Nama Pengguna</th>
                                <th>Link Dokumentasi</th>
                                <th>Opname</th>
                                <th>Batch</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                  {{-- <table id="new-cons" class=" table table-striped table-hover" style="width: 100%">
                    <thead>
                        <th>No.</th>
                        <th>Jenis BMN</th>
                        <th>Kode Satker</th>
                        <th>Nama Satker</th>
                        <th>Kode Barang</th>
                        <th>NUP</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>Tipe</th>
                        <th>Kondisi</th>
                        <th>No Dokumen</th>
                        <th>No BPKP</th>
                        <th>No Polisi</th>
                        <th>No Sertifikat</th>
                        <th>Tanggal Perolehan</th>
                        <th>Nilai Perolehan</th>
                        <th>Nilai Penyusutan</th>
                        <th>Nilai Buku</th>
                        <th>Kode Register</th>
                        <th>Lokasi Ruang</th>
                        <th>Nama Pengguna </th>
                        <th>Link Foto Dokumentasi</th>
                        <th>Opname (terbaru)</th>
                        <th>Import Batch</th>
                    </thead>
                    <tbody>
                        @foreach ($dataSiman as $index => $data)
                        <tr>
                        <td>{{$index+=1}}</td>
                        <td>{{ $data->bmns->name }}</td>
                        <td>{{ $data->satkers->kode_satker }}</td>
                        <td>{{ $data->satkers->nama_satker }}</td>
                        <td>{{ $data->kode_barang }}</td>
                        <td>{{ $data->nup }}</td>
                        <td>{{ $data->nama_barang }}</td>
                        <td>{{ $data->merk }}</td>
                        <td>{{ $data->tipe }}</td>
                        <td>{{ $data->kondisi }}</td>
                        <td>{{ $data->no_dokumen }}</td>
                        <td>{{ $data->no_BPKP }}</td>
                        <td>{{ $data->no_polisi }}</td>
                        <td>{{ $data->no_sertifikat }}</td>
                        <td>{{ $data->tgl_perolehan }}</td>
                        <td data-value="{{ $data->nilai_perolehan }}">{{ $data->nilai_perolehan_fmt }}</td>
                        <td data-value="{{ $data->nilai_penyusutan }}">{{ $data->nilai_penyusutan_fmt }}</td>
                        <td data-value='{{$data->nilai_buku}}'>{{ $data->nilai_buku_fmt }}</td>
                        <td>{{ $data->kode_register }}</td>
                        <td>{{ $data->lokasi_ruang }}</td>
                        <td>{{ $data->nama_pengguna }}</td>
                        <td>{{ $data->link_dokumentasi }}</td>
                        <td>{{ $data->opname }}</td>
                        <td>{{ $data->import_batch_id }}</td>
                        </tr>
                        @endforeach

                    </tbody>
                  </table> --}}
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
        <form  action="{{ route('siman.destroyBatch') }}" method="post" class="d-inline">
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
    <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/responsive.bootstrap5.min.js') }}"></script>

    <script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var genericExamples = document.querySelectorAll('[data-trigger]');
        for (i = 0; i < genericExamples.length; ++i) {
          var element = genericExamples[i];
          new Choices(element, {
            placeholderValue: 'This is a placeholder set in the config',
            searchPlaceholderValue: 'Cari kode barang',
            position: 'bottom'
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
</script>

    <script>
        // flatpickr
        function convertToISO(date) {
            if (!date) return null;
            let [y, m, d] = date.split('/');
            return `${y}-${m}-${d}`;
        }

        flatpickr("#tglFrom, #tglTo", {
            dateFormat: "Y/m/d",
            allowInput: true
        });

        function clearTgl()  {
            let from = document.getElementById('tglFrom').value = null;
            let to = document.getElementById('tglTo').value = null;
            newcs.draw();
        }

    </script>

    <script>
      // [ Configuration Option ]
    //   $('#res-config').DataTable({
    //     responsive: true
    //   });

      // [ New Constructor ]
//     function initNewConsTable() {
//     var isMobile = window.innerWidth < 768;

//     return $('#new-cons').DataTable({
//         destroy: true,      //  allow re-init on resize
//         autoWidth: false,

//         scrollX: !isMobile, //  desktop = scroll, mobile = no scroll

//         responsive: isMobile ? {
//         details: {
//             type: 'column',
//             target: 'tr'
//         }
//         } : false
//     });
//     }

//     //  First Init
//     var newcs = initNewConsTable();

// //  Re-init on screen resize (rotate phone, resize browser)
//     $(window).on('resize', function () {
//     $('#new-cons').DataTable().destroy();
//     newcs = initNewConsTable();
//     });

    $('#tglFrom, #tglTo').on('change', function () {
    newcs.ajax.reload();
    });

    $('#new-cons_filter input').attr('id', 'newConsSearch');

    $('#nupSearch').on('keyup', function () {
        const val = this.value.trim();

        if (val === '') {
            // clear search
            newcs.column(5).search('').draw();
        } else {
            // exact match using regex
            newcs.column(5)
                .search('^' + val + '$', true, false)
                .draw();
        }
    });


    $('#itemSearch').on('change', function () {
    newcs.draw();
    });

    // $('#nupSearch').on('keyup', function () {
    //     newcs.draw();
    // });

    let newcs = $('#new-cons').DataTable({
    processing: true,
    serverSide: true,
    deferRender: true,
    pageLength: 25,

    scrollX: true,
    scrollY: '60vh',

    scrollCollapse: true,

    autoWidth: false,   // IMPORTANT

    ajax: {
        url: "{{ route('siman.datatable') }}",
        data: function (d) {
            d.itemSearch   = $('#itemSearch').val();
            // d.nupSearch = $('#nupSearch').val();
            // d.bmnSearch    = $('#bmnSearch').val();

            //  DATE RANGE
            d.tglFrom = convertToISO($('#tglFrom').val());
            d.tglTo   = convertToISO($('#tglTo').val());
        }
    },

    columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'bmn', name: 'bmns.name', orderable: false },
        { data: 'kode_satker', name: 'satkers.kode_satker', orderable: false },
        { data: 'nama_satker', name: 'satkers.nama_satker', orderable: false },
        { data: 'kode_barang', orderable: false },
        { data: 'nup' },
        { data: 'nama_barang', orderable: false },
        { data: 'merk' },
        { data: 'tipe' },
        { data: 'kondisi' },
        { data: 'no_dokumen' },
        { data: 'no_BPKP' },
        { data: 'no_polisi' },
        { data: 'no_sertifikat' },
        { data: 'tgl_perolehan' },
        { data: 'nilai_perolehan', name: 'nilai_perolehan' },
        { data: 'nilai_penyusutan', name: 'nilai_penyusutan' },
        { data: 'nilai_buku', name: 'nilai_buku' },
        { data: 'kode_register' },
        { data: 'lokasi_ruang' },
        { data: 'update_kondisi' },
        { data: 'update_lokasi_ruang' },
        { data: 'nama_pengguna' },
        { data: 'link_dokumentasi' },
        { data: 'opname' },
        {
                data: 'import_batch_id',
                name: 'import_batch_id', orderable: false,
                render: function (data, type, row) {
                    if (!data || data === '-') {
                        return '<span class="text-muted">-</span>';
                    }

                    return `
                        <h5><span class="badge bg-primary">
                            ${data}
                        </span></h5>
                    `;
                }
            },

    ]
});

    </script>


@endsection
