@extends('app')
@section('title', $title)
@section('dependencies')
  <link href="{{asset('/assets/dist/assets/css/plugins/animate.min.css')}}" rel="stylesheet" type="text/css">


@endsection
@section('content')
<div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Data Internal</a></li>
                  <li class="breadcrumb-item" aria-current="page">View Data Internal</li>
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
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>View Data</h3>
                        @if ($internal->status === 'locked' && $internal->is_requested !== 1 && !auth()->user()->isAdmin())
                            <span class="badge bg-danger">Data Terkunci</span>
                                <form action="{{ route('internal.requestUnlock', $internal->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-shadow btn-warning">Request Unlock</button>
                                </form>
                        @elseif ($internal->is_requested === 1 && !auth()->user()->isAdmin())
                            <div class="d-flex gap-2">
                                <a href="https://wa.me/{{ $adminPhone }}?text={{ urlencode('Tolong buka data internal berikut'.' NUP : ' . $internal->nup . ' Nama - Kode barang : '. $internal->barang->nama_barang .' - '. $internal->barang->kode_barang .' requested to unlock by ' . auth()->user()->name) }}" target="_blank" class="btn btn-shadow btn-success">
                                    <i class="ti ti-brand-whatsapp"></i> Chat Admin
                                </a>
                            </div>
                            <span class="badge bg-warning">Menunggu Persetujuan</span>
                        @else
                            <a href="{{route('internal.edit', $internal->id)}}" class="btn btn-shadow btn-warning">Edit Data BMN</a>
                        @endif
                    </div>
                    <div class="card-body ">

                        {{-- <form id="mainForm" method="POST" action="{{ route('internal.update', $internal->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT') --}}
                        <div class="row g-4" style="height: 60vh; overflow-y: hidden;">
                            <div class="col-md-2 col-sm-12  border-end border-muted">
                                <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <li><a class="nav-link active" id="v-pills-detail-tab" data-bs-toggle="pill" href="#v-pills-detail" role="tab" aria-controls="v-pills-detail" aria-selected="true">Detail BMN</a></li>
                                    <li><a class="nav-link" id="v-pills-foto-tab" data-bs-toggle="pill" href="#v-pills-foto" role="tab" aria-controls="v-pills-foto" aria-selected="false">Foto</a></li>
                                    <li><a class="nav-link" id="v-pills-dokumen-tab" data-bs-toggle="pill" href="#v-pills-dokumen" role="tab" aria-controls="v-pills-dokumen" aria-selected="false">Dokumen</a></li>
                                    <li><a class="nav-link" id="v-pills-pengguna-tab" data-bs-toggle="pill" href="#v-pills-pengguna" role="tab" aria-controls="v-pills-pengguna" aria-selected="false">Pengguna</a></li>
                                    <li><a class="nav-link" id="v-pills-identitas-tab" data-bs-toggle="pill" href="#v-pills-identitas" role="tab" aria-controls="v-pills-identitas" aria-selected="false">Identitas</a></li>
                                    <li><a class="nav-link" id="v-pills-bast-tab" data-bs-toggle="pill" href="#v-pills-bast" role="tab" aria-controls="v-pills-bast" aria-selected="false">BAST</a></li>

                                </ul>
                            </div>
                            <div class="col-md-10 col-sm-12 overflow-y-scroll" style="max-height: 60vh; ">
                                <div class="tab-content" id="v-pills-tabContent">

                                    <div class="tab-pane fade show active" id="v-pills-detail" role="tabpanel" aria-labelledby="v-pills-detail-tab">

                                        <div class="mb-3" >
                                            <h4 class="fw-bold mb-3">Detail BMN</h4>
                                            <hr>
                                            <label for="satker" class="form-label">Kode Satker</label>
                                            <select disabled class="form-control" name="satker_id" id="satker">
                                                <option value="" disabled selected>-- Pilih kode satker --</option>
                                                @foreach ($satker as $keysatker)

                                                <option value="{{$keysatker->id}}" {{ $internal->satker_id == $keysatker->id ? 'selected' : '' }}>{{$keysatker->kode_satker}} - {{$keysatker->nama_satker}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="barang" class="form-label">Kode Barang</label>
                                            <select
                                                disabled
                                                class="form-control"
                                                data-barang
                                                name="barang_id"
                                                id="barangSelect"
                                                >
                                                <option value="" selected disabled>--Pilih Kode Barang--</option>
                                                @foreach ($barang as $keybarang)

                                                    <option value="{{ $keybarang->id }}" {{ $internal->barang_id == $keybarang->id ? 'selected' : '' }}>{{ $keybarang->kode_barang }} - {{ $keybarang->nama_barang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nup" class="form-label">NUP</label>
                                            <input readonly type="number" name="nup" id="nup" class="form-control" value="{{ $internal->nup }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="unitkerja_id" class="form-label">Unit Kerja</label>
                                            <select
                                                disabled
                                                class="form-control"
                                                data-unitkerja
                                                name="unitkerja_id"
                                                id="unitkerjaSelect"
                                                >
                                                <option value="" selected disabled>--Pilih Unit Kerja--</option>
                                                @foreach ($unitkerja as $keyunitkerja)

                                                    <option value="{{ $keyunitkerja->id }}" {{ $internal->unit_kerja_id == $keyunitkerja->id ? 'selected' : '' }}>{{ $keyunitkerja->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- <div class="mb-3">
                                            <label for="nup" class="form-label"> NUP</label>
                                            <input type="number" name="nup" id="nup" class="form-control">
                                            <small>kosongkan </small>
                                        </div> --}}
                                        <div class="col-md-4 mb-3">
                                            <label for="tgl_perolehan" class="form-label">Tanggal Perolehan</label>
                                            <input readonly type="date" name="tgl_perolehan" id="tgl_perolehan" class="form-control" value="{{ $internal->tgl_perolehan ? \Carbon\Carbon::parse($internal->tgl_perolehan)->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class=" mb-3">
                                            <label for="merk" class="form-label">Merk</label>
                                            <input readonly type="text" name="merk" id="merk" class="form-control" value="{{ $internal->merk }}">
                                        </div>
                                        <div class=" mb-3">
                                            <label for="tipe" class="form-label">Tipe</label>
                                            <input readonly type="text" name="tipe" id="tipe" class="form-control" value="{{ $internal->tipe }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="jumlah" class="form-label">Jumlah</label>
                                            <input readonly type="number" name="jumlah" id="jumlah" class="form-control" value="{{ $internal->jumlah }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="nilai_perolehan" class="form-label">Nilai Perolehan</label>
                                            <input readonly type="text" name="nilai_perolehan" id="nilai_perolehan" class="form-control" value="{{ $internal->nilai_aset }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="kondisi" class="form-label">Kondisi</label>
                                            <select disabled name="kondisi" id="kondisi" class="form-control">
                                                <option value="" selected disabled>--Pilih Kondisi--</option>
                                                <option value="B" {{ $internal->kondisi == 'B' ? 'selected' : '' }}>Baik</option>
                                                <option value="RR" {{ $internal->kondisi == 'RR' ? 'selected' : '' }}>Rusak Ringan</option>
                                                <option value="RB" {{ $internal->kondisi == 'RB' ? 'selected' : '' }}>Rusak Berat</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="akun_neraca" class="form-label">Akun Neraca</label>
                                            <input readonly type="text" name="akun_neraca" id="akun_neraca" class="form-control" value="{{ $internal->akun_neraca }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="pembukuan" class="form-label">Pembukuan</label>
                                            <input readonly type="text" name="pembukuan" id="pembukuan" class="form-control" value="{{ $internal->pembukuan }}">
                                        </div>
                                        <div class="mb-4">
                                            <label for="lokasi_id" class="form-label">Lokasi/Ruang</label>
                                            <select
                                                disabled

                                                class="form-control"
                                                data-lokasi
                                                name="lokasi_id"
                                                id="lokasiSelect"
                                                >
                                                <option value="" selected disabled>--Pilih Lokasi/Ruang--</option>
                                                @foreach ($lokasi as $keylokasi)

                                                    <option value="{{ $keylokasi->id }}" {{ $internal->lokasi_id == $keylokasi->id ? 'selected' : '' }}>{{ $keylokasi->unitKerja->name }} - {{ $keylokasi->name }}</option>
                                                @endforeach
                                                <option value="" {{ $internal->lokasi_id === null ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                            <label for="otherLokasiInput" id="otherLokasiLabel" class="form-label mt-2" style="display: none;">Keterangan Lokasi</label>
                                            <input readonly value="{{$internal->ket_lokasi}}" name="ketLokasi" type="text" id="otherLokasiInput" class="form-control mt-2" placeholder="Masukkan keterangan lokasi" >
                                        </div>
                                        <div class="mb-3" style="height: 20vh"></div>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-foto" role="tabpanel" aria-labelledby="v-pills-foto-tab">

                                        {{-- <form id="imageForm" enctype="multipart/form-data" class="mb-3"> --}}
                                            <h4 class="fw-bold mb-3">Foto</h4>
                                            <hr>
                                            <div class="row mb-3">

                                                {{-- <div class="col-md-6">

                                                    <input type="file" id="imageInput" accept="image/*" class="form-control">
                                                </div> --}}
                                                <div class="col-md-6">
                                                    {{-- <button type="button" id="addImage" class="btn btn-shadow btn-primary" onclick="openAddModal({{ $internal->id }})">View Foto</button> --}}
                                                </div>
                                            </div>

                                            <table id="imageTable" border="1" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Preview</th>
                                                        <th style="max-width: 150px;
                                                                white-space: nowrap;
                                                                overflow: hidden;
                                                                text-overflow: ellipsis;
                                                                word-wrap: break-word; ">Filename</th>
                                                        <th>Title</th>
                                                        <th>Description</th>
                                                        <th>Status (Cover)</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ( $internalImages as $image )
                                                        <tr>
                                                            <td>
                                                                <img src="{{ asset($image->path) }}" alt="Image" style="max-width: 100px; max-height: 100px;">
                                                            </td>
                                                            <td style="max-width: 150px;
                                                                white-space: nowrap;
                                                                overflow: hidden;
                                                                text-overflow: ellipsis;
                                                                word-wrap: break-word; ">{{ basename($image->path) }}</td>
                                                            <td>
                                                                <input class="form-control" type="text" value="{{ $image->title }}" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" value="{{ $image->description }}" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="radio" name="cover" class="form-check-input" {{ $image->is_cover ? 'checked' : '' }} disabled>
                                                            </td>
                                                            <td>
                                                                {{-- <button onclick="openEditModal({{ $image }})" class="btn btn-shadow btn-warning">Edit</button>
                                                                <form onclick="return confirm('Yakin ingin menghapus {{ basename($image->path) }} ?')" action="{{ route('internal.imageDestroy', $image->id) }}" method="post" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-shadow btn-danger">Delete</button> --}}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                        {{-- </form> --}}
                                        <div class="mb-3">

                                            <label for="link_dokumentasi">Link Dokumentasi</label>
                                            <input readonly type="text" name="link_dokumentasi" id="link_dokumentasi" class="form-control" value="{{ $internal->link_dokumentasi }}">
                                        </div>
                                        <div class="mb-3" style="height: 5vh"></div>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-dokumen" role="tabpanel" aria-labelledby="v-pills-dokumen-tab">
                                        <h4 class="fw-bold mb-3">Dokumen</h4>
                                        <hr>

                                        <table id="documentTable" border="1" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="max-width: 150px;
                                                            white-space: nowrap;
                                                            overflow: hidden;
                                                            text-overflow: ellipsis;
                                                            word-wrap: break-word; ">Filename</th>
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ( $internalDocuments as $document )
                                                    <tr>
                                                        <td style="max-width: 150px;
                                                            white-space: nowrap;
                                                            overflow: hidden;
                                                            text-overflow: ellipsis;
                                                            word-wrap: break-word;">
                                                            <a href="{{ asset( $document->path) }}" target="_blank">{{ basename($document->filename) }}</a>
                                                        </td>
                                                        <td>{{ $document->title }}</td>
                                                        <td>{{ $document->description }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div class="mb-3" style="height: 5vh"></div>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-pengguna" role="tabpanel" aria-labelledby="v-pills-pengguna-tab">

                                        <h4 class="fw-bold mb-3">Pengguna</h4>
                                        <hr>
                                        <!-- Image Upload -->
                                        {{-- <div class="mb-3">
                                            <label for="profileImage" class="form-label">Foto Pengguna</label>
                                        </div>
                                        <div class="mt-3 mb-3">
                                            @if (empty($internal->profile_image))
                                                    <h5>tidak ada foto</h5>
                                                @else
                                                    <img id="preview" src="{{ asset( $internal->profile_image_path) }}" alt="Image Preview" class="img-thumbnail" style="max-width: 200px;">
                                                @endif
                                        </div> --}}

                                        <!-- Name -->
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nama Lengkap</label>
                                            <input readonly type="text" name="name" class="form-control" id="name" placeholder="Masukkan nama lengkap" value="{{ $internal->nama_pengguna }}">
                                        </div> <!-- Address -->
                                        <div class="mb-3">
                                            <label for="pengguna_unitkerja_id" class="form-label">Unit Penugasan/PokJa</label>
                                            <select
                                                class="form-control"
                                                data-penggunaunitkerja
                                                name="pengguna_unitkerja_id"
                                                id="penggunaunitkerjaSelect"
                                                disabled
                                                >
                                                <option value="" selected disabled>--Pilih Unit Penugasan/PokJa--</option>
                                                @foreach ($unitkerja as $keyunitkerja)

                                                    <option {{ $internal->pengguna_unitkerja_id == $keyunitkerja->id ? 'selected' : '' }} value="{{ $keyunitkerja->id }}" >{{ $keyunitkerja->name }}</option>
                                                @endforeach
                                                <option {{ $internal->pengguna_unitkerja_id === null ? 'selected' : '' }} value="" >Lainnya</option>
                                            </select>
                                            <label for="otherUnitKerjaInput" id="otherUnitKerjaLabel" class="form-label mt-2" >Keterangan Unit Penugasan Eselon 2</label>
                                            <input readonly value="{{ $internal->ket_penugasan }}" name="otherUnitKerja" type="text" id="otherUnitKerjaInput" class="form-control mt-2" placeholder="Masukkan keterangan unit penugasan eselon 2" >
                                        </div>
                                        <div class="mb-3">
                                            <label for="unit_teknis_id" class="form-label">Unit Pokja</label>
                                            <select
                                                class="form-control"
                                                data-penggunaunitteknis
                                                name="unit_teknis_id"
                                                id="penggunaunitteknisSelect"
                                                disabled
                                                >
                                                <option value="" selected disabled>--Pilih Unit Pokja--</option>
                                                @foreach ($unitteknis as $keyunitteknis)

                                                    <option {{ $internal->unit_teknis_id == $keyunitteknis->id ? 'selected' : '' }} value="{{ $keyunitteknis->id }}" >{{ $keyunitteknis->name }}</option>
                                                @endforeach
                                                <option {{ $internal->unit_teknis_id === null ? 'selected' : '' }} value="" >Lainnya</option>
                                            </select>
                                            <label for="otherUnitTeknisInput" id="otherUnitTeknisLabel" class="form-label mt-2" >Keterangan Unit Pokja</label>
                                            <input readonly value="{{ $internal->ket_unit_teknis }}" name="otherUnitTeknis" type="text" id="otherUnitTeknisInput" class="form-control mt-2" placeholder="Masukkan keterangan unit pokja" >
                                        </div>

                                            <div class="mb-3">
                                                <label for="nip_pengguna" class="form-label">NIP</label>
                                                <input readonly type="text" name="nip_pengguna" id="nip_pengguna" class="form-control" placeholder="Masukkan NIP" value="{{ old('nip_pengguna', $internal->nip_pengguna ?? '') }}">
                                            </div>

                                            <div class="mb-3">
                                                <label for="jabatan_pengguna" class="form-label">Jabatan</label>
                                                <input readonly type="text" name="jabatan_pengguna" id="jabatan_pengguna" class="form-control" placeholder="Masukkan jabatan" value="{{ old('jabatan_pengguna', $internal->jabatan_pengguna ?? '') }}">
                                            </div>

                                            <div class="mb-3">
                                                <label for="alamat_pengguna" class="form-label">Alamat</label>
                                                <textarea readonly name="alamat_pengguna" id="alamat_pengguna" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat_pengguna', $internal->alamat_pengguna ?? '') }}</textarea>
                                            </div>
                                        <div class="mb-3" style="height: 5vh"></div>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-identitas" role="tabpanel" aria-labelledby="v-pills-identitas-tab">

                                        <h4 class="fw-bold mb-3">Identitas</h4>
                                        <hr>

                                        @if ($internal->identitas == null)
                                            <div class="alert alert-warning" role="alert">
                                                Tidak ada identitas.
                                            </div>
                                        @else
                                            <h4 class="fw-bold">{{ $internal->identitas->identitasKategori->name }}</h4>
                                            <h5 class="fw-bold">{{ $internal->identitas->name }}</h5>
                                        @endif
                                        <ul class="list-group list-group-flush">
                                            @foreach($internal->dataAtribut as $val)
                                                <li class="list-group-item list-group-item-action" >
                                                    {{ $val->atribut->label }} :
                                                    {{ $val->value_string ?? $val->value_integer ?? $val->value_date }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-bast" role="tabpanel" aria-labelledby="v-pills-bast-tab">

                                            <h4 class="fw-bold mb-3">BAST</h4>
                                            <hr>

                                            <a target="_blank" href="{{ route('internal.bast', $internal->id) }}"
                                                class="btn btn-sm btn-shadow btn-primary fs-6 mb-3">
                                                <i class="bi bi-file-pdf fs-6"></i>
                                                Generate BAST
                                            </a>

                                            <div class="mb-3">
                                                <label for="nama_pihak_pertama" class="form-label">Nama Pihak Pertama</label>
                                                <input readonly type="text" name="nama_pihak_pertama" id="nama_pihak_pertama" class="form-control" placeholder="Masukkan nama pihak pertama" value="{{ old('nama_pihak_pertama', $internal->nama_pihak_pertama ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="nip_pihak_pertama" class="form-label">NIP Pihak Pertama</label>
                                                <input readonly type="text" name="nip_pihak_pertama" id="nip_pihak_pertama" class="form-control" placeholder="Masukkan NIP pihak pertama" value="{{ old('nip_pihak_pertama', $internal->nip_pihak_pertama ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="jabatan_pihak_pertama" class="form-label">Jabatan Pihak Pertama</label>
                                                <input readonly type="text" name="jabatan_pihak_pertama" id="jabatan_pihak_pertama" class="form-control" placeholder="Masukkan jabatan pihak pertama" value="{{ old('jabatan_pihak_pertama', $internal->jabatan_pihak_pertama ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="alamat_pihak_pertama" class="form-label">Alamat Pihak Pertama</label>
                                                <textarea readonly name="alamat_pihak_pertama" id="alamat_pihak_pertama" class="form-control" rows="3" placeholder="Masukkan alamat lengkap pihak pertama">{{ old('alamat_pihak_pertama', $internal->alamat_pihak_pertama ?? '') }}</textarea>
                                            </div>
                                            <div class="mb-3" style="height: 5vh"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- </form> --}}
                        <hr>
                        {{-- <button type="submit" form="mainForm" id="submitAll" class="btn btn-shadow btn-success mt-3">Submit All</button> --}}
                        <a href="{{ route('internal.index') }}" class="btn btn-shadow  btn-secondary">Kembali</a>
                    {{-- </form> --}}
                </div>

            </div>
        </div>
</div>
<!-- Modal Edit Image -->
<div class="modal fade" id="editImageModal" tabindex="-1" aria-labelledby="editImageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editImageModalLabel">Edit Image Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Dynamic content will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-shadow btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-shadow btn-primary" >Submit</button>
      </div>
    </div>
  </div>
</div>

    <script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{asset('/assets/autonumeric/dist/autoNumeric.min.js')}}"></script>


    <script>


        document.getElementById('imageForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Instead of submitting, just add to tempImages
            // The main form will handle submission
        });
    </script>

    {{-- pengguna script --}}
    <script> // Preview uploaded image
        // document.getElementById('profileImage').addEventListener('change', function(event) {
        //     const file = event.target.files[0];
        //     if (file) { const reader = new FileReader(); reader.onload = function(e) {
        //         const preview = document.getElementById('preview');
        //         preview.src = e.target.result;
        //         preview.style.display = 'block';
        //         };
        //         reader.readAsDataURL(file);
        //     }
        // });

            // Handle form submission (example)
            // document.getElementById('profileForm').addEventListener('submit', function(e) {
            //     e.preventDefault();
            //     alert("Form submitted!\nName: " +
            //     document.getElementById('name').value +
            //     "\nAddress: " +
            //     document.getElementById('address').value);
            // });
    </script>


    <script>
        // auto format currency
        new AutoNumeric('#nilai_perolehan', {
            currencySymbol: 'Rp ',
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            unformatOnSubmit: true
            });
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function () {



            var modalBarang = document.querySelectorAll('[data-barang]');
            for (i = 0; i < modalBarang.length; ++i) {
            var elementBarang = modalBarang[i];
            new Choices(elementBarang, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari kode barang',
                position: 'bottom'
            });
            }

            var UnitSelect = document.querySelectorAll('[data-unitkerja]');
            for (i = 0; i < UnitSelect.length; ++i) {
            var unitelement = UnitSelect[i];
            new Choices(unitelement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari unit kerja'
            });
            }

            var UnitPenggunaSelect = document.querySelectorAll('[data-penggunaunitkerja]');
            for (i = 0; i < UnitPenggunaSelect.length; ++i) {
            var unitelement = UnitPenggunaSelect[i];
            new Choices(unitelement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari unit penugasan/pokja'
            });
            }



            var LokasiSelect = document.querySelectorAll('[data-lokasi]');
            for (i = 0; i < LokasiSelect.length; ++i) {
            var lokasielement = LokasiSelect[i];
            new Choices(lokasielement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari lokasi',
                position: 'top'
            });
            }
        })
    </script> --}}












@endsection
