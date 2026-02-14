<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataInternal extends Model
{
    protected $fillable = [
        'satker_id',
        'barang_id',
        'lokasi_id',
        'nup',
        'tgl_perolehan',
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
        'penggunaRaw',
        // 'lokasi_ruang',
        'status_inven',
        'update_kondisi',
        'link_dokumentasi',
        'link_lhi',
        'no_bahi',
        'tgl_bahi',
        'kode_registrasi',
        'siman_id',
        'batch',
        'label',
        'profile_image',
        'profile_image_path',
        'nama_pengguna',
        'alamat_pengguna',
        'identitas_id',
    ];

    protected $casts = [
        'tgl_perolehan' => 'date',
        'tgl_bahi' => 'date',
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

    public function fotoInternals()
    {
        return $this->hasMany(FotoInternal::class);
    }

    public function penggunas()
    {
        return $this->hasOne(Pengguna::class, 'data_internal_id', 'id');
    }

    public function lokasiRuang()
    {
        return $this->belongsTo(LokasiRuang::class, 'lokasi_id', 'id');
    }

    public function dataAtribut()
    {
        return $this->hasMany(DataAtribut::class);
    }

    public function identitas()
    {
        return $this->belongsTo(Identitas::class, 'identitas_id', 'id');
    }
}
