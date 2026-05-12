<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    private function formatTransactionDate($date): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $date = $date->copy()->timezone(config('app.timezone'));

        return $date->format('d') . ' ' . $months[(int) $date->format('n')] . ' ' . $date->format('Y, H.i') . ' WIB';
    }

    public function create(Ebook $ebook)
    {
        if ($ebook->status !== 'published') {
            abort(404);
        }

        if ($ebook->isPurchasedBy(auth()->user())) {
            return redirect()->route('library')->with('info', 'You already own this ebook.');
        }

        // Check for pending purchase
        $existingPurchase = Purchase::where('user_id', auth()->id())
            ->where('ebook_id', $ebook->id)
            ->where('payment_status', 'pending')
            ->first();

        return view('purchases.create', compact('ebook', 'existingPurchase'));
    }

    public function store(Request $request, Ebook $ebook)
    {
        if ($ebook->status !== 'published') {
            abort(404);
        }

        if ($ebook->isPurchasedBy(auth()->user())) {
            return redirect()->route('library')->with('info', 'You already own this ebook.');
        }

        $existingPurchase = Purchase::where('user_id', auth()->id())
            ->where('ebook_id', $ebook->id)
            ->whereIn('payment_status', ['pending', 'approved'])
            ->first();

        if ($existingPurchase) {
            if ($existingPurchase->payment_status === 'pending') {
                $existingPurchase->update([
                    'payment_status' => 'approved',
                    'notes' => trim(($existingPurchase->notes ? $existingPurchase->notes . "\n" : '') . 'Auto-approved when the user completed checkout.'),
                ]);

                return redirect()->route('library')->with('success', 'Purchase completed! The ebook is now available in your library.');
            }

            return redirect()->route('library')->with('info', 'You already own this ebook.');
        }

        $request->validate([
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        Purchase::create([
            'user_id'        => auth()->id(),
            'ebook_id'       => $ebook->id,
            'payment_proof'  => $path,
            'payment_status' => 'approved',
            'amount'         => $ebook->price,
            'notes'          => $request->notes,
        ]);

        return redirect()->route('library')->with('success', 'Purchase completed! The ebook is now available in your library.');
    }

    public function quickStore(Request $request, Ebook $ebook)
    {
        if ($ebook->status !== 'published') {
            abort(404);
        }

        $request->validate([
            'payment_method' => ['nullable', 'string', 'max:100'],
        ]);

        $existingPurchase = Purchase::where('user_id', auth()->id())
            ->where('ebook_id', $ebook->id)
            ->whereIn('payment_status', ['pending', 'approved'])
            ->first();

        if ($existingPurchase) {
            $wasPending = $existingPurchase->payment_status === 'pending';

            if ($existingPurchase->payment_status === 'pending') {
                $paymentMethod = $request->input('payment_method', 'SPA payment');

                $existingPurchase->update([
                    'payment_status' => 'approved',
                    'notes' => trim(($existingPurchase->notes ? $existingPurchase->notes . "\n" : '') . "Auto-approved from app payment page via {$paymentMethod}."),
                ]);
            }

            return response()->json([
                'message' => $wasPending
                    ? 'Purchase completed. The ebook is now available.'
                    : 'You already own this ebook.',
                'transaction_id' => 'TRX-' . $existingPurchase->created_at->format('Ymd') . '-' . str_pad((string) $existingPurchase->id, 3, '0', STR_PAD_LEFT),
                'status' => $existingPurchase->payment_status,
                'created_at' => $existingPurchase->created_at->toIso8601String(),
                'formatted_created_at' => $this->formatTransactionDate($existingPurchase->created_at),
            ]);
        }

        $paymentMethod = $request->input('payment_method', 'SPA payment');

        $purchase = Purchase::create([
            'user_id' => auth()->id(),
            'ebook_id' => $ebook->id,
            'payment_status' => 'approved',
            'amount' => $ebook->price,
            'notes' => "Auto-approved from app payment page via {$paymentMethod}.",
        ]);

        return response()->json([
            'message' => 'Purchase completed. The ebook is now available.',
            'transaction_id' => 'TRX-' . $purchase->created_at->format('Ymd') . '-' . str_pad((string) $purchase->id, 3, '0', STR_PAD_LEFT),
            'status' => $purchase->payment_status,
            'created_at' => $purchase->created_at->toIso8601String(),
            'formatted_created_at' => $this->formatTransactionDate($purchase->created_at),
        ], 201);
    }
}
