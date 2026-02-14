@extends('app')
@section('dependencies')
  <link href="../assets/css/plugins/animate.min.css" rel="stylesheet" type="text/css">
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

                    <form method="POST" action="{{ route('internal.update', $internal->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
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
                                                <label for="akun_neraca" class="form-label">Akun Neraca</label>
                                                <input type="text" name="akun_neraca" id="akun_neraca" class="form-control" value="{{ $internal->akun_neraca }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="pembukuan" class="form-label">Pembukuan</label>
                                                <input type="text" name="pembukuan" id="pembukuan" class="form-control" value="{{ $internal->pembukuan }}">
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
                                                </select>
                                            </div>
                                            <div class="mb-3" style="height: 20vh"></div>
                                        </div>
                                        <div class="tab-pane fade" id="v-pills-foto" role="tabpanel" aria-labelledby="v-pills-foto-tab">
                                                <h4 class="fw-bold mb-3">Foto</h4>
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
                                                                <td>
                                                                    <button type="button" onclick="openEditModal({{ $image }})" class="btn btn-shadow btn-warning">Edit</button>
                                                                    <button class="btn btn-shadow btn-danger" type="submit"
                                                                    onclick="return confirm('Yakin ingin menghapus gambar {{ $image->title }} ?')"
                                                                    form="delete-image-{{ $image->id }}"
                                                                    >Delete
                                                                    </button>

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
                                        <div class="tab-pane fade" id="v-pills-pengguna" role="tabpanel" aria-labelledby="v-pills-pengguna-tab">
                                            <h4 class="fw-bold mb-3">Pengguna</h4>
                                            <!-- Image Upload -->
                                            <div class="mb-3">
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
                                            </div>

                                            <!-- Name -->
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control" id="name" placeholder="Masukkan nama lengkap" value="{{ $internal->nama_pengguna }}">
                                            </div> <!-- Address -->
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Alamat</label>
                                                <textarea name="address" class="form-control" id="address" rows="3" placeholder="Masukkan alamat">{{ $internal->alamat_pengguna }}</textarea>
                                            </div>
                                            <div class="mb-3" style="height: 5vh"></div>
                                        </div>
                                        <div class="tab-pane fade" id="v-pills-identitas" role="tabpanel" aria-labelledby="v-pills-identitas-tab">
                                            <h4 class="fw-bold mb-3">Identitas</h4>
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
                                                                    
                                                                    value="{{ $dataAtribut[$attr->id]->value_string ?? '' }}">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
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

    <script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{asset('https://cdn.jsdelivr.net/npm/autonumeric@4.6.0')}}"></script>

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

    <script>
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
                        <input type="text" class="form-control" id="editTitle" name="title" value="${image.title}">
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <input type="text" class="form-control" id="editDescription" name="description" value="${image.description}">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="isCover" class="form-check-input" id="editIsCover" ${image.is_cover ? 'checked' : ''}>
                        <label class="form-check-label" for="editIsCover">Set as Cover</label>
                    </div>
                    <button type="submit" class="btn btn-shadow btn-warning">Save Changes</button>
                </form>
            `;
            modalBody.innerHTML = modalEditContent;
        }
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
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="isCover" class="form-check-input" id="addIsCover">
                        <label class="form-check-label" for="addIsCover">Set as Cover</label>
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Upload</button>
                </form>
            `;
            modalBody.innerHTML = modalAddContent;
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
        document.addEventListener('DOMContentLoaded', function () {



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
                searchPlaceholderValue: 'Cari lokasi',
                position: 'top'
            });
            }

            var IdentitasSelect = document.querySelectorAll('[data-identitas]');
            for (i = 0; i < IdentitasSelect.length; ++i) {
            var identitasElement = IdentitasSelect[i];
            new Choices(identitasElement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari identitas'
                
            });
            }
        })
    </script>





@endsection
