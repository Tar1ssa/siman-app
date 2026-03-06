<div class="d-flex gap-1">
    <a href="{{ route('internal.show', $row->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
        <i class="fas fa-eye"></i>
    </a>

    @if(auth()->user()->isAdmin())
        @if($row->is_requested == 1)
        <form action="{{ route('internal.reject-request', $row->id) }}" method="POST">
            @csrf
            @method('PUT')
            <button title="Tolak Permintaan Unlock" type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak permintaan unlock?')">
                <i class="fas fa-times"></i>
            </button>
        </form>
        @endif

        <form action="{{ route('internal.unlock', $row->id) }}" method="POST">
            @csrf
            @method('PUT')
            <button title="unlock data" type="submit" class="btn btn-sm btn-warning">
                <i class="fas fa-unlock"></i>
            </button>
        </form>
    @endif

    <a href="{{ route('internal.bast', $row->id) }}" class="btn btn-sm btn-primary" title="Download BAST" target="_blank">
        <i class="fas fa-download"></i>
    </a>
</div>
