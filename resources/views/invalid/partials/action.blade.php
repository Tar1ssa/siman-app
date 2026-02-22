<div class="d-flex gap-1">
    {{-- Edit --}}
    {{-- <button data-row='@json($row)'
    onclick="openEditModal(this)"
       class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal">
        <i class="ti ti-edit"></i>
    </button> --}}

    {{-- Delete --}}
    <button
        class="btn btn-sm btn-danger btn-delete"
        data-id="{{ $row->id }}"
        data-name="{{ $row->id }}">
        <i class="ti ti-trash"></i>
    </button>
</div>
