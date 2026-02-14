<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atribut extends Model
{
    protected $fillable = [
        'key',
        'label',
        'data_type',
    ];

    public function identitas()
    {
        return $this->belongsToMany(
            Identitas::class,
            'identitas_atributs',
            'atributs_id',
            'identitas_id'
        );
    }
}
