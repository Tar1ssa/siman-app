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
                    <div class="card-body">

                        <div class="row g-4">
                            <div class="col-md-2 col-sm-12  border-end border-muted">
                                <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <li><a class="nav-link active" id="v-pills-detail-tab" data-bs-toggle="pill" href="#v-pills-detail" role="tab" aria-controls="v-pills-detail" aria-selected="true">Detail BMN</a></li>
                                    <li><a class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Profile</a></li>
                                    <li><a class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Messages</a></li>
                                    <li><a class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">Settings</a></li>
                                </ul>
                            </div>
                            <div class="col-md-10 col-sm-12">
                                <div class="tab-content" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-detail" role="tabpanel" aria-labelledby="v-pills-detail-tab">
                                    <div class="mb-3" >
                                        <h4 class="fw-bold mb-3">Detail BMN</h4>
                                        <label for="satker" class="form-label">Pilih kode satker</label>
                                        <select class="form-control" name="satker_id" id="satker">
                                            <option value="" disabled selected>-- Pilih kode satker --</option>
                                            @foreach ($satker as $keysatker)

                                            <option value="{{$keysatker->id}}">{{$keysatker->kode_satker}}</option>
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

                                                <option value="{{ $keybarang->id }}" >{{ $keybarang->kode_barang }} - {{ $keybarang->nama_barang }}</option>
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
                                        <input type="number" name="jumlah" id="jumlah" class="form-control" step="5">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="nilai_perolehan" class="form-label">Nilai Perolehan</label>
                                        <input type="text" name="nilai_perolehan" id="nilai_perolehan" class="form-control" step="500000">
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                    <p class="mb-0">It is a long established fact that a reader will be distracted by the readable
                                    content of a page when looking at its layout. The point of using Lorem Ipsum is that it has
                                    a
                                    more-or-less normal distribution of letters, as opposed to using 'Content here, content
                                    here',
                                    making it look like readable English. Many desktop publishing packages and web page editors
                                    now
                                    use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover
                                    many
                                    web sites still in their infancy.</p>
                                </div>
                                <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                    <p class="mb-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                    Lorem
                                    Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown
                                    printer
                                    took a galley of type and scrambled it to make a type specimen book. It has survived not
                                    only
                                    five centuries, but also the leap into electronic typesetting, remaining essentially
                                    unchanged.
                                    </p>
                                </div>
                                <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                    <p class="mb-0">There are many variations of passages of Lorem Ipsum available, but the
                                    majority
                                    have suffered alteration in some form, by injected humour, or words which don't look
                                    even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be
                                    sure
                                    there isn't anything embarrassing hidden in the middle of text.</p>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
</div>

    <script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{asset('https://cdn.jsdelivr.net/npm/autonumeric@4.6.0')}}"></script>

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

            var UnitSelect = document.querySelectorAll('[data-unit]');
            for (i = 0; i < UnitSelect.length; ++i) {
            var unitelement = UnitSelect[i];
            new Choices(unitelement, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari unit kerja'
            });
            }
        })
    </script>





@endsection
