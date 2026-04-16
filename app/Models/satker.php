<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class satker extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_satker',
        'nama_satker'
    ];
}
