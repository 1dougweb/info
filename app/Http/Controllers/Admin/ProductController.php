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
            'file_path'           => 'nullable|file|max:51200', // 50MB
            'checkout_url'        => 'nullable|url',
            'checkout_hotmart_id' => 'nullable|string',
            'checkout_cakto_id'   => 'nullable|string',
            'checkout_wikify_id'  => 'nullable|string',
        ]);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/thumbnails'), $filename);
            $data['thumbnail'] = 'uploads/thumbnails/' . $filename;
        }

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $data['file_path'] = 'uploads/products/' . $filename;
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
            'file_path'           => 'nullable|file|max:51200', // 50MB
            'checkout_url'        => 'nullable|url',
            'checkout_hotmart_id' => 'nullable|string',
            'checkout_cakto_id'   => 'nullable|string',
            'checkout_wikify_id'  => 'nullable|string',
        ]);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/thumbnails'), $filename);
            $data['thumbnail'] = 'uploads/thumbnails/' . $filename;
        }

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $data['file_path'] = 'uploads/products/' . $filename;
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
