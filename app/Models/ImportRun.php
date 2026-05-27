<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'fingerprint',
        'user_id',
        'batch_label',
        'batch_type',
        'batch_id',
        'status',
        'response_status',
        'response_payload',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'response_payload' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
