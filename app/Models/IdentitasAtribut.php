<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdentitasAtribut extends Model
{
    protected $fillable = [
        'identitas_id',
        'atributs_id',
        'is_required',
        'sort_order',
        'placeholder',
        'help_text',
    ];

    public function identitas()
    {
        return $this->belongsTo(Identitas::class);
    }

    public function atribut()
    {
        return $this->belongsTo(Atribut::class, 'atributs_id');
    }
}
