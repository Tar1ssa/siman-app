@extends('app')
@section('title', $title)
@section('dependencies')
<link rel="stylesheet" href="{{ asset('/assets/dist/assets/css/plugins/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection
@section('content')
<div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="page-block">
              <div class="row align-items-center">
                <div class="col-md-12">
                  <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Admin</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="#">Activity Logs</a></li>
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
        </div>
        <!-- [ breadcrumb ] end -->

                <!-- [ Main Content ] start -->
        <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <h3>Activity Logs</h3>
                <div>
                  <button type="button" class="btn btn-shadow btn-success me-2" id="export-btn">
                    <i class="fas fa-file-excel"></i> Export to Excel
                  </button>
                  <button type="button" class="btn btn-danger btn-sm" id="cleanup-btn" data-url="{{ route('activity-logs.cleanup') }}">
                    <i class="fas fa-trash-alt"></i> Cleanup Old Logs (5+ years)
                  </button>
                </div>
              </div>

              <!-- Filters -->
              <div class="card-header">
                <div class="row">
                  <div class="col-md-3">
                    <label for="methodFilter" class="form-label fw-bold">Filter by Method</label>
                    <select id="methodFilter" class="form-select">
                      <option value="">All Methods</option>
                      <option value="GET">GET</option>
                      <option value="POST">POST</option>
                      <option value="PUT">PUT</option>
                      <option value="PATCH">PATCH</option>
                      <option value="DELETE">DELETE</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label for="userFilter" class="form-label fw-bold">Filter by User</label>
                    <select id="userFilter" class="form-select">
                      <option value="">All Users</option>
                      @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Filter by Date Range</label>
                    <div class="row">
                      <div class="col-md-5">
                        <input type="text" id="dateFrom" class="form-control" placeholder="From Date" autocomplete="off">
                      </div>
                      <div class="col-md-5">
                        <input type="text" id="dateTo" class="form-control" placeholder="To Date" autocomplete="off">
                      </div>
                      <div class="col-md-2">
                        <button class="btn btn-sm btn-secondary" onclick="clearDateFilters()">Clear</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="dt-responsive table-responsive">
                  <table id="activity-logs-table" class="display table table-striped table-hover dt-responsive nowrap" style="width: 100%">
                    <thead>
                      <tr>
                        <th>No.</th>
                        <th>User</th>
                        <th>Method</th>
                        <th>URI</th>
                        <th>Route Name</th>
                        <th>Parameters</th>
                        <th>Status Code</th>
                        <th>Response Content</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
</div>
@endsection

@section('script')
 <!-- datatable Js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('/assets/dist/assets/js/plugins/dataTables.responsive.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {
    // Initialize Flatpickr for date inputs
    flatpickr("#dateFrom", {
        dateFormat: "Y-m-d",
        allowInput: true
    });

    flatpickr("#dateTo", {
        dateFormat: "Y-m-d",
        allowInput: true
    });

    $('#activity-logs-table').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        pageLength: 25,

        scrollX: true,
        scrollY: '60vh',

        scrollCollapse: true,

        autoWidth: false,   // IMPORTANT
        responsive: false,
        ajax: {
            url: '{{ route("activity-logs.datatable") }}',
            data: function(d) {
                d.method = $('#methodFilter').val();
                d.user_id = $('#userFilter').val();
                d.date_from = $('#dateFrom').val();
                d.date_to = $('#dateTo').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'user_id', name: 'user_id' },
            { data: 'method', name: 'method' },
            { data: 'uri', name: 'uri' },
            { data: 'route_name', name: 'route_name' },
            { data: 'route_parameters', name: 'route_parameters' },
            { data: 'status_code', name: 'status_code' },
            { data: 'response_content', name: 'response_content', orderable: false },
            { data: 'ip_address', name: 'ip_address' },
            { data: 'user_agent', name: 'user_agent' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[10, 'desc']], // Order by timestamp descending
    });

    // Filter change handlers
    $('#methodFilter, #userFilter').on('change', function() {
        $('#activity-logs-table').DataTable().ajax.reload();
    });

    $('#dateFrom, #dateTo').on('change', function() {
        $('#activity-logs-table').DataTable().ajax.reload();
    });

    // Clear date filters function
    window.clearDateFilters = function() {
        $('#dateFrom').val('');
        $('#dateTo').val('');
        $('#activity-logs-table').DataTable().ajax.reload();
    };

    // Export button handler
    $('#export-btn').on('click', function() {
        const baseUrl = '{{ route("activity-logs.export") }}';
        const params = new URLSearchParams();

        const method = $('#methodFilter').val();
        const userId = $('#userFilter').val();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();

        if (method) params.append('method', method);
        if (userId) params.append('user_id', userId);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);

        const exportUrl = baseUrl + (params.toString() ? '?' + params.toString() : '');
        window.location.href = exportUrl;
    });

    // Cleanup button handler
    $('#cleanup-btn').on('click', function() {
        const url = $(this).data('url');

        if (confirm('Are you sure you want to delete all activity logs older than 5 years? This action cannot be undone.')) {
            // Disable button during processing
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Cleanup completed! ' + response.message);
                        // Reload the DataTable
                        $('#activity-logs-table').DataTable().ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error occurred during cleanup. Please try again.');
                    console.error(xhr);
                },
                complete: function() {
                    // Re-enable button
                    $('#cleanup-btn').prop('disabled', false).html('<i class="fas fa-trash-alt"></i> Cleanup Old Logs (5+ years)');
                }
            });
        }
    });
});
</script>

<!-- Modal for displaying full content -->
<div class="modal fade" id="contentModal" tabindex="-1" aria-labelledby="contentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contentModalLabel">Content Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="modalContent" style="white-space: pre-wrap; word-wrap: break-word; max-height: 400px; overflow-y: auto;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showModal(title, content) {
    $('#contentModalLabel').text(title);
    $('#modalContent').text(content);
    $('#contentModal').modal('show');
}
</script>
@endsection
