<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentInternal extends Model
{
    protected $fillable = [
        'data_internal_id',
        'filename',
        'path',
        'title',
        'description',
    ];

    public function dataInternal()
    {
        return $this->belongsTo(DataInternal::class);
    }
}
