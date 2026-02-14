@extends('app')
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
                        <div class="row g-4" style="height: 50vh; overflow-y: hidden;">
                            <div class="col-md-2 col-sm-12  border-end border-muted">
                                <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <li><a class="nav-link active" id="v-pills-detail-tab" data-bs-toggle="pill" href="#v-pills-detail" role="tab" aria-controls="v-pills-detail" aria-selected="true">Detail BMN</a></li>
                                    <li><a class="nav-link" id="v-pills-foto-tab" data-bs-toggle="pill" href="#v-pills-foto" role="tab" aria-controls="v-pills-foto" aria-selected="false">Foto</a></li>
                                    <li><a class="nav-link" id="v-pills-pengguna-tab" data-bs-toggle="pill" href="#v-pills-pengguna" role="tab" aria-controls="v-pills-pengguna" aria-selected="false">Pengguna</a></li>
                                    <li><a class="nav-link" id="v-pills-identitas-tab" data-bs-toggle="pill" href="#v-pills-identitas" role="tab" aria-controls="v-pills-identitas" aria-selected="false">Identitas</a></li>
                                </ul>
                            </div>
                            <div class="col-md-10 col-sm-12 overflow-y-scroll" style="max-height: 50vh; ">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade show active" id="v-pills-detail" role="tabpanel" aria-labelledby="v-pills-detail-tab">
                                        <div class="mb-3" >
                                            <h4 class="fw-bold mb-3">Detail BMN</h4>
                                            <label for="satker" class="form-label">Kode Satker</label>
                                            <select class="form-control" name="satker_id" id="satker">
                                                <option value="" disabled selected>-- Pilih kode satker --</option>
                                                @foreach ($satker as $keysatker)

                                                <option value="{{$keysatker->id}}">{{$keysatker->kode_satker}} - {{$keysatker->nama_satker}}</option>
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

                                                    <option value="{{ $keybarang->id }}" >{{ $keybarang->kode_barang }} - {{ $keybarang->nama_barang }}</option>
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
                                            <label for="tgl_perolehan" class="form-label">Tanggal Perolehan</label>
                                            <input type="date" name="tgl_perolehan" id="tgl_perolehan" class="form-control">
                                        </div>
                                        <div class=" mb-3">
                                            <label for="merk" class="form-label">Merk</label>
                                            <input type="text" name="merk" id="merk" class="form-control">
                                        </div>
                                        <div class=" mb-3">
                                            <label for="tipe" class="form-label">Tipe</label>
                                            <input type="text" name="tipe" id="tipe" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="jumlah" class="form-label">Jumlah</label>
                                            <input type="number" name="jumlah" id="jumlah" class="form-control" >
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="nilai_perolehan" class="form-label">Nilai Perolehan</label>
                                            <input type="text" name="nilai_perolehan" id="nilai_perolehan" class="form-control" >
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="kondisi" class="form-label">Kondisi</label>
                                            <select name="kondisi" id="kondisi" class="form-control">
                                                <option value="" selected disabled>--Pilih Kondisi--</option>
                                                <option value="B">Baik</option>
                                                <option value="RR">Rusak Ringan</option>
                                                <option value="RB">Rusak Berat</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="akun_neraca" class="form-label">Akun Neraca</label>
                                            <input type="text" name="akun_neraca" id="akun_neraca" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="pembukuan" class="form-label">Pembukuan</label>
                                            <input type="text" name="pembukuan" id="pembukuan" class="form-control">
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

                                                    <option value="{{ $keylokasi->id }}" >{{ $keylokasi->unitKerja->name }} - {{ $keylokasi->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3" style="height: 20vh"></div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-foto" role="tabpanel" aria-labelledby="v-pills-foto-tab">
                                        <h4 class="fw-bold mb-3">Foto</h4>

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
                                            <input type="text" name="link_dokumentasi" id="link_dokumentasi" class="form-control">
                                        </div>
                                        <div class="mb-3" style="height: 5vh"></div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-pengguna" role="tabpanel" aria-labelledby="v-pills-pengguna-tab">
                                        <h4 class="fw-bold mb-3">Pengguna</h4>

                                        <!-- Image Upload -->
                                        <div class="mb-3">
                                            <label for="profileImage" class="form-label">Upload Foto Pengguna</label>
                                            <input class="form-control" type="file" id="profileImage" accept="image/*" name="profileImage">
                                        </div>
                                        <div class="mt-3 mb-3">
                                            <img id="preview" src="" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; display:none;">
                                        </div>

                                        <!-- Name -->
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nama Lengkap</label>
                                            <input type="text" name="name" class="form-control" id="name" placeholder="Masukkan nama lengkap">
                                        </div> <!-- Address -->
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Alamat</label>
                                            <textarea name="address" class="form-control" id="address" rows="3" placeholder="Masukkan alamat"></textarea>
                                        </div>
                                        <div class="mb-3" style="height: 5vh"></div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-identitas" role="tabpanel" aria-labelledby="v-pills-identitas-tab">
                                        <h4 class="fw-bold">Identitas</h4>
                                        <label class="form-label">identitas</label>
                                        <select class="form-control" id="identitas" name="identitas_id">
                                            <option value="">-- Select --</option>
                                            @foreach($identitas as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>

                                        <div id="dynamic-fields"></div>
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
    <script src="{{asset('https://cdn.jsdelivr.net/npm/autonumeric@4.6.0')}}"></script>

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
                <div>
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
        document.addEventListener('DOMContentLoaded', function () {

            // Main form submit
            document.getElementById('mainForm').addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default to handle manually

                const formData = new FormData(this);

                // Append images
                tempImages.forEach((item, index) => {
                    formData.append(`images[${index}]`, item.file);
                    formData.append(`titles[${index}]`, item.title);
                    formData.append(`descriptions[${index}]`, item.description);
                    formData.append(`isCover[${index}]`, item.isCover ? 1 : 0);
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
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            try {
                                const data = JSON.parse(text);
                                throw new Error(data.message || 'Validation error');
                            } catch (e) {
                                if (e instanceof SyntaxError) {
                                    throw new Error('Server error: ' + response.status);
                                } else {
                                    throw e;
                                }
                            }
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'Unknown error',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while submitting the form: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });

            var modalBarang = document.querySelectorAll('[data-barang]');
            for (i = 0; i < modalBarang.length; ++i) {
            var elementBarang = modalBarang[i];
            new Choices(elementBarang, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari kode barang',
                position: 'auto'
            
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
    </script>





@endsection
