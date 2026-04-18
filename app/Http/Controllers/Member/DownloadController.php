<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Download the product file securely.
     */
    public function download(string $slug): StreamedResponse
    {
        $user = Auth::user();
        $product = Product::where('slug', $slug)->where('status', 'published')->firstOrFail();

        // Check if user is enrolled
        if (!$user->isEnrolledIn($product)) {
            abort(403, 'Você não tem acesso a este download.');
        }

        $filePath = public_path($product->file_path);

        // Check if product has a file
        if (!$product->file_path || !file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return response()->download($filePath);
    }
}
