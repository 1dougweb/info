<?php

namespace App\Http\Controllers;

use App\Models\CustomWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomWebhookController extends Controller
{
    public function index()
    {
        $webhooks = CustomWebhook::latest()->get();
        return view('admin.custom-webhooks.index', compact('webhooks'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        CustomWebhook::create(['name' => $request->name]);
        return back()->with('success', 'Webhook Customizado criado!');
    }

    public function show(CustomWebhook $customWebhook)
    {
        return view('admin.custom-webhooks.show', compact('customWebhook'));
    }

    public function update(Request $request, CustomWebhook $customWebhook)
    {
        $request->validate(['mapping' => 'nullable|array']);
        
        $customWebhook->update([
            'mapping' => $request->mapping
        ]);

        return back()->with('success', 'Mapeamento salvo com sucesso!');
    }

    public function destroy(CustomWebhook $customWebhook)
    {
        $customWebhook->delete();
        return redirect()->route('admin.custom-webhooks.index')->with('success', 'Webhook removido.');
    }
}
