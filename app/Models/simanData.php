<?php

namespace App\Models;

use App\Models\bmn;
use App\Models\satker;
use Illuminate\Database\Eloquent\Model;

class simanData extends Model
{
    protected $fillable = [
        'bmn_id',
        'satker_id',
        'barang_id',
        'nup',
        'merk',
        'tipe',
        'kondisi',
        'no_dokumen',
        'no_BPKP',
        'no_polisi',
        'no_sertifikat',
        'tgl_perolehan',
        'nilai_perolehan',
        'nilai_penyusutan',
        'nilai_buku',
        'kode_register',
        'lokasi_ruang',
        'update_lokasi_ruang',
        'update_kondisi',
        'nama_pengguna',
        'link_dokumentasi',
        'opname',
        'import_batch_id'
    ];

    public function bmns()
    {
        return $this->belongsTo(bmn::class, 'bmn_id', 'id');
    }

    public function satkers()
    {
        return $this->belongsTo(satker::class, 'satker_id', 'id');
    }

    public function batchId()
    {
        return $this->belongsTo(SimanBatch::class, 'import_batch_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id');
    }
}
