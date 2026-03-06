@extends('app')
@section('title', $title)
@section('dependencies')
<link rel="stylesheet" href="{{ asset('/assets/dist/assets/css/plugins/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/dist/assets/css/plugins/responsive.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/flatpickr/dist/flatpickr.min.css') }}">

@endsection
@section('content')
    <div class="pc-content overflow-x-hidden">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item" aria-current="page"><a href="#">Data Invalid</a></li>
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
                <h3>Data invalid</h3>
                {{-- laundry trans button --}}
                <div>
                    {{-- <a href="{{ route('invalid.create') }}" class="btn btn-shadow btn-primary">Import data invalid</a> --}}
                    <button
                    type="button"
                    class="btn btn-shadow btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteModal"
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
                        <div class="mb-2">
                            <label for="unitSearch" class="form-label fw-bold">
                                Search by Unit Kerja
                            </label>
                            <select
                                        class="form-control"
                                        data-unit
                                        name="unitSearch"
                                        id="unitSelect"
                                    >
                                        <option value="" selected disabled>--Pilih Unit Kerja--</option>
                                        <option value="">Semua</option>
                                        @foreach ($unitkerja as $keyunitkerja)

                                        <option value="{{ $keyunitkerja->id }}">{{ $keyunitkerja->name }}</option>
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

                <div class="">
                    <table id="new-cons" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Satker</th>
                                <th>Kode Barang</th>
                                <th>NUP</th>
                                <th>Tanggal Perolehan</th>
                                <th>Nama Barang</th>
                                <th>Merk/Tipe</th>
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
                                <th>Nama Pengguna</th>
                                <th>Lokasi Ruang</th>
                                <th>Status INVEN</th>
                                <th>Kondisi Setelah Inventarisasi</th>
                                {{-- <th>Update Lokasi Ruang</th> --}}
                                <th>Link Dokumentasi</th>
                                <th>Link Kelengkapan LHI</th>
                                <th>Nomor BAHI (Berita Acara Hasil Inven)</th>
                                <th>Tanggal BAHI (Berita Acara Hasil Inven)</th>
                                <th>Batch</th>
                                <th>Alasan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <div class="row">
                        <div class="col-md-6 d-flex flex-column justify-content-around">
                            <a
                                    href="{{ route('export.invalid') }}"
                                    class="btn btn-warning mb-3"
                                >
                                    <i class="fa fa-file-excel"></i> Export Invalid Data
                                </a>
                        </div>
                    </div>

                </div>
              </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" >
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit data</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="editContent">

                </div>

            </div>
        </div>
    </div>

    {{-- delete modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteModalLabel">Hapus Batch data</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form  action="{{ route('invalid.destroyBatch') }}" method="post" class="d-inline">
                <div class="modal-body" id="modal-content">
                            @csrf
                            @method('DELETE')
                            <label for="number" class="form-label">Masukkan batch number</label>
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
    <script src="{{ asset('/assets/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/responsive.bootstrap5.min.js') }}"></script>

    <script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ asset('/assets/flatpickr/dist/flatpickr.min.js') }}"></script>

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

        var modalBarang = document.querySelectorAll('[data-barang]');
        for (i = 0; i < modalBarang.length; ++i) {
          var elementBarang = modalBarang[i];
          new Choices(elementBarang, {
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


</script>

<script>
    function openEditModal(button) {
        const row = JSON.parse(button.dataset.row);
        const route =  @json(route('invalid.update', ':id'));
        const editModalHtml = `
                <form  action="${route.replace(':id', row.id)}" method="post" class="d-inline">
                <div class="modal-body" id="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="container mb-3">
                        <h3>Alasan : </h3><br>
                        <h4>${row.description}</h4>
                    </div>
                    <hr>
                    <div class="row mb-3 d-flex justify-content-center">
                        <div class="col-md-10 d-flex flex-column justify-content-end">
                            <div class="mb-3" >
                                <label for="satker" class="form-label">Pilih kode satker</label>
                                <select class="form-control" name="satker_id" id="satker">
                                    <option value="" disabled selected>-- Pilih kode satker --</option>
                                    @foreach ($satker as $keysatker)

                                    <option value="{{$keysatker->id}}" ${row.satker_id == {{$keysatker->id}} ? 'selected' : ''}>{{$keysatker->kode_satker}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="barang" class="form-label">Pilih kode barang</label>
                                <select
                                    class="form-control"
                                    data-barang
                                    name="barang_id"
                                    id="barangSelect"
                                    >
                                    <option value="" selected disabled>--Pilih Kode Barang--</option>
                                    @foreach ($barang as $keybarang)

                                        <option value="{{ $keybarang->id }}" ${row.barang_id == {{$keybarang->id}} ? 'selected' : ''}>{{ $keybarang->kode_barang }} - {{ $keybarang->nama_barang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nup" class="form-label"> NUP</label>
                                <input type="number" name="nup" id="nup" class="form-control">
                                <small>nup saat ini: ${row.nup ? row.nup : ''}</small>

                            </div>
                            <div class="mb-3">
                                <label for="tgl_perolehan" class="form-label"> Tanggal perolehan</label>
                                <input type="date" name="tgl_perolehan" id="tgl_perolehan" class="form-control" value='${row.tgl_perolehan ? row.tgl_perolehan : ''}'>

                            </div>
                            <div class="mb-3">
                                <label for="merk" class="form-label"> Merk/tipe</label>
                                <input type="text" name="merk" id="merk" class="form-control" value='${row.merkRaw ? row.merkRaw : ''}'>

                            </div>
                            <div class="mb-3">
                                <label for="jumlah" class="form-label"> Jumlah</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" value='${row.jumlah ? row.jumlah : ''}'>

                            </div>
                            <div class="mb-3">
                                <label for="nilai_aset" class="form-label"> Nilai aset</label>
                                <input type="number" name="nilai_aset" id="nilai_aset" class="form-control" value='${row.nilai_aset ? row.nilai_aset : ''}'>

                            </div>
                            <div class="mb-3">
                                <label for="nilai_penyusutan" class="form-label"> Nilai penyusutan</label>
                                <input type="number" name="nilai_penyusutan" id="nilai_penyusutan" class="form-control" value='${row.nilai_penyusutan ? row.nilai_penyusutan : ''}'>

                            </div>
                            <div class="mb-3">
                                <label for="nilai_buku" class="form-label"> Nilai buku</label>
                                <input type="number" name="nilai_buku" id="nilai_buku" class="form-control" value='${row.nilai_buku ? row.nilai_buku : ''}'>

                            </div>
                            <div class="mb-3">
                                <label for="kondisi" class="form-label">Kondisi</label>
                                <select name="kondisi" id="kondisi" class="form-control">
                                    <option value="" disabled selected>-- Pilih Kondisi --</option>
                                    <option value="B" ${row.kondisi == 'B' ? 'selected' : ''}>B</option>
                                    <option value="RB" ${row.kondisi == 'RB' ? 'selected' : ''}>RB</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="akun_neraca" class="form-label">Akun Neraca</label>
                                <input type="text" name="akun_neraca" id="akun_neraca" class="form-control" value='${row.akun_neraca ? row.akun_neraca : ''}'>

                            </div>
                            <div class="mb-3">
                                <label for="pembukuan" class="form-label">Pembukuan</label>
                                <input type="text" name="pembukuan" id="pembukuan" class="form-control" value='${row.pembukuan ? row.pembukuan : ''}'>

                            </div>
                            <div class="mb-3">
                                <label for="unit_kerja" class="form-label">Unit Kerja</label>
                                <input type="text" name="unit_kerja" id="unit_kerja" class="form-control" value='${row.unit_kerja ? row.unit_kerja : ''}'>

                            </div>
                            <div class="mb-3">
                                <label for="pengguna" class="form-label">Pengguna</label>
                                <input type="text" name="pengguna" id="pengguna" class="form-control" value='${row.pengguna ? row.pengguna : ''}'>

                            </div>
                            <div class="mb-3">
                                <label for="status_inven" class="form-label">Status Inven</label>
                                <select name="status_inven" id="status_inven" class="form-control">
                                    <option value="SUDAH" ${row.status_inven == 'SUDAH' ? 'selected' : ''}>SUDAH</option>
                                    <option value="" ${row.status_inven == '' ? 'selected' : ''}>BELUM</option>
                                </select>

                            </div>
                            <div class="mb-3">
                                <label for="update_kondisi" class="form-label">Update Kondisi</label>
                                <select class="form-control" name="update_kondisi" id="update_kondisi">
                                    <option value="" disabled selected>-- Pilih Kondisi --</option>
                                    <option value="B" ${row.update_kondisi == 'B' ? 'selected' : ''}>B</option>
                                    <option value="RB" ${row.update_kondisi == 'RB' ? 'selected' : ''}>RB</option>
                                </select>

                            </div>
                            <div class="mb-4">
                                <label for="link_dokumentasi" class="form-label">Link Dokumentasi</label>
                                <input type="text" name="link_dokumentasi" id="link_dokumentasi" class="form-control" value='${row.link_dokumentasi ? row.link_dokumentasi : ''}'>

                            </div>
                            <div class="mb-4">
                                <label for="link_lhi" class="form-label">Link Kelengkapan LHI</label>
                                <input type="text" name="link_lhi" id="link_lhi" class="form-control" value='${row.link_lhi ? row.link_lhi : ''}'>

                            </div>
                            <div class="mb-4">
                                <label for="no_bahi" class="form-label">Nomor BAHI (Berita Acara Hasil Inven)</label>
                                <input type="text" name="no_bahi" id="no_bahi" class="form-control" value='${row.no_bahi ? row.no_bahi : ''}'>

                            </div>
                            <div class="mb-4">
                                <label for="tgl_bahi" class="form-label">Tanggal BAHI (Berita Acara Hasil Inven)</label>
                                <input type="date" name="tgl_bahi" id="tgl_bahi" class="form-control" value='${row.tgl_bahi ? row.tgl_bahi : ''}'>

                            </div>

                            <input type="hidden" name="batch" id="batch" value="${row.batch}">
                            <input type="hidden" name="label" id="label" value="${row.label}">

                        </div>

                    </div>
                    {{-- <label for="number" class="form-label">Masukkan batch number</label>
                    <input type="number" name="batch" class="form-control"> --}}

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-shadow btn-danger" >Edit</button>
                    <button type="button" class="btn btn-shadow btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </form>
        `
        document.getElementById('editContent').innerHTML = editModalHtml;

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

    $('#itemSearch').on('change', function () {
    newcs.draw();
    });

    $('#unitSearch').on('change', function () {
    newcs.draw();
    });

    $('#tglFrom, #tglTo').on('change', function () {
    newcs.ajax.reload();
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
    scrollY: '70vh',

    scrollCollapse: true,

    autoWidth: false,   // IMPORTANT

    ajax: {
        url: "{{ route('invalid.datatable') }}",
        data: function (d) {
            d.itemSearch   = $('#itemSearch').val();
            d.unitSearch   = $('#unitSearch').val();
            // d.nupSearch = $('#nupSearch').val();
            // d.bmnSearch    = $('#bmnSearch').val();

            //  DATE RANGE
            d.tglFrom = convertToISO($('#tglFrom').val());
            d.tglTo   = convertToISO($('#tglTo').val());
        }
    },

    columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'kode_satker', name: 'satkers.kode_satker',orderable: false, },
        { data: 'kode_barang',orderable: false, },
        { data: 'nup' },
        { data: 'tgl_perolehan' },
        { data: 'nama_barang' },
        { data: 'merkRaw' },
        { data: 'merk' },
        { data: 'tipe' },
        { data: 'jumlah' },
        { data: 'nilai_aset', name: 'nilai_aset' },
        { data: 'nilai_penyusutan', name: 'nilai_penyusutan' },
        { data: 'nilai_buku', name: 'nilai_buku' },
        { data: 'kondisi' },
        { data: 'akun_neraca' },
        { data: 'pembukuan' },
        { data: 'unit_kerja_id',orderable: false, },
        { data: 'pengguna' },
        { data: 'lokasi_ruang' },
        { data: 'status_inven' },
        { data: 'update_kondisi' },
        { data: 'link_dokumentasi' },
        { data: 'link_lhi' },
        { data: 'no_bahi' },
        { data: 'tgl_bahi' },
        {
                data: 'batch',
                name: 'batch', orderable: false,
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
        { data: 'description' },
        { data: 'action', orderable: false, searchable: false }

    ]
});

    $(document).on('click', '.btn-delete', function () {
    const id = $(this).data('id');
    const name = $(this).data('name');

    Swal.fire({
        title: 'Hapus data?',
        text: `Kode Register: ${name}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/invalid/${id}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function () {
                    Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                    $('#new-cons').DataTable().ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire('Gagal', 'Data gagal dihapus', 'error');
                }
            });
        }
    });
});


    </script>


@endsection
