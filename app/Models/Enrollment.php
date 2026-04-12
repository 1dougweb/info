<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'product_id', 'source', 'transaction_id', 'status', 'expires_at',
    ];

    protected $casts = ['expires_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSourceBadgeAttribute(): string
    {
        return match($this->source) {
            'hotmart' => 'Hotmart',
            'cakto'   => 'Cakto',
            'wikify'  => 'Wikify',
            'manual'  => 'Manual',
            default   => ucfirst($this->source),
        };
    }
}
