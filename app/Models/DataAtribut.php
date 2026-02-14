<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataAtribut extends Model
{
    protected $fillable = [
        'data_internal_id',
        'atributs_id',
        'value_string',
        'value_integer',
        'value_date'
    ];

    protected $casts = [
        'value_integer' => 'integer',
        'value_date' => 'date',
    ];

    public function atribut()
    {
        return $this->belongsTo(Atribut::class, 'atributs_id');
    }

    public function dataInternal()
    {
        return $this->belongsTo(DataInternal::class, 'data_internal_id');
    }
}
