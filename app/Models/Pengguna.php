<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    protected $fillable = [
        'data_internal_id',
        'foto',
        'nama',
        'alamat'
    ];

    public function dataInternal()
    {
        return $this->belongsTo(DataInternal::class);
    }
}
