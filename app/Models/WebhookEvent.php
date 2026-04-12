<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = [
        'source', 'event_type', 'payload', 'normalized_data', 'status', 'error_message', 'processed_at',
    ];

    protected $casts = [
        'payload'         => 'array',
        'normalized_data' => 'array',
        'processed_at'    => 'datetime',
    ];
}
