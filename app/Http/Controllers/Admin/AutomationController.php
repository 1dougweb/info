<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automation;
use App\Models\Product;
use App\Models\CustomWebhook;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function index()
    {
        $automations = Automation::with('product')->latest()->get();
        $products    = Product::published()->get();
        $webhooks    = CustomWebhook::all();
        $templates   = \App\Models\EmailTemplate::where('is_active', true)->get();
        
        return view('admin.automations.index', compact('automations', 'products', 'webhooks', 'templates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'trigger'           => 'required|string',
            'source'            => 'required|string',
            'source_product_id' => 'nullable|string',
            'product_id'        => 'nullable|exists:products,id',
            'action'            => 'required|in:grant_access,revoke_access,send_email,create_user',
            'is_active'         => 'boolean',
            'delay_seconds'     => 'nullable|integer|min:0',
            'action_config'     => 'nullable|array',
            'conditions'        => 'nullable|array',
        ]);

        Automation::create($data + ['is_active' => $request->boolean('is_active', true)]);

        return back()->with('success', 'Automação criada!');
    }

    public function update(Request $request, Automation $automation)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'trigger'           => 'required|string',
            'source'            => 'required|string',
            'source_product_id' => 'nullable|string',
            'product_id'        => 'nullable|exists:products,id',
            'action'            => 'required|in:grant_access,revoke_access,send_email,create_user',
            'is_active'         => 'boolean',
            'delay_minutes'     => 'nullable|integer|min:0',
            'action_config'     => 'nullable|array',
            'conditions'        => 'nullable|array',
        ]);

        $automation->update($data + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Automação atualizada!');
    }

    public function destroy(Automation $automation)
    {
        $automation->delete();
        return back()->with('success', 'Automação removida.');
    }

    public function toggle(Automation $automation)
    {
        $automation->update(['is_active' => !$automation->is_active]);
        return back()->with('success', 'Status alterado.');
    }
}
