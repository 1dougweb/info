<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        // Garantir que temos templates para cada trigger
        $triggers = [
            'purchase_approved' => 'Compra Aprovada',
            'billet_printed'    => 'Boleto Gerado',
            'pix_generated'     => 'Pix Gerado',
            'cart_abandonment'  => 'Abandono de Carrinho',
            'purchase_refused'  => 'Compra Recusada',
            'purchase_refunded' => 'Reembolso',
            'purchase_cancelled'=> 'Assinatura Cancelada',
        ];

        foreach ($triggers as $slug => $name) {
            EmailTemplate::firstOrCreate(['trigger' => $slug]);
        }

        $templates = EmailTemplate::all();
        return view('admin.email-templates.index', compact('templates', 'triggers'));
    }

    public function update(Request $request, EmailTemplate $template)
    {
        $data = $request->validate([
            'subject'   => 'required|string|max:255',
            'body'      => 'required|string',
            'is_active' => 'boolean',
        ]);

        $template->update($data + ['is_active' => $request->has('is_active')]);

        return back()->with('success', 'Modelo de e-mail atualizado!');
    }
}
