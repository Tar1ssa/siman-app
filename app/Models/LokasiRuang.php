<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiRuang extends Model
{
    protected $fillable = [
        'unit_kerja_id',
        'name',
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id', 'id');
    }
}
