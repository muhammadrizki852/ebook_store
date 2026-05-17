<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EbookController extends Controller
{
    public function show(Ebook $ebook)
    {
        if ($ebook->status !== 'published') {
            abort(404);
        }

        $isPurchased = false;
        if (auth()->check()) {
            $isPurchased = $ebook->isPurchasedBy(auth()->user());
        }

        return view('ebooks.show', compact('ebook', 'isPurchased'));
    }

    public function readPdf(Ebook $ebook)
    {
        if ($ebook->status !== 'published') {
            abort(404);
        }

        $isFree = (float) $ebook->price <= 0;
        $isPurchased = auth()->check() && $ebook->isPurchasedBy(auth()->user());

        if (!$isFree && !$isPurchased) {
            abort(403);
        }

        if (!$ebook->file_path || !Storage::disk('public')->exists($ebook->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($ebook->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . addslashes($ebook->slug) . '.pdf"',
        ]);
    }
}
