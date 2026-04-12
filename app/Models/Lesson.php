<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id', 'title', 'type', 'content', 'video_url', 'file_path', 'duration', 'order', 'is_free',
    ];

    protected $casts = ['is_free' => 'boolean'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->orderBy('created_at', 'desc');
    }

    public function isCompletedByUser(User $user): bool
    {
        return $this->progress()->where('user_id', $user->id)->whereNotNull('completed_at')->exists();
    }

    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) return '—';
        $minutes = intdiv($this->duration, 60);
        $seconds = $this->duration % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getTypeIconAttribute()
    {
        return match ($this->type) {
            'video' => '<i class="bi bi-play-circle-fill text-primary"></i>',
            'text'  => '<i class="bi bi-file-text-fill text-info"></i>',
            'file'  => '<i class="bi bi-paperclip text-warning"></i>',
            'quiz'  => '<i class="bi bi-question-circle-fill text-success"></i>',
            default => '<i class="bi bi-pin-fill"></i>',
        };
    }
}
