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
        return view('admin.webhooks.index', compact('webhooks'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        CustomWebhook::create(['name' => $request->name]);
        return back()->with('success', 'Webhook criado!');
    }

    public function show(CustomWebhook $webhook)
    {
        // Renamed parameter to $webhook to match the resource 'webhooks' binding
        return view('admin.webhooks.show', ['customWebhook' => $webhook]);
    }

    public function update(Request $request, CustomWebhook $webhook)
    {
        $request->validate(['mapping' => 'nullable|array']);
        
        $webhook->update([
            'mapping' => $request->mapping
        ]);

        return back()->with('success', 'Mapeamento salvo com sucesso!');
    }

    public function destroy(CustomWebhook $webhook)
    {
        $webhook->delete();
        return redirect()->route('admin.webhooks.index')->with('success', 'Webhook removido.');
    }
}
