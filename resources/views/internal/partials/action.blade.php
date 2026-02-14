<div class="">
    {{-- Edit --}}
    <a href="{{ route('internal.show', $row->id) }}"
        class="btn btn-sm btn-shadow btn-success fs-6">
        <i class="ti ti-eye fs-6"></i>
        Show
    </a>
    {{-- <button data-row='@json($row)'
        data-id="{{ $row->id }}"
        data-name="{{ $row->id }}"
        class="btn btn-sm btn-success">
        <i class="ti ti-eye"></i>
        Show
    </button> --}}

    {{-- Delete --}}
    <form onclick="return confirm('Yakin ingin menghapus {{ $row->barang->nama_barang }}, NUP: {{ $row->nup }} ?')" action="{{ route('internal.destroy', $row->id) }}" method="post" class="d-inline">
        @csrf
        @method('DELETE')

        <button class="btn btn-sm btn-shadow btn-danger fs-6"><i class="ti ti-trash fs-6"></i>Delete</button>
    </form>
    {{-- <button
        class="btn btn-sm btn-danger btn-delete"
        data-id="{{ $row->id }}"
        data-name="{{ $row->id }}"
        data-nama_barang="{{$row->barang->nama_barang}}"
        data-nup="{{$row->nup}}"
        >
        <i class="ti ti-trash"></i>
        Delete
    </button> --}}
</div>
