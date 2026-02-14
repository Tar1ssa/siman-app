@extends('app')
@section('dependencies')
  <link href="../assets/css/plugins/animate.min.css" rel="stylesheet" type="text/css">

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
                        <a href="{{route('internal.edit', $internal->id)}}" class="btn btn-shadow btn-warning">Edit Data BMN</a>
                    </div>
                    <div class="card-body ">

                        {{-- <form id="mainForm" method="POST" action="{{ route('internal.update', $internal->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT') --}}
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
                                            <input readonly type="date" name="tgl_perolehan" id="tgl_perolehan" class="form-control" value="{{ \Carbon\Carbon::parse($internal->tgl_perolehan)->format('Y-m-d') }}">
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
                                            </select>
                                        </div>
                                        <div class="mb-3" style="height: 20vh"></div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-foto" role="tabpanel" aria-labelledby="v-pills-foto-tab">
                                        {{-- <form id="imageForm" enctype="multipart/form-data" class="mb-3"> --}}
                                            <h4 class="fw-bold mb-3">Foto</h4>
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
                                    <div class="tab-pane fade" id="v-pills-pengguna" role="tabpanel" aria-labelledby="v-pills-pengguna-tab">
                                        <h4 class="fw-bold mb-3">Pengguna</h4>
                                        <!-- Image Upload -->
                                        <div class="mb-3">
                                            <label for="profileImage" class="form-label">Foto Pengguna</label>
                                            {{-- <input class="form-control" type="file" id="profileImage" accept="image/*" name="profileImage" > --}}
                                        </div>
                                        <div class="mt-3 mb-3">
                                            @if (empty($internal->profile_image))
                                                    <h5>tidak ada foto</h5>
                                                @else
                                                    <img id="preview" src="{{ asset( $internal->profile_image_path) }}" alt="Image Preview" class="img-thumbnail" style="max-width: 200px;">
                                                @endif
                                        </div>

                                        <!-- Name -->
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nama Lengkap</label>
                                            <input readonly type="text" name="name" class="form-control" id="name" placeholder="Masukkan nama lengkap" value="{{ $internal->nama_pengguna }}">
                                        </div> <!-- Address -->
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Alamat</label>
                                            <textarea readonly name="address" class="form-control" id="address" rows="3" placeholder="Masukkan alamat">{{ $internal->alamat_pengguna }}</textarea>
                                        </div>
                                        <div class="mb-3" style="height: 5vh"></div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-identitas" role="tabpanel" aria-labelledby="v-pills-identitas-tab">
                                        <h4 class="fw-bold">Identitas</h4><br>
                                        <h5 class="fw-bold">{{ $internal->identitas->name ?? 'Tidak ada identitas' }}</h5>
                                        <ul class="list-group list-group-flush">
                                            @foreach($internal->dataAtribut as $val)
                                                <li class="list-group-item list-group-item-action" >
                                                    {{ $val->atribut->label }} :
                                                    {{ $val->value_string ?? $val->value_integer ?? $val->value_date }}
                                                </li>
                                            @endforeach
                                        </ul>
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
    <script src="{{asset('https://cdn.jsdelivr.net/npm/autonumeric@4.6.0')}}"></script>

    {{-- Script for Image Modal --}}
    {{-- <script>
        function openEditModal(image) {
            var editModal = new bootstrap.Modal(document.getElementById('editImageModal'));
            editModal.show();

            var modalBody = document.getElementById('modalBody');
            const imageId = image.id; // Assuming 'image' object has an 'id' property
            const route = "{{ route('internal.updateImage', ':id') }}".replace(':id', imageId);
            const modalEditContent = `
                <form id="editImageForm" action="${route}" method="POST">
                  <div class="mb-3">
                    <label for="editTitle" class="form-label">Title</label>
                    <input type="text" class="form-control" id="editTitle" name="title">
                  </div>
                  <div class="mb-3">
                    <label for="editDescription" class="form-label">Description</label>
                    <input type="text" class="form-control" id="editDescription" name="description">
                  </div>
                  <div class="mb-3 form-check">
                    <input type="checkbox" name="isCover" class="form-check-input" id="editIsCover">
                    <label class="form-check-label" for="editIsCover">Set as Cover</label>
                  </div>
                </form>
            `;
            modalBody.innerHTML = modalEditContent;
        }
    </script> --}}

    {{-- Script for Add Image Modal --}}
    {{-- <script>
        function openAddModal(internalId) {
            var addModal = new bootstrap.Modal(document.getElementById('editImageModal'));
            addModal.show();

            const internalId = internalId;
            var modalBody = document.getElementById('modalBody');
            const route = "{{ route('internal.addImage', ':id') }}".replace(':id', internalId);
            const modalAddContent = `
                <form id="addImageForm" action="${route}" method="POST" enctype="multipart/form-data">
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
                <div class="mb-3 form-check">
                    <input type="checkbox" name="isCover" class="form-check-input" id="addIsCover">
                    <label class="form-check-label" for="addIsCover">Set as Cover</label>
                </div>
                </form>
            `;
            modalBody.innerHTML = modalAddContent;
        }
    </script> --}}

    {{-- Image Upload Script --}}
    <script>
        // let tempImages = [];

        // document.getElementById('addImage').addEventListener('click', function() {
        //     const input = document.getElementById('imageInput');
        //     if (input.files.length > 0) {
        //         const file = input.files[0];
        //         tempImages.push({
        //             file: file,
        //             title: '',
        //             description: '',
        //             isCover: false
        //         });
        //         renderTable();
        //     }
        // });

        // function renderTable() {
        //     const tbody = document.querySelector('#imageTable tbody');
        //     tbody.innerHTML = '';

        //     tempImages.forEach((item, index) => {
        //         const row = `
        //             <tr>
        //                 <td><img class="img-thumbnail" src="${URL.createObjectURL(item.file)}" width="100"></td>
        //                 <td style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word;">${item.file.name}</td>
        //                 <td><input class="form-control" type="text" value="${item.title}"
        //                         oninput="updateTitle(${index}, this.value)"></td>
        //                 <td><input class="form-control" type="text" value="${item.description}"
        //                         oninput="updateDescription(${index}, this.value)"></td>
        //                 <td>
        //                     <input type="radio" name="cover" class="form-check-input"
        //                         ${item.isCover ? 'checked' : ''}
        //                         onclick="setCover(${index})">
        //                 </td>
        //                 <td>
        //                     <button onclick="deleteImage(${index})" class="btn btn-shadow btn-danger">Delete</button>
        //                 </td>
        //             </tr>
        //         `;
        //         tbody.innerHTML += row;
        //     });
        // }

        // function updateTitle(index, value) {
        //     tempImages[index].title = value;
        // }

        // function updateDescription(index, value) {
        //     tempImages[index].description = value;
        // }

        // function setCover(index) {
        //     tempImages.forEach((img, i) => img.isCover = (i === index));
        //     renderTable();
        // }

        // function deleteImage(index) {
        //     tempImages.splice(index, 1);
        //     renderTable();
        // }

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
        document.addEventListener('DOMContentLoaded', function () {

            // Main form submit
            // document.getElementById('mainForm').addEventListener('submit', function(e) {
            //     e.preventDefault(); // Prevent default to handle manually

            //     const formData = new FormData(this);

            //     // Append images
            //     tempImages.forEach((item, index) => {
            //         formData.append(`images[${index}]`, item.file);
            //         formData.append(`titles[${index}]`, item.title);
            //         formData.append(`descriptions[${index}]`, item.description);
            //         formData.append(`isCover[${index}]`, item.isCover ? 1 : 0);
            //     });

            //     // Submit via fetch
            //     fetch(this.action, {
            //         method: 'POST',
            //         body: formData,
            //         headers: {
            //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            //             'Accept': 'application/json',
            //             'X-Requested-With': 'XMLHttpRequest'
            //         }
            //     })
            //     .then(response => {
            //         if (response.ok) {
            //             return response.json();
            //         } else {
            //             return response.text().then(text => {
            //                 try {
            //                     const data = JSON.parse(text);
            //                     throw new Error(data.message || 'Validation error');
            //                 } catch (e) {
            //                     if (e instanceof SyntaxError) {
            //                         throw new Error('Server error: ' + response.status);
            //                     } else {
            //                         throw e;
            //                     }
            //                 }
            //             });
            //         }
            //     })
            //     .then(data => {
            //         if (data.success) {
            //             window.location.href = data.redirect;
            //         } else {
            //             Swal.fire({
            //                 title: 'Error',
            //                 text: data.message || 'Unknown error',
            //                 icon: 'error',
            //                 confirmButtonText: 'OK'
            //             });
            //         }
            //     })
            //     .catch(error => {
            //         Swal.fire({
            //             title: 'Error',
            //             text: 'An error occurred while submitting the form: ' + error.message,
            //             icon: 'error',
            //             confirmButtonText: 'OK'
            //         });
            //     });
            // });

            var modalBarang = document.querySelectorAll('[data-barang]');
            for (i = 0; i < modalBarang.length; ++i) {
            var elementBarang = modalBarang[i];
            new Choices(elementBarang, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari kode barang'
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

            var LokasiSelect = document.querySelectorAll('[data-lokasi]');
            for (i = 0; i < LokasiSelect.length; ++i) {
            var lokasielement = LokasiSelect[i];
            new Choices(lokasielement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari lokasi'
            });
            }
        })
    </script>





@endsection
