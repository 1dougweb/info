<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledTask extends Model
{
    protected $fillable = [
        'automation_id', 'user_email', 'payload', 'execute_at', 'status', 'error_message',
    ];

    protected $casts = [
        'payload'    => 'array',
        'execute_at' => 'datetime',
    ];

    public function automation()
    {
        return $this->belongsTo(Automation::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDue($query)
    {
        return $query->pending()->where('execute_at', '<=', now());
    }
}
