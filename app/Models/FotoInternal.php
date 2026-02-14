<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoInternal extends Model
{
    protected $fillable = [
        'data_internal_id',
        'filename',
        'path',
        'title',
        'description',
        'is_cover'
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function dataInternal()
    {
        return $this->belongsTo(DataInternal::class);
    }
}
