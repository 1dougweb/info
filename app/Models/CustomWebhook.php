<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class CustomWebhook extends Model
{
    protected $fillable = ['uuid', 'name', 'last_payload', 'mapping'];

    protected $casts = [
        'last_payload' => 'array',
        'mapping'      => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
