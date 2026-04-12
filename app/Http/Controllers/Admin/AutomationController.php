<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automation;
use App\Models\Product;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function index()
    {
        $automations = Automation::with('product')->latest()->get();
        $products    = Product::published()->get();
        return view('admin.automations.index', compact('automations', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'trigger'           => 'required|in:purchase_approved,purchase_cancelled,purchase_refunded,purchase_expired',
            'source'            => 'required|in:hotmart,cakto,wikify,any',
            'source_product_id' => 'nullable|string',
            'product_id'        => 'nullable|exists:products,id',
            'action'            => 'required|in:grant_access,revoke_access,send_email',
            'is_active'         => 'boolean',
        ]);

        Automation::create($data + ['is_active' => $request->boolean('is_active', true)]);

        return back()->with('success', 'Automação criada!');
    }

    public function update(Request $request, Automation $automation)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'trigger'           => 'required|in:purchase_approved,purchase_cancelled,purchase_refunded,purchase_expired',
            'source'            => 'required|in:hotmart,cakto,wikify,any',
            'source_product_id' => 'nullable|string',
            'product_id'        => 'nullable|exists:products,id',
            'action'            => 'required|in:grant_access,revoke_access,send_email',
            'is_active'         => 'boolean',
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
