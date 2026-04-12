<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $products = Product::published()->with('modules')->get()->map(function ($product) use ($user) {
            $product->is_enrolled = $user->isEnrolledIn($product);
            return $product;
        });

        return view('member.products.index', compact('products'));
    }

    public function show(string $slug)
    {
        $user    = Auth::user();
        $product = Product::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $modules = $product->modules()->with('lessons')->get();
        $isEnrolled = $user->isEnrolledIn($product);
        $progress   = $user->lessonProgress()->pluck('lesson_id')->toArray();

        $totalLessons = $modules->flatMap->lessons->count();
        $completedLessons = count(array_intersect($progress, $modules->flatMap->lessons->pluck('id')->toArray()));
        $percentage = $totalLessons > 0 ? round($completedLessons / $totalLessons * 100) : 0;

        return view('member.products.show', compact('product', 'modules', 'isEnrolled', 'progress', 'totalLessons', 'completedLessons', 'percentage'));
    }
}
