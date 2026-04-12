<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookConfig extends Model
{
    protected $fillable = ['source', 'secret', 'is_active', 'settings'];
    protected $casts = ['is_active' => 'boolean', 'settings' => 'array'];

    public static function forSource(string $source): ?self
    {
        return static::where('source', $source)->first();
    }
}
