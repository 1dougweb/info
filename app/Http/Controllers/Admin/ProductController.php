<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::withCount('enrollments')->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'content'             => 'nullable|string',
            'type'                => 'required|in:course,ebook,download,membership',
            'price'               => 'required|numeric|min:0',
            'status'              => 'required|in:draft,published,archived',
            'thumbnail'           => 'nullable|image|max:2048',
            'checkout_url'        => 'nullable|url',
            'checkout_hotmart_id' => 'nullable|string',
            'checkout_cakto_id'   => 'nullable|string',
            'checkout_wikify_id'  => 'nullable|string',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'content'             => 'nullable|string',
            'type'                => 'required|in:course,ebook,download,membership',
            'price'               => 'required|numeric|min:0',
            'status'              => 'required|in:draft,published,archived',
            'thumbnail'           => 'nullable|image|max:2048',
            'checkout_url'        => 'nullable|url',
            'checkout_hotmart_id' => 'nullable|string',
            'checkout_cakto_id'   => 'nullable|string',
            'checkout_wikify_id'  => 'nullable|string',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produto atualizado!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produto removido.');
    }
}
