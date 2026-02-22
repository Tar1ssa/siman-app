@extends('app')
@section('title', $title)
@section('content')
<div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item" aria-current="page"><a href="#">Dashboard</a></li>
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

        {{-- dashboard content start --}}
        <div class="row mb-3">
            <h3>Selamat Datang, {{ auth()->user()->name }}</h3>
            <hr class="mb-3">
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Level Akses</h6>
                        <h2 class="mb-3"><span class="badge bg-light-primary border border-primary">{{ auth()->user()->level->level_name }}</span></h2>
                        <h6 class="mb-2 f-w-400 text-muted">Unit Kerja</h6>
                        <h2 class="mb-3"><span class="badge bg-light-warning border border-warning">{{ auth()->user()->unitKerja->name ?? 'N/A' }}</span></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Kondisi Barang Berdasarkan Status
                        </h6>
                        <div class="mb-2">
                            <label for="unit_kerja_filter" class="form-label">Filter Unit Kerja</label>
                            <select id="unit_kerja_filter" class="form-select">
                                <option value="">Semua Unit Kerja</option>
                                @foreach($unitKerjaList as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row text-center" id="kondisi-barang-status">
                            <div class="col-4">
                                <span class="badge bg-success">B</span>
                                <div>Baik</div>
                                <h4 id="countBaik"><span class="badge bg-light-success border border-success">{{ number_format($countBaik) }}</span></h4>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-warning text-white">RR</span>
                                <div>Rusak Ringan</div>
                                <h4 id="countRusakRingan"><span class="badge bg-light-warning border border-warning">{{ number_format($countRusakRingan) }}</span></h4>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-danger">RB</span>
                                <div>Rusak Berat</div>
                                <h4 id="countRusakBerat"><span class="badge bg-light-danger border border-danger">{{ number_format($countRusakBerat) }}</span></h4>
                            </div>
                        </div>
                        <script>
                        document.getElementById('unit_kerja_filter').addEventListener('change', function() {
                            const unitKerjaId = this.value;
                            fetch(`{{ route('dashboard.kondisi-barang-status') }}?unit_kerja=${unitKerjaId}`)
                                .then(response => {
                                    if (!response.ok) {
                                        return response.text().then(text => { throw new Error(text); });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    document.getElementById('countBaik').textContent = data.countBaik;
                                    document.getElementById('countRusakRingan').textContent = data.countRusakRingan;
                                    document.getElementById('countRusakBerat').textContent = data.countRusakBerat;
                                })
                                .catch(error => {
                                    alert('Error: ' + error.message);
                                    console.error(error);
                                });
                        });
                        </script>
                    </div>
            </div>
        </div>

        <div class="row mb-3">
            <h3>Database Info</h3>
            <hr>
            <div class="col-md-6 col-xl-3">
                <a href="{{route('siman.index')}}">
                        <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Data SIMAN</h6>
                            <h2 class="mb-3"><span class="badge bg-light-primary border border-primary"> {{ number_format($simanCount) }}</span></h2>
                            {{-- <a
                                            href="{{ route('export.siman', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="mb-0 text-primary text-sm"
                                        >
                                            <i class="fa fa-file-excel"></i> Export SIMAN Only
                                        </a> --}}

                        </div>
                        </div>
                </a>
            </div>
            <div class="col-md-6 col-xl-3">
                <a href="{{route('internal.index')}}">

                    <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Data Internal</h6>
                        <h2 class="mb-3"><span class="badge bg-light-warning border border-warning">{{ number_format($internalCount) }}</span></h2>
                        <a href="{{ route('export.internal-all') }}" class="mb-0 text-warning text-sm"> <i class="fa fa-file-excel"></i> Export Data Internal</a>
                        {{-- <a
                                        href="{{ route('export.internal', [
                                            'batch_internal' => request('batch_internal'),
                                            'batch_siman'    => request('batch_siman'),
                                        ]) }}"
                                        class="mb-0 text-warning text-sm"
                                    >
                                        <i class="fa fa-file-excel"></i> Export Internal Only
                                    </a> --}}
                    </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-xl-3">
                <a href="{{route('invalid.index')}}">

                    <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Data Invalid</h6>
                        <h2 class="mb-3"><span class="badge bg-light-danger border border-danger">{{ number_format($invalidCount) }}</span></h2>
                        <a
                                        href="{{ route('export.invalid') }}"
                                        class="mb-0 text-danger text-sm"
                                    >
                                        <i class="fa fa-file-excel"></i> Export Invalid Data
                                    </a>

                    </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row mb-3">
            <h3>Komparasi Info</h3>
            <hr>
            <!-- [ sample-page ] start -->
            <div class="col-md-6 col-xl-3">
                <a href="{{route('compare.index', ['status' => 'MATCH'])}}">
                    <div class="card">
                        <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Data Match</h6>
                        <h2 class="mb-3"> <span class="badge bg-light-success border border-success"> {{ number_format($match) }}</span></h2>
                                        <a
                                            href="{{ route('export.match', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="mb-0 text-success text-sm"
                                        >
                                            <i class="fa fa-file-excel"></i> Export MATCH
                                        </a>
                        </div>
                    </div>

                </a>
            </div>

            <div class="col-md-6 col-xl-3">
            <a href="{{route('compare.index', ['status' => 'MATCH_NUP'])}}">
                <div class="card">
                    <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Total Data Match NUP (mismatch tgl dan nilai)</h6>
                    <h2 class="mb-3"> <span class="badge bg-light-white border border-dark text-dark"> {{ number_format($matchnup) }}</span></h2>
                                    <a
                                        href="{{ route('export.matchnup', [
                                            'batch_internal' => request('batch_internal'),
                                            'batch_siman'    => request('batch_siman'),
                                        ]) }}"
                                        class="mb-0 text-dark text-sm"
                                    >
                                        <i class="fa fa-file-excel"></i> Export Match NUP
                                    </a>
                    </div>
                </div>

            </a>
            </div>

            <div class="col-md-6 col-xl-3">
                <a href="{{route('compare.index', ['status' => 'MATCH_NILAI'])}}">
                    <div class="card">
                        <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Data Match Nilai (mismatch tgl)</h6>
                        <h2 class="mb-3"> <span class="badge bg-light-dark border border-dark"> {{ number_format($matchnilai) }}</span></h2>
                                        <a
                                            href="{{ route('export.matchnilai', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="mb-0 text-dark text-sm"
                                        >
                                            <i class="fa fa-file-excel"></i> Export Match Nilai
                                        </a>
                        </div>
                    </div>

                </a>
            </div>

            <div class="col-md-6 col-xl-3">
                <a href="{{route('compare.index', ['status' => 'MATCH_TGL'])}}">
                    <div class="card">
                        <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Data Match Tanggal (mismatch nilai)</h6>
                        <h2 class="mb-3"> <span class="badge bg-light-info border border-info"> {{ number_format($matchtgl) }}</span></h2>
                                        <a
                                            href="{{ route('export.matchtgl', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="mb-0 text-info text-sm"
                                        >
                                            <i class="fa fa-file-excel"></i> Export Match Tanggal
                                        </a>
                        </div>
                    </div>

                </a>
            </div>

            <div class="col-md-6 col-xl-3">
                <a href="{{route('siman.index')}}">
                        <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Data SIMAN Only</h6>
                            <h2 class="mb-3"><span class="badge bg-light-primary border border-primary"> {{ number_format($simanOnly) }}</span></h2>
                            <a
                                            href="{{ route('export.siman', [
                                                'batch_internal' => request('batch_internal'),
                                                'batch_siman'    => request('batch_siman'),
                                            ]) }}"
                                            class="mb-0 text-primary text-sm"
                                        >
                                            <i class="fa fa-file-excel"></i> Export SIMAN Only
                                        </a>

                        </div>
                        </div>
                </a>
            </div>
            <div class="col-md-6 col-xl-3">
                <a href="{{route('internal.index')}}">

                    <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Data Internal Only</h6>
                        <h2 class="mb-3"><span class="badge bg-light-warning border border-warning">{{ number_format($internalOnly) }}</span></h2>
                        <a
                                        href="{{ route('export.internal', [
                                            'batch_internal' => request('batch_internal'),
                                            'batch_siman'    => request('batch_siman'),
                                        ]) }}"
                                        class="mb-0 text-warning text-sm"
                                    >
                                        <i class="fa fa-file-excel"></i> Export Internal Only
                                    </a>
                    </div>
                    </div>
                </a>
            </div>

        </div>

</div>


@endsection
