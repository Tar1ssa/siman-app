<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvalidData extends Model
{
    use HasFactory;

    protected $fillable = [
        'satker_id',
        'barang_id',
        'nup',
        'tgl_perolehan',
        'merkRaw',
        'merk',
        'tipe',
        'jumlah',
        'nilai_aset',
        'nilai_penyusutan',
        'nilai_buku',
        'kondisi',
        'akun_neraca',
        'pembukuan',
        'unit_kerja_id',
        'pengguna',
        'lokasi_ruang',
        'status_inven',
        'update_kondisi',
        'link_dokumentasi',
        'link_lhi',
        'no_bahi',
        'tgl_bahi',
        'kode_registrasi',
        'siman_id',
        'description',
        'batch',
        'label'
    ];

    public function bmns()
    {
        return $this->belongsTo(bmn::class, 'bmn_id', 'id');
    }

    public function satkers()
    {
        return $this->belongsTo(satker::class, 'satker_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id', 'id');
    }
}
