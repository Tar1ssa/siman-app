@extends('app')
@section('title', $title)
@section('dependencies')
<link rel="stylesheet" href="{{ asset('/assets/dist/assets/css/plugins/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/dist/assets/css/plugins/responsive.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/flatpickr/dist/flatpickr.min.css') }}">
<style>
    /* Make action column sticky */
table.dataTable th.dt-action-col ,
table.dataTable td.dt-action-col{
    position: sticky;
    right: 0;
    background: #fff;
    z-index: 2;
    white-space: nowrap;
}

/* Make checkbox column sticky */
table.dataTable th.checkbox-col,
table.dataTable td.checkbox-col {
    position: sticky;
    left: 0;
    background: #fff;
    z-index: 2;
    white-space: nowrap;
}

/* Header should be above body */
table.dataTable th.dt-action-col {
    /* z-index: 3; */
}



/* Optional: shadow separator */
table.dataTable td.dt-action-col::before,
table.dataTable th.dt-action-col::before {
    content: "";
    position: absolute;
    left: -6px;
    top: 0;
    bottom: 0;
    width: 6px;
    background: linear-gradient(to left, rgba(0,0,0,.15), transparent);
}

/* Shadow separator for checkbox column */
table.dataTable td.checkbox-col::after,
table.dataTable th.checkbox-col::after {
    content: "";
    position: absolute;
    right: -6px;
    top: 0;
    bottom: 0;
    width: 6px;
    background: linear-gradient(to right, rgba(0,0,0,.15), transparent);
}

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

