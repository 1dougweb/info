<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Product;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Product $product)
    {
        $modules = $product->modules()->withCount('lessons')->get();
        return view('admin.modules.index', compact('product', 'modules'));
    }

    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'order'       => 'integer|min:0',
        ]);

        $product->modules()->create($data + ['order' => $product->modules()->count()]);

        return back()->with('success', 'Módulo criado!');
    }

    public function update(Request $request, Product $product, Module $module)
    {
        $module->update($request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'order'       => 'integer|min:0',
        ]));

        return back()->with('success', 'Módulo atualizado!');
    }

    public function destroy(Product $product, Module $module)
    {
        $module->delete();
        return back()->with('success', 'Módulo removido.');
    }
}
