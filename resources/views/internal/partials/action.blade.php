

<div class="d-flex gap-1">
    {{-- Edit --}}
    <a title="Lihat detail" target="_blank" href="{{ route('internal.show', $row->id) }}"
        class="btn btn-sm btn-shadow btn-success fs-6">
        <i class="ti ti-eye fs-6"></i>

    </a>

    <a title="Generate BAST" target="_blank" href="{{ route('internal.bast', $row->id) }}"
        class="btn btn-sm btn-shadow btn-primary fs-6">
        <i class="bi bi-file-pdf fs-6"></i>

    </a>
    {{-- <button data-row='@json($row)'
        data-id="{{ $row->id }}"
        data-name="{{ $row->id }}"
        class="btn btn-sm btn-success">
        <i class="ti ti-eye"></i>
        Show
    </button> --}}

     @if(auth()->user()->isAdmin())
    <form action="{{ route('internal.lock', $row->id) }}" method="POST">
        @csrf
        @method('PUT')
        <button title="kunci data" type="submit" class="btn btn-sm btn-warning">
            <i class="fas fa-lock"></i>
        </button>
    </form>
    @endif

    {{-- Delete --}}
    @if (auth()->user()->isAdmin() || $row->status != 'locked')
    <form onclick="return confirm('Yakin ingin menghapus {{ $row->barang->nama_barang }}, NUP: {{ $row->nup }}, {{$row->merk}} {{$row->tipe}} ?')" action="{{ route('internal.destroy', $row->id) }}" method="post" class="d-inline">
        @csrf
        @method('DELETE')

        <button title="Hapus data" class="btn btn-sm btn-shadow btn-danger fs-6"><i class="ti ti-trash fs-6"></i></button>
    </form>
    @endif
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
