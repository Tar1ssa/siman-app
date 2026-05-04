<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataInternal extends Model
{
    // use HasFactory;

    protected $casts = [
        'tgl_perolehan' => 'date',
        'tgl_bahi' => 'date',
        'nup' => 'integer',
    ];

    protected $fillable = [
        'satker_id',
        'barang_id',
        'lokasi_id',
        'pengguna_unitkerja_id',
        'unit_teknis_id',
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
        'nip_pengguna',
        'jabatan_pengguna',
        'nama_pihak_pertama',
        'nip_pihak_pertama',
        'jabatan_pihak_pertama',
        'alamat_pihak_pertama',
        'status',
        'ket_lokasi',
        'ket_penugasan',
        'ket_unit_teknis',
        'is_requested',
        'is_borrowed',
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

    public function documentInternals()
    {
        return $this->hasMany(DocumentInternal::class);
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

    /**
     * Check if all required fields are filled
     */
    public function isComplete()
    {
        $requiredFields = [
            'satker_id',
            'barang_id',
            // 'lokasi_id',
            // 'pengguna_unitkerja_id',
            // 'unit_teknis_id',
            'nup',
            'tgl_perolehan',
            'merk',
            'tipe',
            'jumlah',
            'nilai_aset',
            // 'nilai_penyusutan',
            // 'nilai_buku',
            'kondisi',
            // 'akun_neraca',
            'pembukuan',
            'unit_kerja_id',
            // 'status_inven',
            // 'update_kondisi',
            // 'link_dokumentasi',
            // 'link_lhi',
            // 'no_bahi',
            // 'tgl_bahi',
            // 'kode_registrasi',
            // 'siman_id',
            // 'batch',
            // 'label',
            'nama_pengguna',
            'alamat_pengguna',
            'identitas_id',
            'nip_pengguna',
            'jabatan_pengguna',
            'nama_pihak_pertama',
            'nip_pihak_pertama',
            'jabatan_pihak_pertama',
            'alamat_pihak_pertama',
            // 'ket_lokasi',
            // 'ket_penugasan',
            // 'ket_unit_teknis',
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if data has at least one foto internal
     */
    public function hasPhotos()
    {
        return $this->fotoInternals()->count() > 0;
    }

    /**
     * Check if data should be locked (complete and has photos)
     */
    public function shouldBeLocked()
    {
        return $this->isComplete() && $this->hasPhotos();
    }

    /**
     * Auto-lock data if conditions are met
     */
    public function autoLock()
    {
        if ($this->shouldBeLocked()) {
            $this->update(['status' => 'locked']);
        }
    }

    /**
     * Check if data is locked
     */
    public function isLocked()
    {
        return $this->status === 'locked';
    }

    /**
     * Check if data can be updated
     */
    public function canBeUpdated()
    {
        return $this->status !== 'locked';
    }
}
