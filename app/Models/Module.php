<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'title', 'description', 'order'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function getCompletionForUser(User $user): array
    {
        $total = $this->lessons->count();
        $completed = $this->lessons->filter(
            fn($l) => $user->lessonProgress->where('lesson_id', $l->id)->isNotEmpty()
        )->count();

        return ['total' => $total, 'completed' => $completed, 'percentage' => $total > 0 ? round($completed / $total * 100) : 0];
    }
}
