<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identitas extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'kategori_id',
    ];

    public function atribut()
    {
        return $this->belongsToMany(
            Atribut::class,
            'identitas_atributs',        // pivot table name
            'identitas_id',             // FK on pivot
            'atributs_id'                // related FK
        )
        ->withPivot(['is_required', 'sort_order', 'placeholder', 'help_text'])
        ->orderBy('sort_order');
    }

    public function identitasKategori()
    {
        return $this->belongsTo(IdentitasKategori::class, 'kategori_id');
    }
}
