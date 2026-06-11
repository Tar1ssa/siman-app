@extends('app')
@section('title', $title)
@section('dependencies')
<style>
    #attribute-search {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .attribute-item input[type="checkbox"]:checked + label {
        color: #0d6efd;
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
                  <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                  <li class="breadcrumb-item"><a href="#">identitas</a></li>
                  <li class="breadcrumb-item" aria-current="page">Tambah identitas</li>
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

        <!-- [ Main Content ] start -->
        <div class="row">
          <!-- [ form-element ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3>Edit identitas</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('identitas.update', $identitas->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">

                                <label class="form-label">Kategori</label>
                                <select name="kategori_id" id="kategori_id" class="form-control">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($identitasKategori as $kategori)
                                        <option value="{{ $kategori->id }}" {{ $identitas->kategori_id == $kategori->id ? 'selected' : '' }}>{{ $kategori->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">

                                <label class="form-label">Name</label>
                                <input class="form-control" type="text" name="name" value="{{ $identitas->name }}">
                            </div>
                            <div class="mb-3">

                                <label class="form-label">Slug</label>
                                <input class="form-control" type="text" name="slug" value="{{ $identitas->slug }}">
                            </div>

                                <h3 class="mb-3">atribut</h3>

                                <div class="mb-3">
                                    <label class="form-label">Search Attribute</label>
                                    <input type="text" id="attribute-search" class="form-control"
                                        placeholder="Cari atribut (mis. RAM, Processor)">
                                    <small class="text-muted">
                                        Ketik untuk memfilter daftar atribut secara langsung.
                                    </small>
                                </div>

                                @php
                                    $assigned = $identitas->atribut->keyBy('id');
                                @endphp

                                <div id="attribute-container" class="mb-3"
                                        style="max-height: 450px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                @foreach($atribut as $attr)
                                @php
                                    $pivot = $assigned[$attr->id]->pivot ?? null;
                                @endphp
                                            <div class="attribute-item mb-3"
                                                data-label="{{ strtolower($attr->label) }}"
                                                data-key="{{ strtolower($attr->key) }}">

                                                <div class="border rounded p-3">

                                                    {{-- ENABLE ATTRIBUTE --}}
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            name="atribut[{{ $attr->id }}][enabled]"
                                                            id="attr-{{ $attr->id }}" {{ $pivot ? 'checked' : '' }}>

                                                        <label class="form-check-label fw-bold"
                                                            for="attr-{{ $attr->id }}">
                                                            {{ $attr->label }}
                                                            <small class="text-muted">({{ $attr->key }})</small>
                                                        </label>
                                                    </div>

                                                    {{-- REQUIRED --}}
                                                    <div class="mb-2">
                                                        <label class="form-label">Required</label><br>
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            name="atribut[{{ $attr->id }}][is_required]" {{ $pivot && $pivot->is_required ? 'checked' : '' }}>
                                                        <small class="text-muted">
                                                            Wajib diisi saat input data.
                                                        </small>
                                                    </div>

                                                    {{-- ORDER --}}
                                                    <div class="mb-2">
                                                        <label class="form-label">Order</label>
                                                        <input class="form-control form-control-sm"
                                                            type="number"
                                                            name="atribut[{{ $attr->id }}][sort_order]"
                                                            value="{{ $pivot ? $pivot->sort_order : 0 }}">
                                                        <small class="text-muted">
                                                            Urutan tampil di form. (semakin kecil semakin duluan)
                                                        </small>
                                                    </div>

                                                    {{-- PLACEHOLDER --}}
                                                    <div class="mb-2">
                                                        <label class="form-label">Placeholder</label>
                                                        <input class="form-control form-control-sm"
                                                            type="text"
                                                            name="atribut[{{ $attr->id }}][placeholder]" value="{{ $pivot ? $pivot->placeholder : '' }}">
                                                        <small class="text-muted">
                                                            Contoh isi input. (opsional)
                                                        </small>
                                                    </div>

                                                    {{-- HELP TEXT --}}
                                                    <div class="mb-2">
                                                        <label class="form-label">Help Text</label>
                                                        <input class="form-control form-control-sm"
                                                            type="text"
                                                            name="atribut[{{ $attr->id }}][help_text]"
                                                            value="{{ $pivot ? $pivot->help_text : '' }}">
                                                        <small class="text-muted">
                                                            Penjelasan tambahan. (opsional)
                                                        </small>
                                                    </div>

                                                </div>
                                            </div>
                                        @endforeach
                                </div>


                        </div>
                        {{-- <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <i class="ti ti-id font-size-icon text-blue-500"></i>
                        </div> --}}
                    </div>
                    <button type="submit" class="btn btn-shadow btn-primary">Submit</button>
                    <a href="{{ route('identitas.index') }}" class="btn btn-shadow btn-secondary">Kembali</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
</div>

<script>
document.getElementById('attribute-search').addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    const items = document.querySelectorAll('.attribute-item');

    items.forEach(item => {
        const label = item.dataset.label;
        const key = item.dataset.key;

        if (label.includes(keyword) || key.includes(keyword)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>

<script>
document.querySelectorAll('.attribute-item input[type="text"], .attribute-item input[type="number"]')
    .forEach(input => {
        input.addEventListener('input', function () {
            const card = this.closest('.attribute-item');
            const checkbox = card.querySelector('input[name$="[enabled]"]');
            if (checkbox) checkbox.checked = true;
        });
    });
</script>
@endsection
