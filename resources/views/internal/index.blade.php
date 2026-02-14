@extends('app')
@section('dependencies')
<link rel="stylesheet" href="{{ asset('/assets/asset/css/plugins/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/asset/css/plugins/responsive.bootstrap5.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Make action column sticky */
/* table.dataTable th.dt-action-col ,
table.dataTable td.dt-action-col{
    position: sticky;
    right: 0;
    background: #fff;
    z-index: 2;
    white-space: nowrap;
} */

/* Header should be above body */
/* table.dataTable th.dt-action-col {
    z-index: 3;
} */

/* Optional: shadow separator */
/* table.dataTable td.dt-action-col::before,
table.dataTable th.dt-action-col::before {
    content: "";
    position: absolute;
    left: -6px;
    top: 0;
    bottom: 0;
    width: 6px;
    background: linear-gradient(to left, rgba(0,0,0,.15), transparent);
} */

/* .dt-action-col {
    min-width: 180px;
    max-width: 180px;
}

.dataTables_scrollHead th.batch-col {
    padding-right: 10rem; 
}

.dataTables_scrollHead th.link_dokumentasi {
    padding-right: 10rem; 
}

.dataTables_scrollHead th.kode-satker-col {
    padding-right: 6rem; 
}

.dataTables_scrollHead th.merk-col {
    padding-right: 3rem; 
}
.dataTables_scrollHead th.tipe-col {
    padding-right: 3rem; 
}

.dataTables_scrollHead th.dt-action-col {
    padding-right: 11.5rem; 
} */

.choices__list--dropdown {
    z-index: 5 !important;
}

