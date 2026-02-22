<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'method',
        'uri',
        'route_name',
        'route_parameters',
        'status_code',
        'response_content',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'route_parameters' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
