@extends('app')
@section('title', $title)
@section('dependencies')
<link rel="stylesheet" href="{{asset('/assets/dist/assets/css/plugins/dropzone.min.css')}}">
<style>
/* Remove thumbnail box completely */
#csvDropzone .dz-image {
    display: none !important;
}

/* THIS is the real white blob — remove it */
#csvDropzone .dz-preview {
    background: none !important;
    min-height: auto !important;
    padding: 8px 0 !important;
}

/* Make text layout clean */
#csvDropzone .dz-details {
    position: relative !important;
    opacity: 1 !important;
    background: none !important;
    padding: 0 !important;
}

/* Spacing so nothing overlaps */
#csvDropzone .dz-filename {
    margin-top: 4px;
}

#csvDropzone .dz-size {
    margin-top: 4px;
}

#csvDropzone .dz-remove {
    margin-top: 6px;
    display: inline-block;
}

/* Make preview a positioning container */
#csvDropzone .dz-preview {
    position: relative;
}

/* Make each preview a column layout */
#csvDropzone .dz-preview {
    display: flex !important;
    flex-direction: column !important;
}

/* Put progress bar last in the column */
#csvDropzone .dz-progress {
    order: 99 !important;      /* forces it to bottom */
    width: 8% !important;
    margin-top: 8px !important;
    height: 6px;
}

/* Make the inner bar fill properly */
#csvDropzone .dz-progress .dz-upload {
    height: 100% !important;
}

/* Keep text readable */
#csvDropzone .dz-details {
    order: 1;
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
                        <form action="{{ route('internal.store') }}"
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
                            <div class="text-center m-t-20 mb-3">
                                <button type="submit" id="uploadBtn" class="btn btn-primary">Upload Now</button>
                            </div>
                        <div class="row mb-3">
                            <div class="col-md-4">

                                <a href="{{asset('/assets/import_template_internal.csv')}}" download class="btn btn-primary btn-shadow btn-sm">
                                    Download template import File
                                </a>
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
            url: "{{ route('internal.store') }}",
            paramName: "csv_file",
            acceptedFiles: ".csv",
            maxFiles: 1,                 // prevent multiple files
            uploadMultiple: false,       // force single
            parallelUploads: 1,          // safety
            autoProcessQueue: false,
            addRemoveLinks: true,
            dictRemoveFile: 'Hapus file',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            init: function () {
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
                this.on('removedfile', function(file) {
                    // clear fallback input when a file is removed
                    const fallbackInput = document.querySelector('#csvDropzone input[type=file]');
                    if (fallbackInput) fallbackInput.value = '';
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

            if (response && response.success) {
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
                const msg = response && response.message ? response.message : 'Terjadi kesalahan pada server';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: msg
                });

                // remove the failed file so user can retry
                try { myDropzone.removeFile(file); } catch (e) { console.warn(e); }
                const fallbackInput = document.querySelector('#csvDropzone input[type=file]');
                if (fallbackInput) fallbackInput.value = '';
            }
        });

        myDropzone.on("error", function (file, response, xhr) {
            let message = 'Terjadi kesalahan saat upload';

            // Case A: Dropzone gives parsed JSON as `response`
            if (response && typeof response === 'object') {
                if (response.message) {
                    message = response.message;
                } else if (response.errors) {
                    if (Array.isArray(response.errors)) {
                        message = response.errors.join('; ');
                    } else if (typeof response.errors === 'object') {
                        // Laravel validation errors object
                        message = Object.values(response.errors).flat().join('; ');
                    }
                }
            }

            // Case B: XHR available with parsed JSON
            else if (xhr && xhr.responseJSON) {
                const body = xhr.responseJSON;
                if (body.message) message = body.message;
                else if (body.errors) {
                    if (Array.isArray(body.errors)) message = body.errors.join('; ');
                    else message = Object.values(body.errors).flat().join('; ');
                }
            }

            // Case C: response may be a string (plain text or JSON string)
            else if (typeof response === 'string') {
                try {
                    const parsed = JSON.parse(response);
                    if (parsed.message) message = parsed.message;
                    else if (parsed.errors) message = Array.isArray(parsed.errors) ? parsed.errors.join('; ') : Object.values(parsed.errors).flat().join('; ');
                } catch (e) {
                    // fallback to raw string
                    message = response;
                }
            }

            console.error('Dropzone upload error:', { file, response, xhr });

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: message
            });

            // remove the errored file so user can reselect
            try { myDropzone.removeFile(file); } catch (e) { console.warn(e); }
            const fallbackInput2 = document.querySelector('#csvDropzone input[type=file]');
            if (fallbackInput2) fallbackInput2.value = '';
        });

    </script>



@endsection
