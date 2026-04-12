<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enrollments = $user->activeEnrollments()->with(['product.modules.lessons'])->get();

        $enrollmentsWithProgress = $enrollments->map(function ($enrollment) use ($user) {
            $product = $enrollment->product;
            $totalLessons     = $product->modules->flatMap->lessons->count();
            $completedLessons = $user->lessonProgress()
                ->whereIn('lesson_id', $product->modules->flatMap->lessons->pluck('id'))
                ->count();

            return [
                'enrollment'       => $enrollment,
                'product'          => $product,
                'total_lessons'    => $totalLessons,
                'completed_lessons'=> $completedLessons,
                'percentage'       => $totalLessons > 0 ? round($completedLessons / $totalLessons * 100) : 0,
            ];
        });

        return view('member.dashboard', compact('enrollmentsWithProgress'));
    }
}
