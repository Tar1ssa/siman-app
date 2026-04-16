@extends('app')
@section('title', $title)
@section('dependencies')
  <link href="{{asset('/assets/dist/assets/css/plugins/animate.min.css')}}" rel="stylesheet" type="text/css">
<style>
    .choices__list--dropdown {
    z-index: 5 !important;
}
</style>
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
                  <li class="breadcrumb-item" aria-current="page">Edit Data Internal</li>
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
                    <div class="card-header">
                        <h3>Edit Data</h3>
                    </div>
                    <div class="card-body ">

                    <form id="mainForm" name="mainform" method="POST" action="{{ route('internal.update', $internal->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                            <div class="row g-4" style="height: 60vh; overflow-y: hidden; @media (max-width: 768px) { height: 100vh; overflow-y: visible; }">
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
                                <div class="col-md-10 col-sm-12 overflow-y-scroll" style="max-height: 60vh; @media (max-width: 768px) { max-height: 100vh; }">
                                    <div class="tab-content" id="v-pills-tabContent">

                                        <div class="tab-pane fade show active" id="v-pills-detail" role="tabpanel" aria-labelledby="v-pills-detail-tab">

                                            <div class="mb-3" >
                                                <h4 class="fw-bold mb-3">Detail BMN</h4>
                                                <hr>
                                                <label for="satker" class="form-label">Kode Satker</label>
                                                <select class="form-control" name="satker_id" id="satker">
                                                    <option value="" disabled selected>-- Pilih kode satker --</option>
                                                    @foreach ($satker as $keysatker)

                                                    <option value="{{$keysatker->id}}" {{ $internal->satker_id == $keysatker->id ? 'selected' : '' }}>{{$keysatker->kode_satker}} - {{$keysatker->nama_satker}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="barang" class="form-label">Kode Barang</label>
                                                <select
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
                                                <label for="unitkerja_id" class="form-label">Unit Kerja</label>
                                                <select
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
                                                <input type="date" name="tgl_perolehan" id="tgl_perolehan" class="form-control" value="{{ \Carbon\Carbon::parse($internal->tgl_perolehan)->format('Y-m-d') }}">
                                            </div>
                                            <div class=" mb-3">
                                                <label for="merk" class="form-label">Merk</label>
                                                <input type="text" name="merk" id="merk" class="form-control" value="{{ $internal->merk }}">
                                            </div>
                                            <div class=" mb-3">
                                                <label for="tipe" class="form-label">Tipe</label>
                                                <input type="text" name="tipe" id="tipe" class="form-control" value="{{ $internal->tipe }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="jumlah" class="form-label">Jumlah</label>
                                                <input type="number" name="jumlah" id="jumlah" class="form-control" value="{{ $internal->jumlah }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="nilai_perolehan" class="form-label">Nilai Perolehan</label>
                                                <input type="text" name="nilai_perolehan" id="nilai_perolehan" class="form-control" value="{{ $internal->nilai_aset }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="kondisi" class="form-label">Kondisi</label>
                                                <select name="kondisi" id="kondisi" class="form-control">
                                                    <option value="" selected disabled>--Pilih Kondisi--</option>
                                                    <option value="B" {{ $internal->kondisi == 'B' ? 'selected' : '' }}>Baik</option>
                                                    <option value="RR" {{ $internal->kondisi == 'RR' ? 'selected' : '' }}>Rusak Ringan</option>
                                                    <option value="RB" {{ $internal->kondisi == 'RB' ? 'selected' : '' }}>Rusak Berat</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="pembukuan" class="form-label">Pembukuan</label>
                                                <select name="pembukuan" id="pembukuan" class="form-control">
                                                    <option value="" selected disabled>--Pilih Pembukuan--</option>
                                                    <option value="Perolehan APBN" {{ $internal->pembukuan == 'Perolehan APBN' ? 'selected' : '' }}>Perolehan APBN</option>
                                                    <option value="Hibah" {{ $internal->pembukuan == 'Hibah' ? 'selected' : '' }}>Hibah</option>
                                                </select>
                                            </div>
                                            <div class="mb-4">
                                                <label for="lokasi_id" class="form-label">Lokasi/Ruang</label>
                                                <select
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
                                                <label for="otherLokasiInput" id="otherLokasiLabel" class="form-label mt-2" {{ $internal->lokasi_id === null ? '' : 'style=display:none;' }}>Keterangan Lokasi</label>
                                                <input name="ketLokasi" value="{{ old('ketLokasi', $internal->ket_lokasi ?? '') }}" type="text" id="otherLokasiInput" class="form-control mt-2" placeholder="Masukkan keterangan lokasi" {{ $internal->lokasi_id === null ? '' : 'style=display:none;' }}>
                                            </div>
                                            <div class="mb-3" style="height: 20vh"></div>
                                        </div>

                                        <div class="tab-pane fade" id="v-pills-foto" role="tabpanel" aria-labelledby="v-pills-foto-tab">
                                                <h4 class="fw-bold mb-3">Foto</h4>
                                                <hr>
                                                <div class="row mb-3">


                                                    <div class="col-md-6">
                                                        <button type="button"  class="btn btn-shadow btn-primary" onclick="openAddModal({{ $internal->id }})">Tambah Foto</button>
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
                                                                    <img src="{{ asset( $image->path) }}" alt="Image" style="max-width: 100px; max-height: 100px;">
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
                                                                <td >
                                                                    <div class="d-flex flex-column gap-1">

                                                                        <button type="button" onclick="openEditModal({{ $image }})" class="btn btn-shadow btn-warning">Edit</button>
                                                                        <button class="btn btn-shadow btn-danger" type="submit"
                                                                        onclick="return confirm('Yakin ingin menghapus gambar {{ $image->title }} ?')"
                                                                        form="delete-image-{{ $image->id }}"
                                                                        >Delete
                                                                        </button>
                                                                    </div>

                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>


                                            <div class="mb-3">

                                                <label for="link_dokumentasi">Link Dokumentasi</label>
                                                <input type="text" name="link_dokumentasi" id="link_dokumentasi" class="form-control" value="{{ $internal->link_dokumentasi }}">
                                            </div>
                                            <div class="mb-3" style="height: 5vh"></div>
                                        </div>

                                        <div class="tab-pane fade" id="v-pills-dokumen" role="tabpanel" aria-labelledby="v-pills-dokumen-tab">
                                                <h4 class="fw-bold mb-3">Dokumen</h4>
                                                <hr>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <button type="button"  class="btn btn-shadow btn-primary" onclick="openAddDocumentModal({{ $internal->id }})">Tambah Dokumen</button>
                                                    </div>
                                                </div>

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
                                                            <th>Actions</th>
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
                                                                <td>
                                                                    <div class="d-flex flex-column gap-1">

                                                                        <button type="button" class="btn btn-sm btn-warning btn-shadow" onclick="openEditDocumentModal({{ $document->id }}, '{{ $document->title }}', '{{ $document->description }}')">Edit</button>

                                                                        <button form="delete-document-{{ $document->id }}" type="submit" class="btn btn-sm btn-shadow btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                                    </div>
                                                                </td>
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
                                                <label for="profileImage" class="form-label">Upload Foto Pengguna</label>
                                                <input class="form-control" type="file" id="profileImage" accept="image/*" name="profileImage" >
                                                <small>Upload file untuk mengubah foto pengguna</small>
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
                                                <input type="text" name="name" class="form-control" id="name" placeholder="Masukkan nama lengkap" value="{{ $internal->nama_pengguna }}">
                                            </div> <!-- Address -->
                                            <div class="mb-3">
                                                <label for="pengguna_unitkerja_id" class="form-label">Unit Penugasan Eselon 2</label>
                                            <select
                                                class="form-control"
                                                data-penggunaunitkerja
                                                name="pengguna_unitkerja_id"
                                                id="penggunaunitkerjaSelect"
                                                >
                                                <option value="" selected disabled>--Pilih Unit Penugasan/PokJa--</option>
                                                @foreach ($unitkerja as $keyunitkerja)

                                                    <option {{ $internal->pengguna_unitkerja_id == $keyunitkerja->id ? 'selected' : '' }} value="{{ $keyunitkerja->id }}" >{{ $keyunitkerja->name }}</option>
                                                @endforeach
                                                <option value="" {{ $internal->pengguna_unitkerja_id === null ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                            <label for="otherUnitKerjaInput" id="otherUnitKerjaLabel" class="form-label mt-2" {{ $internal->pengguna_unitkerja_id === null ? '' : 'style=display:none;' }}>Keterangan Unit Penugasan Eselon 2</label>
                                            <input name="ket_penugasan" value="{{ old('ket_penugasan', $internal->ket_penugasan ?? '') }}" type="text" id="otherUnitKerjaInput" class="form-control mt-2" placeholder="Masukkan keterangan unit penugasan eselon 2" {{ $internal->pengguna_unitkerja_id === null ? '' : 'style=display:none;' }}>
                                            </div>

                                            <div class="mb-3">
                                                <label for="unit_teknis_id" class="form-label">Unit Pokja</label>
                                                <select
                                                    class="form-control"
                                                    data-penggunaunitteknis
                                                    name="unit_teknis_id"
                                                    id="penggunaunitteknisSelect"
                                                    >
                                                    <option value="" selected disabled>--Pilih Unit Pokja--</option>
                                                    @foreach ($unitteknis as $keyunitteknis)

                                                        <option {{ $internal->unit_teknis_id == $keyunitteknis->id ? 'selected' : '' }} value="{{ $keyunitteknis->id }}" >{{ $keyunitteknis->name }}</option>
                                                    @endforeach
                                                    <option value="" {{ $internal->unit_teknis_id === null ? 'selected' : '' }}>Lainnya</option>
                                                </select>
                                                <label for="otherUnitTeknisInput" id="otherUnitTeknisLabel" class="form-label mt-2" {{ $internal->unit_teknis_id === null ? '' : 'style=display:none;' }}>Keterangan Unit Pokja</label>
                                                <input name="ket_unit_teknis" value="{{ old('ket_unit_teknis', $internal->ket_unit_teknis ?? '') }}" type="text" id="otherUnitTeknisInput" class="form-control mt-2" placeholder="Masukkan keterangan unit pokja"  {{ $internal->unit_teknis_id === null ? '' : 'style=display:none;' }}>
                                            </div>

                                            <div class="mb-3">
                                                <label for="nip_pengguna" class="form-label">NIP</label>
                                                <input type="text" name="nip_pengguna" id="nip_pengguna" class="form-control" placeholder="Masukkan NIP" value="{{ old('nip_pengguna', $internal->nip_pengguna ?? '') }}">
                                            </div>

                                            <div class="mb-3">
                                                <label for="jabatan_pengguna" class="form-label">Jabatan</label>
                                                <input type="text" name="jabatan_pengguna" id="jabatan_pengguna" class="form-control" placeholder="Masukkan jabatan" value="{{ old('jabatan_pengguna', $internal->jabatan_pengguna ?? '') }}">
                                            </div>

                                            <div class="mb-3">
                                                <label for="alamat_pengguna" class="form-label">Alamat</label>
                                                <textarea name="alamat_pengguna" id="alamat_pengguna" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat_pengguna', $internal->alamat_pengguna ?? '') }}</textarea>
                                            </div>

                                            <div class="mb-3" style="height: 5vh"></div>
                                        </div>

                                        <div class="tab-pane fade" id="v-pills-identitas" role="tabpanel" aria-labelledby="v-pills-identitas-tab">

                                            <h4 class="fw-bold mb-3">Identitas</h4>
                                            <hr>

                                            <div class="mb-3">

                                                <label for="identitas_kategori" class="form-label">Kategori identitas</label>
                                                <select data-kategori class="form-control" id="identitas_kategori" name="kategori_id">
                                                    <option value="">-- Pilih Kategori --</option>
                                                    @foreach($identitasKategori as $cat)
                                                        <option value="{{ $cat->id }}" {{ optional($internal->identitas)->kategori_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">

                                                <label class="form-label" for="">Identitas</label>
                                                <select
                                                    class="form-control"
                                                    data-identitas
                                                    id="identitas" name="identitas_id"
                                                    >
                                                    <option value="" disabled selected>--Pilih identitas--</option>
                                                    @foreach ($identitas as $cat)
                                                        <option value="{{ $cat->id }}"
                                                            @selected($cat->id === $internal->identitas_id)>
                                                            {{ $cat->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                @if (empty($internal->identitas))
                                                    <div id="dynamic-fields">

                                                        <h5>Belum ada identitas</h5>
                                                    </div>

                                                @else
                                                    <div id="dynamic-fields">
                                                        @foreach($internal->identitas->atribut as $attr)
                                                            <div class="mb-3">

                                                                <label class="form-label" for="atribut_{{ $attr->id }}">{{ $attr->label }}</label>
                                                                <input type="text"
                                                                    class="form-control mb-3"
                                                                    name="atribut[{{ $attr->id }}]"

                                                                    value="{{ $dataAtribut[$attr->id]->value_string ?? $dataAtribut[$attr->id]->value_integer ?? $dataAtribut[$attr->id]->value_date ?? '' }}">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mb-3" style="height: 5vh"></div>
                                        </div>

                                        <div class="tab-pane fade" id="v-pills-bast" role="tabpanel" aria-labelledby="v-pills-bast-tab">

                                            <h4 class="fw-bold mb-3">BAST</h4>
                                            <hr>

                                            <div class="mb-3">
                                                <label for="download_after_input" class="form-label">Download setelah Edit</label>
                                                <input type="checkbox" name="download_after_input" id="download_after_input" class="form-check-input ms-2" value="1">
                                            </div>

                                            <div class="mb-3">
                                                <label for="nama_pihak_pertama" class="form-label">Nama Pihak Pertama</label>
                                                <input type="text" name="nama_pihak_pertama" id="nama_pihak_pertama" class="form-control" placeholder="Masukkan nama pihak pertama" value="{{ old('nama_pihak_pertama', $internal->nama_pihak_pertama ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="nip_pihak_pertama" class="form-label">NIP Pihak Pertama</label>
                                                <input type="text" name="nip_pihak_pertama" id="nip_pihak_pertama" class="form-control" placeholder="Masukkan NIP pihak pertama" value="{{ old('nip_pihak_pertama', $internal->nip_pihak_pertama ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="jabatan_pihak_pertama" class="form-label">Jabatan Pihak Pertama</label>
                                                <input type="text" name="jabatan_pihak_pertama" id="jabatan_pihak_pertama" class="form-control" placeholder="Masukkan jabatan pihak pertama" value="{{ old('jabatan_pihak_pertama', $internal->jabatan_pihak_pertama ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="alamat_pihak_pertama" class="form-label">Alamat Pihak Pertama</label>
                                                <textarea name="alamat_pihak_pertama" id="alamat_pihak_pertama" class="form-control" rows="3" placeholder="Masukkan alamat lengkap pihak pertama">{{ old('alamat_pihak_pertama', $internal->alamat_pihak_pertama ?? '') }}</textarea>
                                            </div>
                                            <div class="mb-3" style="height: 5vh"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <hr>
                        <button type="submit" id="" class="btn btn-shadow btn-warning mt-3">Update</button>
                        <a href="{{ route('internal.show', $internal->id) }}" class="btn btn-shadow btn-secondary mt-3">Kembali</a>
                    </form>
                    @foreach ($internalImages as $image)
                    <form id="delete-image-{{ $image->id }}"
                        action="{{ route('internal.imageDestroy', $image->id) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endforeach
                    @foreach ($internalDocuments as $document)
                    <form id="delete-document-{{ $document->id }}"
                        action="{{ route('internal.documentDestroy', $document->id) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endforeach
                </div>

            </div>
        </div>
</div>
<!-- Modal Edit Image -->
<div class="modal fade" id="editImageModal" tabindex="-1" aria-labelledby="editImageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editImageModalLabel"></h5>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Dynamic content will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-shadow btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Document -->
<div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editDocumentModalLabel"></h5>
      </div>
      <div class="modal-body" id="documentModalBody">
        <!-- Dynamic content will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-shadow btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

    <script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{asset('/assets/autonumeric/dist/autoNumeric.min.js')}}"></script>

    {{-- <script>
        document.addEventListener('change', function (e) {
            if (e.target.id === 'identitas_kategori') {
                const kategoriId = e.target.value;

                const container = document.getElementById('dynamic-fields');
                container.innerHTML = '';
                const identitasSelect = document.getElementById('identitas');
                identitasSelect.innerHTML = '<option value="" disabled selected>-- Pilih identitas --</option>';

                if (kategoriId) {
                    fetch(`/identitas/bykategori/${kategoriId}`)
                        .then(res => res.json())
                        .then(data => {
                            data.forEach(identitas => {
                                const option = document.createElement('option');
                                option.value = identitas.id;
                                option.textContent = identitas.name;
                                identitasSelect.appendChild(option);
                            });
                        });
                }
            }
        });
    </script> --}}

    <script>
    // Populate identitas when category changes (matches make.blade behavior)
    // document.addEventListener('change', function (e) {
    //     if (e.target.id === 'identitas_kategori') {
    //         const kategoriId = e.target.value;
    //         const identitasSelect = document.getElementById('identitas');
    //         // identitasSelect.innerHTML = '<option value="" disabled selected>-- Pilih identitas --</option>';
    //         const container = document.getElementById('dynamic-fields');
    //         container.innerHTML = '';

    //         if (kategoriId) {
    //             fetch(`/identitas/bykategori/${kategoriId}`)
    //                 .then(res => res.json())
    //                 .then(data => {
    //                     data.forEach(identitas => {
    //                         choices.clearChoices();   // remove old options

    //                         const newOptions = data.map(item => ({
    //                             value: item.id,
    //                             label: item.name
    //                         }));

    //                         choices.setChoices(newOptions, 'value', 'label', true);
    //                         // identitasSelect.innerHTML += `<option value="${identitas.id}">${identitas.name}</option>`;

    //                         // const option = document.createElement('option');
    //                         // option.value = identitas.id;
    //                         // option.textContent = identitas.name;
    //                         // identitasSelect.appendChild(option);
    //                     });

    //                     // If there is a pre-selected identitas, keep it selected
    //                     // const currentId = '{{ $internal->identitas_id ?? '' }}';
    //                     // if (currentId) {
    //                     //     identitasSelect.value = currentId;
    //                     //     // trigger change to load attributes
    //                     //     identitasSelect.dispatchEvent(new Event('change'));
    //                     // }
    //                 });
    //         }
    //     }
    // });

    document.addEventListener('change', function (e) {
        if (e.target.id === 'identitas_kategori') {
            const kategoriId = e.target.value;
            const container = document.getElementById('dynamic-fields');
            container.innerHTML = '';

            if (!kategoriId) return;

            fetch(`/identitas/bykategori/${kategoriId}`)
                .then(res => res.json())
                .then(data => {

                    //  IMPORTANT PART
                    identitasChoices[0].clearChoices();
                    identitasChoices[0].removeActiveItems();

                    const newOptions = data.map(item => ({
                        value: item.id,
                        label: item.name
                    }));

                    identitasChoices[0].setChoices(newOptions, 'value', 'label', true);
                });
        }
    });


    // On load, if a category is already selected, pre-populate identitas select
    document.addEventListener('DOMContentLoaded', function () {
        const kategoriSelect = document.getElementById('identitas_kategori');
        if (kategoriSelect && kategoriSelect.value) {
            const event = new Event('change');
            kategoriSelect.dispatchEvent(event);
        }
    });

    document.getElementById('identitas').addEventListener('change', async function () {
        const container = document.getElementById('dynamic-fields');
        container.innerHTML = '';

        if (!this.value) return;
        const res = await fetch(`/identitas/${this.value}/atribut`);
        const fields = await res.json();

        fields.forEach(f => {
            container.innerHTML += `
                <div class="mb-3">
                    <label class="form-label">${f.label}${f.required ? ' *' : ''}</label>
                    <input class="form-control" type="${f.type === 'number' ? 'number' : 'text'}"
                        name="atribut[${f.id}]"
                        placeholder="${f.placeholder ?? ''}">
                    <small>${f.help_text ?? ''}</small>
                </div>
            `;
        });
    });
    </script>

    <script>
        function openAddModal(internalId) {
            var editModal = new bootstrap.Modal(document.getElementById('editImageModal'));
            editModal.show();

            document.getElementById('editImageModalLabel').innerHTML = 'Add Image';

            var modalBody = document.getElementById('modalBody');
            const route = "{{ route('internal.addImage') }}";
            const modalAddContent = `
                <form id="addImageForm" action="${route}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="internal_id" value="${internalId}">
                    <div class="mb-3">
                        <label for="addImageInput" class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="addImageInput" name="image" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="addTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="addTitle" name="title">
                    </div>
                    <div class="mb-3">
                        <label for="addDescription" class="form-label">Description</label>
                        <input type="text" class="form-control" id="addDescription" name="description">
                    </div>
                    <div class="mb-3">
                        <label for="addIsCover" class="form-label">Set as Cover</label>
                        <input type="checkbox" class="form-check-input" id="addIsCover" name="is_cover" value="1">
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Upload</button>
                </form>
            `;
            modalBody.innerHTML = modalAddContent;
        }

        function openEditModal(image) {
            var editModal = new bootstrap.Modal(document.getElementById('editImageModal'));
            editModal.show();

            document.getElementById('editImageModalLabel').innerHTML = 'Edit Image Details';
            var modalBody = document.getElementById('modalBody');
            const imageId = image.id; // Assuming 'image' object has an 'id' property
            const route = "{{ route('internal.updateImage', ':id') }}".replace(':id', imageId);
            const modalEditContent = `
                <form id="editImageForm" action="${route}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title" value="${image.title ? image.title : '' }">
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <input type="text" class="form-control" id="editDescription" name="description" value="${image.description ? image.description : '' }">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_cover" class="form-check-input" id="editIsCover" ${image.is_cover ? 'checked' : ''}>
                        <label class="form-check-label" for="editIsCover">Set as Cover</label>
                    </div>
                    <button type="submit" class="btn btn-shadow btn-warning">Save Changes</button>
                </form>
            `;
            modalBody.innerHTML = modalEditContent;
        }
    </script>

    <script>
        function openEditModal(image) {
            var editModal = new bootstrap.Modal(document.getElementById('editImageModal'));
            editModal.show();

            document.getElementById('editImageModalLabel').innerHTML = 'Edit Image Details';

            var modalBody = document.getElementById('modalBody');
            const route = "{{ route('internal.updateImage', ':id') }}".replace(':id', image.id);
            const modalEditContent = `
                <form id="editImageForm" action="${route}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <img src="${image.path}" alt="Current Image" style="max-width: 200px; max-height: 200px;">
                    </div>
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title" value="${image.title || ''}">
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <input type="text" class="form-control" id="editDescription" name="description" value="${image.description || ''}">
                    </div>
                    <div class="mb-3">
                        <label for="editIsCover" class="form-label">Set as Cover</label>
                        <input type="checkbox" class="form-check-input" id="editIsCover" name="is_cover" value="1" ${image.is_cover ? 'checked' : ''}>
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Update</button>
                </form>
            `;
            modalBody.innerHTML = modalEditContent;
        }

        function openAddDocumentModal(internalId) {
            var editModal = new bootstrap.Modal(document.getElementById('editDocumentModal'));
            editModal.show();

            document.getElementById('editDocumentModalLabel').innerHTML = 'Add Document';

            var modalBody = document.getElementById('documentModalBody');
            const route = "{{ route('internal.addDocument') }}";
            const modalAddContent = `
                <form id="addDocumentForm" action="${route}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="internal_id" value="${internalId}">
                    <div class="mb-3">
                        <label for="addDocumentInput" class="form-label">Select Document</label>
                        <input type="file" class="form-control" id="addDocumentInput" name="document" accept=".pdf" required>
                    </div>
                    <div class="mb-3">
                        <label for="addTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="addTitle" name="title">
                    </div>
                    <div class="mb-3">
                        <label for="addDescription" class="form-label">Description</label>
                        <input type="text" class="form-control" id="addDescription" name="description">
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Upload</button>
                </form>
            `;
            modalBody.innerHTML = modalAddContent;
        }

        function openEditDocumentModal(documentId, title, description) {
            var editModal = new bootstrap.Modal(document.getElementById('editDocumentModal'));
            editModal.show();

            document.getElementById('editDocumentModalLabel').innerHTML = 'Edit Document Details';

            var modalBody = document.getElementById('documentModalBody');
            const route = "{{ route('internal.updateDocument', ':id') }}".replace(':id', documentId);
            const modalEditContent = `
                <form id="editDocumentForm" action="${route}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title" value="${title}">
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <input type="text" class="form-control" id="editDescription" name="description" value="${description}">
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Update</button>
                </form>
            `;
            modalBody.innerHTML = modalEditContent;
        }
    </script>

    {{-- pengguna script --}}
    <script> // Preview uploaded image
        document.getElementById('profileImage').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) { const reader = new FileReader(); reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

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

    <script>
        var identitasChoices = [];
    </script>

    <script>
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

            var PenggunaUnitSelect = document.querySelectorAll('[data-penggunaunitkerja]');
            for (i = 0; i < PenggunaUnitSelect.length; ++i) {
            var penggunaunitelement = PenggunaUnitSelect[i];
            new Choices(penggunaunitelement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari unit penugasan/pokja',
                position: 'bottom'
            });
            }

            var LokasiSelect = document.querySelectorAll('[data-lokasi]');
            for (i = 0; i < LokasiSelect.length; ++i) {
            var lokasielement = LokasiSelect[i];
            new Choices(lokasielement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari lokasi',
                // position: 'top'
            });
            }

            var IdentitasSelect = document.querySelectorAll('[data-identitas]');


            for (i = 0; i < IdentitasSelect.length; ++i) {
                var identitasElement = IdentitasSelect[i];

                identitasChoices[i] = new Choices(identitasElement, {
                    placeholderValue: 'Pilih identitas',
                    searchPlaceholderValue: 'Cari identitas',
                    position: 'bottom',
                    removeItemButton: true,
                });
            }

            var UnitTeknisSelect = document.querySelectorAll('[data-penggunaunitteknis]');
            for (i = 0; i < UnitTeknisSelect.length; ++i) {
            var unittekniselement = UnitTeknisSelect[i];
            new Choices(unittekniselement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari Unit Pokja',
                position: 'bottom'
            });
            }

            var kategoriSelect = document.querySelectorAll('[data-kategori]');
            for (i = 0; i < kategoriSelect.length; ++i) {
            var kategorielement = kategoriSelect[i];
            new Choices(kategorielement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari kategori identitas',
                position: 'bottom'
            });
            }

            const selectLokasi = document.getElementById("lokasiSelect");
            selectLokasi.addEventListener("change", function () {
                const selectedOption = selectLokasi.options[selectLokasi.selectedIndex];

                if (selectedOption.value === null || selectedOption.value === "") {
                    // Tampilkan input teks
                    document.getElementById("otherLokasiLabel").style.display = "block";
                    document.getElementById("otherLokasiInput").style.display = "block";
                } else {
                    // Sembunyikan input teks
                    document.getElementById("otherLokasiLabel").style.display = "none";
                    document.getElementById("otherLokasiInput").style.display = "none";
                    document.getElementById("otherLokasiInput").value = "";
                }
            });

            const selectUnitTeknis = document.getElementById("penggunaunitteknisSelect");
            selectUnitTeknis.addEventListener("change", function () {
                const selectedOption = selectUnitTeknis.options[selectUnitTeknis.selectedIndex];
                if (selectedOption.value === null || selectedOption.value === "") {
                    // Tampilkan input teks
                    document.getElementById("otherUnitTeknisLabel").style.display = "block";
                    document.getElementById("otherUnitTeknisInput").style.display = "block";
                } else {
                    // Sembunyikan input teks
                    document.getElementById("otherUnitTeknisLabel").style.display = "none";
                    document.getElementById("otherUnitTeknisInput").style.display = "none";
                    document.getElementById("otherUnitTeknisInput").value = "";
                }
            })

            const selectUnitKerja = document.getElementById("penggunaunitkerjaSelect");
            selectUnitKerja.addEventListener("change", function () {
                const selectedOption = selectUnitKerja.options[selectUnitKerja.selectedIndex];
                if (selectedOption.value === null || selectedOption.value === "") {
                    // Tampilkan input teks
                    document.getElementById("otherUnitKerjaLabel").style.display = "block";
                    document.getElementById("otherUnitKerjaInput").style.display = "block";
                } else {
                    // Sembunyikan input teks
                    document.getElementById("otherUnitKerjaLabel").style.display = "none";
                    document.getElementById("otherUnitKerjaInput").style.display = "none";
                    document.getElementById("otherUnitKerjaInput").value = "";
                }
            })
        })
    </script>

        <script>
document.getElementById('mainForm').addEventListener('submit', function(e) {

    const checkbox = document.getElementById('download_after_input');

    //  IMPORTANT: only block normal submit if checkbox is CHECKED
    if (!checkbox.checked) {
        return; // <-- let Laravel handle everything normally (SweetAlert will work)
    }

    // Otherwise, use AJAX
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch(form.action, {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        }
    })
    .then(res => res.json())
    .then(result => {

        const pdfUrl = `/internal/bast/${result.id}`;
        window.open(pdfUrl, "_blank");

        // then go to show page
        window.location.href = `/internal/${result.id}`;
    });
});
</script>





@endsection
