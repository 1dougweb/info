<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebhookConfig;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;

class WebhookConfigController extends Controller
{
    public function index()
    {
        $configs = collect(['hotmart', 'cakto', 'wikify'])->map(function ($source) {
            return WebhookConfig::firstOrCreate(['source' => $source], ['is_active' => true]);
        });

        $events = WebhookEvent::latest()->limit(50)->get();
        $webhookUrl = url('/webhooks/{source}');

        return view('admin.webhooks.index', compact('configs', 'events', 'webhookUrl'));
    }

    public function update(Request $request, string $source)
    {
        $data = $request->validate([
            'secret'    => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        WebhookConfig::updateOrCreate(
            ['source' => $source],
            $data + ['is_active' => $request->boolean('is_active')]
        );

        return back()->with('success', "Configuração {$source} atualizada!");
    }
}
