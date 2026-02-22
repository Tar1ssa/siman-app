@extends('app')
@section('title', $title)
@section('dependencies')
<link rel="stylesheet" href="{{asset('/assets/dist/assets/css/plugins/dropzone.min.css')}}">
@endsection
@section('content')
<div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Data SIMAN</a></li>
                  <li class="breadcrumb-item" aria-current="page">Tambah Data SIMAN</li>
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
                        <div class="row mb-3">
                            <div class="col-md-4"></div>
                            <div class="col-md-4 d-flex justify-content-center flex-column align-items-center">
                                <label for="" class="form-label">Label Batch</label>
                                <input
                                    type="text"
                                    id="batchLabel"
                                    class="form-control mb-3"
                                    placeholder="Batch label (contoh: Import Januari 2025)"
                                >
                            </div>
                            <div class="col-md-4"></div>
                        </div>

                        <form action="{{ route('siman.store') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="dropzone"
                            id="csvDropzone"
                            >
                            @csrf

                            <div class="fallback">
                                <input name="csv_file" accept=".csv" type="file" id="">
                            </div>
                        </form>


                            <div class="text-center m-t-20">
                            <button type="button" id="uploadBtn" class="btn btn-primary">Upload Now</button>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">

                                    <a href="{{asset('/assets/import_template_siman.csv')}}" download class="btn btn-primary btn-shadow btn-sm">
                                        Download template import File
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

    <script src="{{ asset('/assets/dist/assets/js/plugins/choices.min.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
            var genericExamples = document.querySelectorAll('[data-trigger]');
            for (i = 0; i < genericExamples.length; ++i) {
            var element = genericExamples[i];
            new Choices(element, {
                placeholderValue: 'This is a placeholder set in the config',
                searchPlaceholderValue: 'Cari BMN'
            });
            }

        });
    </script>

    <script src="{{ asset('/assets/dist/assets/js/plugins/dropzone-amd-module.min.js')}}"></script>
    <script>
        Dropzone.autoDiscover = false;



        const myDropzone = new Dropzone("#csvDropzone", {
            url: "{{ route('siman.store') }}",
            paramName: "csv_file",
            acceptedFiles: ".csv",
            maxFiles: 1,                 // prevent multiple files
            uploadMultiple: false,       // force single
            parallelUploads: 1,          // safety
            autoProcessQueue: false,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            init: function () {
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
            }
        });

        myDropzone.on("sending", function (file, xhr, formData) {
            const batchLabel = document.getElementById('batchLabel').value;

            formData.append('batch_label', batchLabel);
        });

        document.getElementById('uploadBtn').addEventListener('click', function () {
            if (myDropzone.files.length === 0) {
                alert("Please select a CSV file first.");
                return;
            }

            myDropzone.processQueue();
        });


        myDropzone.on("success", function (file, response) {
            console.log('Dropzone success fired');

            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                }).then(() => {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                });
            } else {
                // Backend returned JSON error but HTTP 200
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message
                });
            }
        });

        myDropzone.on("error", function (file, xhr) {
            let message = 'Terjadi kesalahan saat upload';

            // Laravel JSON error (500 / 422)
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: message
            });
        });

    </script>



@endsection
