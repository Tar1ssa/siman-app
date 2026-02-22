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
                  <li class="breadcrumb-item"><a href="#">Admin</a></li>
                  <li class="breadcrumb-item"><a href="{{ route('activity-logs.index') }}">Activity Logs</a></li>
                  <li class="breadcrumb-item" aria-current="page">Detail</li>
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
                <h3>Activity Log Details</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tr>
                    <th>User</th>
                    <td>{{ $activityLog->user ? $activityLog->user->name : 'Guest' }}</td>
                  </tr>
                  <tr>
                    <th>Method</th>
                    <td>{{ $activityLog->method }}</td>
                  </tr>
                  <tr>
                    <th>URI</th>
                    <td style="max-width: 300px; max-height: 100px; overflow: auto; word-wrap: break-word;">{{ $activityLog->uri }}</td>
                  </tr>
                  <tr>
                    <th>Route Name</th>
                    <td>{{ $activityLog->route_name ?: '-' }}</td>
                  </tr>
                  <tr>
                    <th>Route Parameters</th>
                    <td style="max-width: 400px; max-height: 200px; overflow: auto; word-wrap: break-word;">
                      @if($activityLog->route_parameters)
                        <pre style="margin: 0; white-space: pre-wrap;">{{ json_encode($activityLog->route_parameters, JSON_PRETTY_PRINT) }}</pre>
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <th>Status Code</th>
                    <td>{{ $activityLog->status_code }}</td>
                  </tr>
                  <tr>
                    <th>Response Content</th>
                    <td style="max-width: 500px; max-height: 300px; overflow: auto; word-wrap: break-word;">
                      @if($activityLog->response_content)
                        <pre style="margin: 0; white-space: pre-wrap;">{{ $activityLog->response_content }}</pre>
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <th>IP Address</th>
                    <td>{{ $activityLog->ip_address }}</td>
                  </tr>
                  <tr>
                    <th>User Agent</th>
                    <td style="max-width: 400px; max-height: 100px; overflow: auto; word-wrap: break-word;">{{ $activityLog->user_agent }}</td>
                  </tr>
                  <tr>
                    <th>Timestamp</th>
                    <td>{{ $activityLog->created_at->format('Y-m-d H:i:s') }}</td>
                  </tr>
                </table>
                <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">Back to List</a>
              </div>
            </div>
          </div>
        </div>
</div>
@endsection