</style>
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
                <h3>Data internal</h3>
                {{-- laundry trans button --}}
                <div>
                    <a href="{{ route('internal.make') }}" class="btn btn-shadow btn-success">Tambah data Internal</a>

                    <a href="{{ route('internal.create') }}" class="btn btn-shadow btn-primary">Import data Internal</a>
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
                                        name="itemsSearch"
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
                        <div class="mb-2">
                            <label for="unitSearch" class="form-label fw-bold">
                                Search by Unit Kerja
                            </label>
                            <select
                                        class="form-control "
                                        data-unit
                                        name="unitSearch"
                                        id="unitSelect"
                                    >
                                        <option style="z-index: 4" value="" selected disabled>--Pilih Unit Kerja--</option>
                                        <option style="z-index: 4" value="">Semua</option>
                                        @foreach ($unitkerja as $keyunitkerja)

                                        <option style="z-index: 4" value="{{ $keyunitkerja->id }}">{{ $keyunitkerja->name }}</option>
                                        @endforeach
                                    </select>
                            <input
                                type="hidden"
                                id="unitSearch"
                                placeholder=""
                            >
                        </div>
                    </div>
                </div>

                {{-- filter by tgl --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="lokasiSearch" class="form-label fw-bold">
                                Search by Tanggal Perolehan
                            </label>
                        <div class="accordion " id="accordionTgl">
                            <div class="accordion-item">
                                <h5 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Pilih Rentang Tanggal Perolehan
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

                    <div class="col-md-4">
                        <div class="mb-2">
                            <label for="lokasiSearch" class="form-label fw-bold">
                                Search by Lokasi Ruang
                            </label>
                            <select
                                        class="form-control"
                                        data-lokasi
                                        name="lokasiSearch"
                                        id="lokasiSearch"
                                    >
                                        <option value="" selected disabled>--Pilih Lokasi Ruang--</option>
                                        <option value="">Semua</option>
                                        @foreach ($lokasiruang as $keylokasiruang)

                                        <option value="{{ $keylokasiruang->id }}">{{ $keylokasiruang->name }}</option>
                                        @endforeach
                                    </select>
                            <input
                                type="hidden"
                                id="lokasiSearch"
                                placeholder=""
                            >
                        </div>
                </div>

                <div class="table-responsive">
                    <table id="new-cons" class="table table-striped nowrap w-100 " >
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Satker</th>
                                <th>Kode Barang</th>
                                <th>NUP</th>
                                <th>Tanggal Perolehan</th>
                                <th>Nama Barang</th>
                                <th>Merk</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Nilai Aset</th>
                                <th>Nilai Penyusutan</th>
                                <th>Nilai Buku</th>
                                <th>Kondisi</th>
                                <th>Akun Neraca</th>
                                <th>Pembukuan</th>
                                <th>Unit Kerja</th>
                                <th>PenggunaRaw</th>
                                <th>Nama Pengguna</th>
                                <th>Alamat Pengguna</th>
                                <th>Lokasi Ruang</th>
                                <th>Status INVEN</th>
                                <th>Kondisi Setelah Inventarisasi</th>
                                <th>Link Dokumentasi</th>
                                <th>Link Kelengkapan LHI</th>
                                <th>Nomor BAHI (Berita Acara Hasil Inven)</th>
                                <th>Tanggal BAHI (Berita Acara Hasil Inven)</th>
                                <th>Batch</th>
                                {{-- <th>Aksi</th> --}}
                                
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
                        @foreach ($datainternal as $index => $data)
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
            <form  action="{{ route('internal.destroyBatch') }}" method="post" class="d-inline">
                <div class="modal-body" id="modal-content">
                            @csrf
                            @method('DELETE')

                            <label for="number" class="form-label">Masukkan batch number</label>
                            {{-- <input type="number" name="batch" class="form-control"> --}}
                            <select name="batch" id="" class="form-control">
                                <option value="" selected disabled>-- Pilih Batch --</option>
                                @foreach ($batchNumber as $keyBatch)
                                    <option value="{{$keyBatch->batch}}">{{$keyBatch->batch}} - {{$keyBatch->label}}</option>
                                @endforeach
                            </select>

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
        // data choices
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

            var UnitSelect = document.querySelectorAll('[data-unit]');
            for (i = 0; i < UnitSelect.length; ++i) {
            var unitelement = UnitSelect[i];
            new Choices(unitelement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari unit kerja',
                position: 'bottom'
            });
            }

            var LokasiSelect = document.querySelectorAll('[data-lokasi]');
            for (i = 0; i < LokasiSelect.length; ++i) {
            var lokasielement = LokasiSelect[i];
            new Choices(lokasielement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari lokasi ruang'
            });
            }

        })
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
        // search filter
        const item = document.getElementById('itemSelect');
        const itemSearch = document.getElementById('itemSearch');

        item.addEventListener('change', function () {
            itemSearch.value = item.value; //  sync select → input
            newcs.draw();
        });

        const unit = document.getElementById('unitSelect');
        const unitSearch = document.getElementById('unitSearch');

        unit.addEventListener('change', function () {
            unitSearch.value = unit.value; //  sync select → input
            newcs.draw();
        });

        const lokasi = document.getElementById('lokasiSearch');
        const lokasiSearch = document.getElementById('lokasiSearch');

        lokasi.addEventListener('change', function () {
            lokasiSearch.value = lokasi.value; //  sync select → input
            newcs.draw();
        });
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


    $('#new-cons_filter input').attr('id', 'newConsSearch');

    $('#nupSearch').on('keyup', function () {
        const val = this.value.trim();

        if (val === '') {
            // clear search
            newcs.column(3).search('').draw();
        } else {
            // exact match using regex
            newcs.column(3)
                .search('^' + val + '$', true, false)
                .draw();
        }
    });

    // redraw table when filter applied
    $('#itemSearch').on('change', function () {
    newcs.draw();
    });

    $('#unitSearch').on('change', function () {
    newcs.draw();
    });

    $('#tglFrom, #tglTo').on('change', function () {
    newcs.ajax.reload();
    });

    $('#lokasiSearch').on('change', function () {
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
    responsive: false,  // IMPORTANT


    ajax: {
        url: "{{ route('internal.datatable') }}",
        data: function (d) {
            d.itemSearch   = $('#itemSearch').val();
            d.unitSearch   = $('#unitSearch').val();
            d.lokasiSearch = $('#lokasiSearch').val();
            // d.nupSearch = $('#nupSearch').val();
            // d.bmnSearch    = $('#bmnSearch').val();

            //  DATE RANGE
            d.tglFrom = convertToISO($('#tglFrom').val());
            d.tglTo   = convertToISO($('#tglTo').val());
        }
    },

    columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, width: '3rem' },
        { data: 'kode_satker', name: 'satkers.kode_satker', orderable: false, width: '6rem' },
        { data: 'kode_barang', orderable: false, width: '6rem' },
        { data: 'nup', width: '5rem' },
        { data: 'tgl_perolehan', width: '8rem' },
        { data: 'nama_barang', orderable: false, width: '10rem' },
        // { data: 'merkRaw' },
        { data: 'merk', width: '6rem' },
        { data: 'tipe', width: '6rem' },
        { data: 'jumlah', width: '5rem' },
        { data: 'nilai_aset', name: 'nilai_aset', width: '8rem' },
        { data: 'nilai_penyusutan', name: 'nilai_penyusutan', width: '8rem' },
        { data: 'nilai_buku', name: 'nilai_buku', width: '8rem' },
        { data: 'kondisi', width: '4rem' },
        { data: 'akun_neraca', width: '6rem' },
        { data: 'pembukuan', width: '6rem' },
        { data: 'unit_kerja_id', orderable: false, width: '8rem' },
        { data: 'penggunaRaw', width: '8rem' },
        { data: 'nama_pengguna', width: '8rem' },
        { data: 'alamat_pengguna', width: '10rem' },
        { data: 'lokasi_id', width: '8rem' },
        { data: 'status_inven', width: '6rem' },
        { data: 'update_kondisi', width: '10rem' },
        { data: 'link_dokumentasi', width: '12rem' },
        { data: 'link_lhi', width: '12rem' },
        { data: 'no_bahi', width: '8rem' },
        { data: 'tgl_bahi', width: '8rem' },
        {
                data: 'batch',
                name: 'batch',
                className: 'batch-col',
                orderable: false,
                width: '6rem',
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
        // { data: 'action',
        //     name: 'action',
        //     orderable: false,
        //     searchable: false,
        //     // className: 'dt-action-col'
        // },

    ]
});
// table.on('draw.dt', function () {
//     table.columns.adjust();
// });



    </script>


@endsection
