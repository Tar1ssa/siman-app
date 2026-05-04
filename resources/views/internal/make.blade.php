@extends('app')
@section('title', $title)
@section('dependencies')
  <link href="{{asset('/assets/dist/assets/css/plugins/animate.min.css')}}" rel="stylesheet" type="text/css">
{{-- <style>
    .choices__list--dropdown {
        z-index: 5 !important;
    }
</style> --}}
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
                  <li class="breadcrumb-item" aria-current="page">Tambah Data Internal</li>
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
                        <h3>Tambah Data</h3>
                    </div>
                    <div class="card-body ">

                        <form id="mainForm" method="POST" action="{{ route('internal.insert') }}" enctype="multipart/form-data">
                            @csrf
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
                            <div class="col-md-10 col-sm-12 overflow-y-scroll" style="max-height: 60vh;">
                                <div class="tab-content" id="v-pills-tabContent">

                                    <div class="tab-pane fade show active" id="v-pills-detail" role="tabpanel" aria-labelledby="v-pills-detail-tab">

                                        <div class="mb-3" >
                                            <h4 class="fw-bold mb-3">Detail BMN</h4>
                                            <hr>
                                            <label for="satker" class="form-label">Kode Satker*</label>
                                            <select  class="form-control" name="satker_id" id="satker" value="{{ old('satker_id') }}">
                                                <option value="" disabled selected>-- Pilih kode satker --</option>
                                                @foreach ($satker as $keysatker)

                                                <option value="{{$keysatker->id}}">{{$keysatker->kode_satker}} - {{$keysatker->nama_satker}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="barang" class="form-label">Kode Barang*</label>
                                            <select

                                                class="form-control"
                                                data-barang
                                                name="barang_id"
                                                id="barangSelect"
                                                value="{{ old('barang_id') }}"
                                                >
                                                <option value="" selected disabled>--Pilih Kode Barang--</option>
                                                @foreach ($barang as $keybarang)

                                                    <option value="{{ $keybarang->id }}" >{{ $keybarang->kode_barang }} - {{ $keybarang->nama_barang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="unitkerja_id" class="form-label">Unit Kerja*</label>
                                            <select

                                                class="form-control"
                                                data-unitkerja
                                                name="unitkerja_id"
                                                id="unitkerjaSelect"
                                                value="{{ old('unitkerja_id') }}"
                                                >
                                                <option value="" selected disabled>--Pilih Unit Kerja--</option>
                                                @foreach ($unitkerja as $keyunitkerja)

                                                    <option value="{{ $keyunitkerja->id }}" >{{ $keyunitkerja->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- <div class="mb-3">
                                            <label for="nup" class="form-label"> NUP</label>
                                            <input type="number" name="nup" id="nup" class="form-control">
                                            <small>kosongkan </small>
                                        </div> --}}
                                        <div class="col-md-4 mb-3">
                                            <label for="tgl_perolehan" class="form-label">Tanggal Perolehan*</label>
                                            <input  type="date" value="{{ old('tgl_perolehan') }}" name="tgl_perolehan" id="tgl_perolehan" class="form-control">
                                        </div>
                                        <div class=" mb-3">
                                            <label for="merk" class="form-label">Merk</label>
                                            <input type="text" value="{{ old('merk') }}" name="merk" id="merk" class="form-control">
                                        </div>
                                        <div class=" mb-3">
                                            <label for="tipe" class="form-label">Tipe</label>
                                            <input type="text" value="{{ old('tipe') }}" name="tipe" id="tipe" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="jumlah" class="form-label">Jumlah*</label>
                                            <input  type="number" value="{{ old('jumlah') }}" name="jumlah" id="jumlah" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="nilai_perolehan" class="form-label">Nilai Perolehan*</label>
                                            <input  type="text" value="{{ old('nilai_perolehan') }}" name="nilai_perolehan" id="nilai_perolehan" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="kondisi" class="form-label">Kondisi*</label>
                                            <select  name="kondisi" id="kondisi" class="form-control" value="{{ old('kondisi') }}">
                                                <option value="" selected disabled>--Pilih Kondisi--</option>
                                                <option value="B">Baik</option>
                                                <option value="RR">Rusak Ringan</option>
                                                <option value="RB">Rusak Berat</option>
                                            </select>
                                        </div>
                                        {{-- <div class="col-md-4 mb-3">
                                            <label for="akun_neraca" class="form-label">Akun Neraca</label>
                                            <input type="text" name="akun_neraca" id="akun_neraca" class="form-control">
                                        </div> --}}
                                        <div class="col-md-4 mb-3">
                                            <label for="pembukuan" class="form-label">Pembukuan</label>
                                            <select name="pembukuan" id="pembukuan" class="form-control" value="{{ old('pembukuan') }}">
                                                <option value="" selected disabled>--Pilih Pembukuan--</option>
                                                <option value="Perolehan APBN">Perolehan APBN</option>
                                                <option value="Hibah">Hibah</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="lokasi_id" class="form-label">Lokasi/Ruang*</label>
                                            <select

                                                class="form-control"
                                                data-lokasi
                                                name="lokasi_id"
                                                id="lokasiSelect"
                                                value="{{ old('lokasi_id') }}"
                                                >
                                                <option value="" selected disabled>--Pilih Lokasi/Ruang--</option>
                                                @foreach ($lokasi as $keylokasi)

                                                    <option value="{{ $keylokasi->id }}" >{{ $keylokasi->unitKerja->name }} - {{ $keylokasi->name }}</option>
                                                @endforeach
                                                <option id="other-lokasi" value="">Lainnya</option>
                                            </select>
                                            <label for="otherLokasiInput" id="otherLokasiLabel" class="form-label" style="display: none;">Keterangan Lokasi</label>
                                            <input name="ketLokasi" value="{{ old('ketLokasi') }}" type="text" id="otherLokasiInput" class="form-control mt-2" placeholder="Masukkan keterangan lokasi" style="display: none;">
                                        </div>
                                        <div class="mb-3">
                                            <label for="is_borrowed" class="form-label">Status Peminjaman</label>
                                            <select name="is_borrowed" id="is_borrowed" class="form-control" value="{{ old('is_borrowed') }}">
                                                <option value="" selected disabled>--Pilih Status Peminjaman--</option>
                                                <option value="">Tidak Dipinjam</option>
                                                <option value="1">Dipinjam</option>
                                                <option value="2">Sudah Dikembalikan</option>
                                            </select>
                                        </div>
                                        <div class="mb-3" style="height: 20vh"></div>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-foto" role="tabpanel" aria-labelledby="v-pills-foto-tab">

                                        <h4 class="fw-bold mb-3">Foto</h4>
                                        <hr>

                                        <form id="imageForm" enctype="multipart/form-data" class="mb-3">
                                            <div class="row mb-3">

                                                <div class="col-md-6">

                                                    <input type="file" id="imageInput" accept="image/*" class="form-control">
                                                </div>
                                                <div class="col-md-6">

                                                    <button type="button" id="addImage" class="btn btn-shadow btn-primary">Tambah Foto</button>
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
                                                <tbody></tbody>
                                            </table>

                                        </form>
                                        <div class="mb-3">

                                            <label for="link_dokumentasi">Link Dokumentasi</label>
                                            <input type="text" value="{{ old('link_dokumentasi') }}" name="link_dokumentasi" id="link_dokumentasi" class="form-control">
                                        </div>
                                        <div class="mb-3" style="height: 5vh"></div>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-dokumen" role="tabpanel" aria-labelledby="v-pills-dokumen-tab">

                                        <h4 class="fw-bold mb-3">Dokumen</h4>
                                        <hr>

                                        <form id="documentForm" enctype="multipart/form-data" class="mb-3">
                                            <div class="row mb-3">

                                                <div class="col-md-6">

                                                    <input type="file" id="documentInput" accept=".pdf" class="form-control">
                                                </div>
                                                <div class="col-md-6">

                                                    <button type="button" id="addDocument" class="btn btn-shadow btn-primary">Tambah Dokumen</button>
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
                                                <tbody></tbody>
                                            </table>

                                        </form>
                                        <div class="mb-3" style="height: 5vh"></div>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-pengguna" role="tabpanel" aria-labelledby="v-pills-pengguna-tab">

                                        <h4 class="fw-bold mb-3">Pengguna</h4>
                                        <hr>

                                        <!-- Image Upload -->
                                        {{-- <div class="mb-3">
                                            <label for="profileImage" class="form-label">Upload Foto Pengguna</label>
                                            <input class="form-control" type="file" id="profileImage" accept="image/*" name="profileImage">
                                        </div>
                                        <div class="mt-3 mb-3">
                                            <img id="preview" src="" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; display:none;">
                                        </div> --}}

                                        <!-- Name -->
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nama Lengkap</label>
                                            <input type="text" value="{{ old('nama_pengguna') }}" name="name" class="form-control" id="name" placeholder="Masukkan nama lengkap">
                                        </div> <!-- Address -->
                                        <div class="mb-3">
                                            <label for="pengguna_unitkerja_id" class="form-label">Unit Penugasan Eselon 2</label>
                                            <select
                                                class="form-control"
                                                data-penggunaunitkerja
                                                name="pengguna_unitkerja_id"
                                                id="penggunaunitkerjaSelect"
                                                value="{{ old('pengguna_unitkerja_id') }}"
                                                >
                                                <option value="" selected disabled>--Pilih Unit Penugasan Eselon 2--</option>
                                                @foreach ($unitkerja as $keyunitkerja)

                                                    <option value="{{ $keyunitkerja->id }}" >{{ $keyunitkerja->name }}</option>
                                                @endforeach
                                                <option id="other-unitkerja" value="">Lainnya</option>
                                            </select>
                                            <label for="otherUnitKerjaInput" id="otherUnitKerjaLabel" class="form-label" style="display: none;">Keterangan Unit Penugasan Eselon 2</label>
                                            <input type="text" value="{{ old('ket_penugasan') }}" name="ket_penugasan" id="otherUnitKerjaInput" class="form-control" placeholder="Masukkan Keterangan Unit Penugasan Eselon 2" style="display: none;">
                                        </div>

                                        <div class="mb-3">
                                            <label for="unit_teknis_id" class="form-label">Unit Pokja</label>
                                            <select
                                                class="form-control"
                                                data-penggunaunitteknis
                                                name="unit_teknis_id"
                                                id="penggunaunitteknisSelect"
                                                value ="{{ old('unit_teknis_id') }}"
                                                >
                                                <option value="" selected disabled>--Pilih Unit Pokja--</option>
                                                @foreach ($unitteknis as $keyunitteknis)

                                                    <option value="{{ $keyunitteknis->id }}" >{{ $keyunitteknis->name }}</option>
                                                @endforeach
                                                <option id="other-unitteknis" value="">Lainnya</option>
                                            </select>
                                            <label for="otherUnitTeknisInput" id="otherUnitTeknisLabel" class="form-label" style="display: none;">Keterangan Unit Pokja</label>
                                            <input type="text" value="{{ old('ket_unit_teknis') }}" name="ket_unit_teknis" id="otherUnitTeknisInput" class="form-control" placeholder="Masukkan Keterangan Unit Pokja" style="display: none;">
                                        </div>

                                            <div class="mb-3">
                                                <label for="nip_pengguna" class="form-label">NIP</label>
                                                <input type="text" value="{{ old('nip_pengguna') }}" name="nip_pengguna" id="nip_pengguna" class="form-control" placeholder="Masukkan NIP">
                                            </div>

                                        <div class="mb-3">
                                            <label for="jabatan_pengguna" class="form-label">Jabatan</label>
                                            <input type="text" value="{{ old('jabatan_pengguna') }}" name="jabatan_pengguna" id="jabatan_pengguna" class="form-control" placeholder="Masukkan jabatan">
                                        </div>

                                        <div class="mb-3">
                                            <label for="alamat_pengguna" class="form-label">Alamat</label>
                                            <textarea name="alamat_pengguna" id="alamat_pengguna" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat_pengguna') }}</textarea>
                                        </div>
                                        <div class="mb-3" style="height: 5vh"></div>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-identitas" role="tabpanel" aria-labelledby="v-pills-identitas-tab">

                                        <h4 class="fw-bold mb-3">Identitas</h4>
                                        <hr>

                                        <div class="mb-3">
                                            <label for="identitas_kategori" class="form-label">Kategori identitas*</label>
                                            <select data-kategori value="{{ old('kategori_id') }}" class="form-control" id="identitas_kategori" name="kategori_id">
                                                <option value="">-- Pilih Kategori --</option>
                                                @foreach($identitasKategori as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">

                                            <label class="form-label">identitas*</label>
                                            <select  data-identitas value="{{ old('identitas_id') }}" class="form-control" id="identitas" name="identitas_id">
                                                {{-- <option value="">-- Pilih identitas --</option> --}}
                                                {{-- @foreach($identitas as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach  --}}
                                            </select>
                                        </div>

                                        <div class="mb-3">

                                            <div id="dynamic-fields"></div>
                                        </div>


                                        <div class="mb-3" style="height: 5vh"></div>

                                    </div>

                                    <div class="tab-pane fade" id="v-pills-bast" role="tabpanel" aria-labelledby="v-pills-bast-tab">

                                        <h4 class="fw-bold mb-3">BAST</h4>
                                        <hr>

                                        <div class="mb-3">
                                            <label for="download_after_input" class="form-label">Download setelah input</label>
                                            <input type="checkbox" name="download_after_input" id="download_after_input" value="1" class="form-check-input" {{ old('download_after_input') ? 'checked' : '' }}>
                                        </div>

                                        <div class="mb-3">
                                            <label for="nama_pihak_pertama" class="form-label">Nama Pihak Pertama</label>
                                            <input type="text" value="{{ old('nama_pihak_pertama') }}" name="nama_pihak_pertama" id="nama_pihak_pertama" class="form-control" placeholder="Masukkan nama pihak pertama">
                                        </div>
                                        <div class="mb-3">
                                            <label for="nip_pihak_pertama" class="form-label">NIP Pihak Pertama</label>
                                            <input type="text" value="{{ old('nip_pihak_pertama') }}" name="nip_pihak_pertama" id="nip_pihak_pertama" class="form-control" placeholder="Masukkan NIP pihak pertama">
                                        </div>
                                        <div class="mb-3">
                                            <label for="jabatan_pihak_pertama" class="form-label">Jabatan Pihak Pertama</label>
                                            <input type="text" value="{{ old('jabatan_pihak_pertama') }}" name="jabatan_pihak_pertama" id="jabatan_pihak_pertama" class="form-control" placeholder="Masukkan jabatan pihak pertama">
                                        </div>
                                        <div class="mb-3">
                                            <label for="alamat_pihak_pertama" class="form-label">Alamat Pihak Pertama</label>
                                            <textarea name="alamat_pihak_pertama" id="alamat_pihak_pertama" class="form-control" rows="3" placeholder="Masukkan alamat lengkap pihak pertama">{{ old('alamat_pihak_pertama') }} </textarea>
                                        </div>
                                        <div class="mb-3" style="height: 5vh"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                        <hr>
                        <button type="submit" form="mainForm" id="submitAll" class="btn btn-shadow btn-success mt-3">Submit All</button>
                        <a href="{{ route('internal.index') }}" class="btn btn-shadow btn-secondary mt-3">Kembali</a>
                    </form>
                </div>

            </div>
        </div>
</div>

    <script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{asset('/assets/autonumeric/dist/autoNumeric.min.js')}}"></script>



    <script>
        document.addEventListener('change', function (e) {
            if (e.target.id === 'identitas_kategori') {
                const kategoriId = e.target.value;

                const container = document.getElementById('dynamic-fields');
                container.innerHTML = '';
                const identitasSelect = document.getElementById('identitas');
                // identitasSelect.innerHTML = '<option value="" disabled selected>-- Pilih identitas --</option>';

                if (kategoriId) {
                    fetch(`/identitas/bykategori/${kategoriId}`)
                        .then(res => res.json())
                        .then(data => {
                            data.forEach(identitas => {
                                identitasChoices[0].clearChoices();
                                identitasChoices[0].removeActiveItems();

                                const newOptions = data.map(item => ({
                                    value: item.id,
                                    label: item.name
                                }));

                                identitasChoices[0].setChoices(newOptions, 'value', 'label', true);
                            });
                        });
                }
            }
        });
    </script>

    {{-- identitas script --}}
    <script>
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


    {{-- Image Upload Script --}}
    <script>
        let tempImages = [];
        let tempDocuments = [];

        document.getElementById('addImage').addEventListener('click', function() {
            const input = document.getElementById('imageInput');
            if (input.files.length > 0) {
                const file = input.files[0];
                tempImages.push({
                    file: file,
                    title: '',
                    description: '',
                    isCover: false
                });
                renderTable();
            }
        });

        document.getElementById('addDocument').addEventListener('click', function() {
            const input = document.getElementById('documentInput');
            if (input.files.length > 0) {
                const file = input.files[0];
                tempDocuments.push({
                    file: file,
                    title: '',
                    description: ''
                });
                renderDocumentTable();
            }
        });

        function renderTable() {
            const tbody = document.querySelector('#imageTable tbody');
            tbody.innerHTML = '';

            tempImages.forEach((item, index) => {
                const row = `
                    <tr>
                        <td><img class="img-thumbnail" src="${URL.createObjectURL(item.file)}" width="100"></td>
                        <td style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word;">${item.file.name}</td>
                        <td><input class="form-control" type="text" value="${item.title}"
                                oninput="updateTitle(${index}, this.value)"></td>
                        <td><input class="form-control" type="text" value="${item.description}"
                                oninput="updateDescription(${index}, this.value)"></td>
                        <td>
                            <input type="radio" name="cover" class="form-check-input"
                                ${item.isCover ? 'checked' : ''}
                                onclick="setCover(${index})">
                        </td>
                        <td>
                            <button onclick="deleteImage(${index})" class="btn btn-shadow btn-danger">Delete</button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        function renderDocumentTable() {
            const tbody = document.querySelector('#documentTable tbody');
            tbody.innerHTML = '';

            tempDocuments.forEach((item, index) => {
                const row = `
                    <tr>
                        <td style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word;">${item.file.name}</td>
                        <td><input class="form-control" type="text" value="${item.title}"
                                oninput="updateDocumentTitle(${index}, this.value)"></td>
                        <td><input class="form-control" type="text" value="${item.description}"
                                oninput="updateDocumentDescription(${index}, this.value)"></td>
                        <td>
                            <button onclick="deleteDocument(${index})" class="btn btn-shadow btn-danger">Delete</button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        function updateTitle(index, value) {
            tempImages[index].title = value;
        }

        function updateDescription(index, value) {
            tempImages[index].description = value;
        }

        function setCover(index) {
            tempImages.forEach((img, i) => img.isCover = (i === index));
            renderTable();
        }

        function deleteImage(index) {
            tempImages.splice(index, 1);
            renderTable();
        }

        function updateDocumentTitle(index, value) {
            tempDocuments[index].title = value;
        }

        function updateDocumentDescription(index, value) {
            tempDocuments[index].description = value;
        }

        function deleteDocument(index) {
            tempDocuments.splice(index, 1);
            renderDocumentTable();
        }

        document.getElementById('imageForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Instead of submitting, just add to tempImages
            // The main form will handle submission
        });
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
            document.getElementById('profileForm').addEventListener('submit', function(e) {
                e.preventDefault();
                alert("Form submitted!\nName: " +
                document.getElementById('name').value +
                "\nAddress: " +
                document.getElementById('address').value);
            });
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

            // Main form submit
            document.getElementById('mainForm').addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default to handle manually

                const checkbox = document.getElementById('download_after_input');
                const formData = new FormData(this);

                // Append images
                tempImages.forEach((item, index) => {
                    formData.append(`images[${index}]`, item.file);
                    formData.append(`titles[${index}]`, item.title);
                    formData.append(`descriptions[${index}]`, item.description);
                    formData.append(`isCover[${index}]`, item.isCover ? 1 : 0);
                });

                // Append documents
                tempDocuments.forEach((item, index) => {
                    formData.append(`documents[${index}]`, item.file);
                    formData.append(`documentTitles[${index}]`, item.title);
                    formData.append(`documentDescriptions[${index}]`, item.description);
                });

                // Submit via fetch
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (!result.success) {
                        // Handle validation errors
                        if (result.errors) {
                            let errorMessages = [];
                            for (let field in result.errors) {
                                errorMessages.push(...result.errors[field]);
                            }
                            throw new Error(errorMessages.join('\n'));
                        } else {
                            throw new Error(result.message || 'Gagal menyimpan data');
                        }
                    }

                    const id = result.id;

                    Swal.fire({
                        title: "Sukses",
                        text: "Data berhasil disimpan",
                        icon: "success",
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => {

                        if (checkbox.checked) {
                            const pdfUrl = `/internal/bast/${id}`;
                            window.open(pdfUrl, "_blank");
                        }

                        // Redirect AFTER alert closes
                        window.location.href = `/internal/${id}`;
                    });
                })
                .catch(error => {

                    let msg = error.message;

                    if (msg.includes("No query results for model")) {
                        msg = "Data tidak ditemukan. Silakan periksa kembali pilihan identitas.";
                    }
                    else if (msg.includes("419")) {
                        msg = "Sesi Anda habis. Silakan refresh halaman dan coba lagi.";
                    }
                    // else {
                    //     msg = "Terjadi kesalahan sistem. Silakan coba lagi.";
                    // }

                    Swal.fire({
                        title: "Gagal",
                        text: msg,
                        icon: "error"
                    });
                });

            });

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
                searchPlaceholderValue: 'Cari unit kerja',
                position: 'auto'

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

            var KategoriSelect = document.querySelectorAll('[data-kategori]');
            for (i = 0; i < KategoriSelect.length; ++i) {
            var kategElement = KategoriSelect[i];
            new Choices(kategElement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari kategori identitas',
                position: 'bottom',
                });
            }

            var UnitTeknisSelect = document.querySelectorAll('[data-penggunaunitteknis]');
            for (i = 0; i < UnitTeknisSelect.length; ++i) {
            var unitelement = UnitTeknisSelect[i];
            new Choices(unitelement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari Unit Pokja',
                position: 'bottom'

            });
            }

            var UnitPenggunaSelect = document.querySelectorAll('[data-penggunaunitkerja]');
            for (i = 0; i < UnitPenggunaSelect.length; ++i) {
            var unitelement = UnitPenggunaSelect[i];
            new Choices(unitelement, {
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





@endsection
