<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Product;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function store(Request $request, Product $product, Module $module)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'type'      => 'required|in:video,text,file,quiz',
            'content'   => 'nullable|string',
            'video_url' => 'nullable|url',
            'duration'  => 'nullable|integer|min:0',
            'order'     => 'integer|min:0',
            'is_free'   => 'boolean',
        ]);

        $module->lessons()->create($data + [
            'order'   => $module->lessons()->count(),
            'is_free' => $request->boolean('is_free'),
        ]);

        return back()->with('success', 'Aula criada!');
    }

    public function update(Request $request, Product $product, Module $module, Lesson $lesson)
    {
        $lesson->update($request->validate([
            'title'     => 'required|string|max:255',
            'type'      => 'required|in:video,text,file,quiz',
            'content'   => 'nullable|string',
            'video_url' => 'nullable|url',
            'duration'  => 'nullable|integer|min:0',
            'order'     => 'integer|min:0',
            'is_free'   => 'boolean',
        ]) + ['is_free' => $request->boolean('is_free')]);

        return back()->with('success', 'Aula atualizada!');
    }

    public function destroy(Product $product, Module $module, Lesson $lesson)
    {
        $lesson->delete();
        return back()->with('success', 'Aula removida.');
    }
}