.row-checkbox:hover {
    cursor: pointer;

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
                    <li class="breadcrumb-item"><a href="{{route('internal.index')}}">Data Internal</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="#">Generate Dokumen PSP</a></li>
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
              <div class="card-header">
                <div class="row d-flex flex-column justify-content-between ">
               
                        <h3>Generate Dokumen PSP</h3>
                        <button
                            type="button"
                            onclick="generatePSP()"
                            class="btn btn-shadow btn-primary"
                            >
                            Generate Dokumen PSP
                        </button>

                </div>

                
                
              </div>
              <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-2">
                            <label for="nupSearch" class="form-label fw-bold">
                                Search by NUP Range
                            </label>
                            <div class="row g-1">
                                <div class="col-6">
                                    <input
                                        type="number"
                                        id="nupMin"
                                        class="form-control form-control-sm"
                                        placeholder="Min NUP"
                                        min="1"
                                    >
                                </div>
                                <div class="col-6">
                                    <input
                                        type="number"
                                        id="nupMax"
                                        class="form-control form-control-sm"
                                        placeholder="Max NUP"
                                        min="1"
                                    >
                                </div>
                            </div>
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

                    <div class="col-md-4">
                        <div class="mb-2">
                            <label for="statusSearch" class="form-label fw-bold">
                                Search by Status
                            </label>
                            <select
                                class="form-control"
                                name="statusSearch"
                                id="statusSearch"
                            >
                                <option value="" selected>Semua</option>
                                <option value="draft">Draft</option>
                                <option value="unlocked">Dibuka</option>
                                <option value="locked">Terkunci</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="identitasSearch" class="form-label fw-bold">
                                Search by Identitas
                            </label>
                        <div class="accordion " id="accordionIdentitas">
                            <div class="accordion-item">
                                <h5 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIdentitas" aria-expanded="false" aria-controls="collapseIdentitas">
                                    Pilih Identitas
                                </button>
                                </h5>
                                <div id="collapseIdentitas" class="accordion-collapse collapse" data-bs-parent="#accordionIdentitas">
                                    <div class="accordion-body">
                                        <div class="row d-flex justify-content-between flex-column">
                                            <div class="col-md-12">
                                            <div class="mb-2">
                                                <label for="kategoriIdentitasSearch" class="form-label">
                                                    Pilih Kategori Identitas
                                                </label>
                                                <select
                                                    class="form-control"
                                                    data-kategori-identitas
                                                    name="kategoriIdentitasSearch"
                                                    id="kategoriIdentitasSearch"
                                                >
                                                    <option value="" selected disabled>--Pilih Kategori Identitas--</option>
                                                    <option value="">Semua</option>
                                                    @foreach ($identitasKategori as $kategori)
                                                    <option value="{{ $kategori->id }}">{{ $kategori->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-2">
                                                <label for="identitasSearch" class="form-label">
                                                    Pilih Identitas
                                                </label>
                                                <select
                                                    class="form-control"
                                                    data-identitas
                                                    name="identitasSearch"
                                                    id="identitasSearch"
                                                    disabled
                                                >
                                                    <option value="" selected disabled>--Pilih Identitas--</option>
                                                    <option value="">Semua</option>
                                                </select>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- filter by identitas --}}
                <div class="row mb-3">

                </div>

                {{-- Lampiran --}}
                {{-- <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="lampiran" class="form-label fw-bold">Lampiran</label>
                        <textarea id="lampiran" name="lampiran" class="form-control" rows="3" placeholder="Masukkan lampiran..."></textarea>
                    </div>
                </div> --}}

                {{-- <div class="table-responsive"> --}}
                    <table id="new-cons" class="table table-striped  " style="width:100%">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="form-check-input " id="selectAll"></th>
                                <th>No</th>
                                <th>Kode Satker</th>
                                <th>Kode Barang</th>
                                <th>NUP</th>
                                <th>Tanggal Perolehan</th>
                                <th>Nama Barang</th>
                                <th>Foto Barang</th>
                                <th>Identitas</th>
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
                                <th>Pengguna (CSV)</th>
                                <th>Nama Pengguna</th>
                                <th>Lokasi Ruang</th>
                                <th>Status INVEN</th>
                                <th>Kondisi Setelah Inventarisasi</th>
                                <th>Link Dokumentasi</th>
                                <th>Link Kelengkapan LHI</th>
                                <th>Nomor BAHI (Berita Acara Hasil Inven)</th>
                                <th>Tanggal BAHI (Berita Acara Hasil Inven)</th>
                                <th>Status</th>
                                <th>Batch</th>

                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                  
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
    <script src="{{ asset('/assets/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/responsive.bootstrap5.min.js') }}"></script>

    <script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ asset('/assets/flatpickr/dist/flatpickr.min.js') }}"></script>



    <script>
        // data choices
        document.addEventListener('DOMContentLoaded', function () {
            var genericExamples = document.querySelectorAll('[data-trigger]');
            for (i = 0; i < genericExamples.length; ++i) {
            var element = genericExamples[i];
            new Choices(element, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari kode barang',
                position: 'bottom',
                shouldSort: false
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

            var KategoriIdentitasSelect = document.querySelectorAll('[data-kategori-identitas]');
            for (i = 0; i < KategoriIdentitasSelect.length; ++i) {
            var kategoriIdentitaselement = KategoriIdentitasSelect[i];
            new Choices(kategoriIdentitaselement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari kategori identitas',
                position: 'bottom'
            });
            }

            var IdentitasSelect = document.querySelectorAll('[data-identitas]');
            for (i = 0; i < IdentitasSelect.length; ++i) {
            var identitaselement = IdentitasSelect[i];
            var choicesInstance = new Choices(identitaselement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari identitas',
                position: 'bottom'
            });
            // Store the Choices instance for later use
            $(identitaselement).data('choices', choicesInstance);
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



    $('#new-cons_filter input').attr('id', 'newConsSearch');

    // NUP range filter
    $('#nupMin, #nupMax').on('input', function () {
        newcs.draw();
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

    // Identitas filter logic
    $('#kategoriIdentitasSearch').on('change', function () {
        const kategoriId = $(this).val();
        const identitasSelect = $('#identitasSearch');
        const identitasChoices = identitasSelect.data('choices');

        if (kategoriId) {
            // Fetch identitas for selected kategori
            $.ajax({
                url: '{{ route("internal.kategoriIdentitas", ":id") }}'.replace(':id', kategoriId),
                type: 'GET',
                success: function (data) {
                    // Clear existing options except the first two (placeholder and "Semua")
                    identitasChoices.clearChoices();

                    // Add "Semua" option
                    identitasChoices.setChoices([{
                        value: '',
                        label: 'Semua',
                        selected: false
                    }], 'value', 'label', false);

                    // Add fetched identitas options
                    const identitasOptions = data.map(function (identitas) {
                        return {
                            value: identitas.id,
                            label: identitas.name,
                            selected: false
                        };
                    });
                    identitasChoices.setChoices(identitasOptions, 'value', 'label', false);

                    // Enable the select
                    identitasSelect.prop('disabled', false);
                    identitasChoices.enable();
                }
            });
        } else {
            // Reset to disabled state
            identitasChoices.clearChoices();
            identitasChoices.setChoices([{
                value: '',
                label: '--Pilih Identitas--',
                selected: true,
                disabled: true
            }], 'value', 'label', false);
            identitasSelect.prop('disabled', true);
            identitasChoices.disable();
        }

        // Reset identitas selection and redraw table
        identitasChoices.setChoiceByValue('');
        newcs.draw();
    });

    $('#identitasSearch').on('change', function () {
        newcs.draw();
    });

    $('#statusSearch').on('change', function () {
        newcs.draw();
    });

    // $('#nupSearch').on('keyup', function () {
    //     newcs.draw();
    // });

    let selectedRows = []; // Array to store selected row IDs

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

    drawCallback: function() {
        $('.row-checkbox').each(function() {
            const id = $(this).val();
            $(this).prop('checked', selectedRows.includes(id));
        });
        // Update selectAll based on visible checkboxes
        const visibleCheckboxes = $('.row-checkbox');
        const checkedVisible = visibleCheckboxes.filter(':checked');
        $('#selectAll').prop('checked', visibleCheckboxes.length > 0 && checkedVisible.length === visibleCheckboxes.length);
    },


    ajax: {
        // url: '{{ route("internal.locked.datatable") }}',
        url: "{{ route('internal.datatable') }}",
        data: function (d) {
            d.itemSearch   = $('#itemSearch').val();
            d.unitSearch   = $('#unitSearch').val();
            d.lokasiSearch = $('#lokasiSearch').val();
            d.statusSearch = $('#statusSearch').val();
            d.kategoriIdentitasSearch = $('#kategoriIdentitasSearch').val();
            d.identitasSearch = $('#identitasSearch').val();
            d.nupMin = $('#nupMin').val();
            d.nupMax = $('#nupMax').val();
            // d.bmnSearch    = $('#bmnSearch').val();

            //  DATE RANGE
            d.tglFrom = convertToISO($('#tglFrom').val());
            d.tglTo   = convertToISO($('#tglTo').val());
        }
    },

    columns: [
        { data: null, orderable: false, searchable: false, className: 'checkbox-col', render: function(data, type, row) { 
            return '<input type="checkbox" class="row-checkbox form-check-input" value="' + row.id + '">'; 
        } },
        { data: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'kode_satker', name: 'satkers.kode_satker', orderable: false,  },
        { data: 'kode_barang', orderable: false },
        { data: 'nup' },
        { data: 'tgl_perolehan' },
        { data: 'nama_barang', orderable: false },
        { data: 'foto_barang', orderable: false },
        { data: 'identitas', orderable: false },
        // { data: 'merkRaw' },
        { data: 'merk', },
        { data: 'tipe',  },
        { data: 'jumlah' },
        { data: 'nilai_aset', name: 'nilai_aset' },
        { data: 'nilai_penyusutan', name: 'nilai_penyusutan' },
        { data: 'nilai_buku', name: 'nilai_buku' },
        { data: 'kondisi' },
        { data: 'akun_neraca' },
        { data: 'pembukuan' },
        { data: 'unit_kerja_id',orderable: false, },
        { data: 'penggunaRaw' },
        { data: 'nama_pengguna' },
        { data: 'lokasi_id' },
        { data: 'status_inven' },
        { data: 'update_kondisi' },
        { data: 'link_dokumentasi', },
        { data: 'link_lhi' },
        { data: 'no_bahi' },
        { data: 'tgl_bahi' },
        {
                data: 'status',
                name: 'status',
                orderable: false,
                searchable: false
        },
        {
                data: 'batch',
                name: 'batch',
                className: 'batch-col',
                orderable: false,
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
// table.on('draw.dt', function () {
//     table.columns.adjust();
// });

$(document).on('change', '.row-checkbox', function() {
    const id = $(this).val();
    if ($(this).is(':checked')) {
        if (!selectedRows.includes(id)) selectedRows.push(id);
    } else {
        selectedRows = selectedRows.filter(item => item != id);
    }
    // Update selectAll
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
});

$('#selectAll').on('change', function() {
    const isChecked = $(this).is(':checked');
    $('.row-checkbox').prop('checked', isChecked);
    $('.row-checkbox').each(function() {
        const id = $(this).val();
        if (isChecked) {
            if (!selectedRows.includes(id)) selectedRows.push(id);
        } else {
            selectedRows = selectedRows.filter(item => item != id);
        }
    });
});

function generatePSP() {
    if (selectedRows.length === 0) {
        alert('Please select at least one row.');
        return;
    }
    const lampiran = $('#lampiran').val();
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("psp.download") }}';
    form.target = '_blank'; // Open in new tab
    form.style.display = 'none';
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    selectedRows.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected[]';
        input.value = id;
        form.appendChild(input);
    });
    const lampiranInput = document.createElement('input');
    lampiranInput.type = 'hidden';
    lampiranInput.name = 'lampiran';
    lampiranInput.value = lampiran;
    form.appendChild(lampiranInput);
    document.body.appendChild(form);
    form.submit();
    // Redirect current page to internal.index
    window.location.href = '{{ route("internal.index") }}';
}

    // Function to open image modal
    function openImageModal(imageSrc, title) {
        // Create modal if it doesn't exist
        if (!$('#imageModal').length) {
            $('body').append(`
                <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel">${title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="${imageSrc}" class="img-fluid" alt="${title}" style="max-height: 70vh;">
                            </div>
                        </div>
                    </div>
                </div>
            `);
        } else {
            // Update existing modal
            $('#imageModal .modal-title').text(title);
            $('#imageModal img').attr('src', imageSrc).attr('alt', title);
        }

        // Show modal
        $('#imageModal').modal('show');
    }

    </script>


@endsection
