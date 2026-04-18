<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'content', 'type', 'thumbnail', 'file_path',
        'price', 'status', 'checkout_url', 'checkout_hotmart_id', 'checkout_cakto_id', 'checkout_wikify_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Module::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->wherePivot('status', 'active');
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail
            ? asset($this->thumbnail)
            : asset('images/product-placeholder.svg');
    }

    public function getTotalLessonsAttribute(): int
    {
        return $this->modules->sum(fn($m) => $m->lessons->count());
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset($this->file_path) : null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'course'     => 'Curso',
            'ebook'      => 'E-book',
            'download'   => 'Download',
            'membership' => 'Membership',
            default      => ucfirst($this->type),
        };
    }
}
