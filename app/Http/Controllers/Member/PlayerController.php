<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function show(Product $product, Lesson $lesson)
    {
        $user = Auth::user();

        if (!$lesson->is_free && !$user->isEnrolledIn($product)) {
            return redirect()->route('member.products.show', $product->slug)
                ->with('error', 'Você não tem acesso a este conteúdo.');
        }

        $modules  = $product->modules()->with('lessons')->get();
        $progress = $user->lessonProgress()->pluck('lesson_id')->toArray();

        $allLessons = collect();
        foreach ($modules as $m) {
            foreach ($m->lessons as $l) {
                $allLessons->push($l);
            }
        }
        $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $allLessons->get($currentIndex - 1) : null;
        $nextLesson = $currentIndex !== false && $currentIndex < $allLessons->count() - 1 ? $allLessons->get($currentIndex + 1) : null;

        return view('member.player', compact('product', 'lesson', 'modules', 'progress', 'prevLesson', 'nextLesson'));
    }

    public function complete(Request $request, Lesson $lesson)
    {
        LessonProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        return response()->json(['success' => true]);
    }
}
