@extends('app')
@section('title', $title)
@section('dependencies')
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection
@section('content')
<div class="pc-content overflow-x-hidden">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item" aria-current="page"><a href="#">Master Data</a></li>
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
          <div class="col-sm-12">
            <div class="">


                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card p-4 mb-4">
                    <h5>Last Backup:</h5>
                    <p><strong>{{ $lastBackup }}</strong></p>

                    <form action="/admin/backups/full" method="POST" class="mb-2">
                        @csrf
                        <button class="btn btn-shadow btn-primary">Run Full Backup (DB + Files)</button>
                    </form>

                    <form action="/admin/backups/files-only" method="POST">
                        @csrf
                        <button class="btn btn-shadow btn-warning">Run Files Only Backup</button>
                    </form>
                </div>

                <h5>Existing Backups</h5>

                <table class="table table-bordered bg-white">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                        <tr>
                            <td>{{ $backup['name'] }}</td>
                            <td>{{ $backup['size'] }}</td>
                            <td>{{ $backup['created_at'] }}</td>
                            <td>
                                <a href="{{ route('backups.download', $backup['name']) }}"
                                class="btn btn-shadow btn-sm btn-success">
                                 Download
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                </table>

            </div>

          </div>
          <!-- `New` Constructor table end -->
        </div>



</div>
@endsection
