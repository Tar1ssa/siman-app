<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdentitasKategori extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function identitas()
    {
        return $this->hasMany(Identitas::class, 'kategori_id');
    }
}
