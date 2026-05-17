<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\Purchase;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    // Dipanggil saat klik Beli Sekarang
    public function checkout($bookId)
    {
        $book = \App\Models\Book::findOrFail($bookId);
        
        // Simpan data transaksi pending dulu
        $purchase = Purchase::create([
            'user_id' => auth()->id(),
            'book_id' => $bookId,
            'order_id' => 'ORDER-'.uniqid(),
            'amount' => $book->price,
            'status' => 'pending'
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $purchase->order_id,
                'gross_amount' => $book->price,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
            'item_details' => [[
                'id' => $book->id,
                'price' => $book->price,
                'quantity' => 1,
                'name' => $book->title
            ]]
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('payment.checkout', compact('snapToken', 'book'));
    }

    // Dipanggil otomatis oleh Midtrans setelah bayar
    public function notificationHandler(Request $request)
    {
        $notif = new Notification();

        if ($notif->transaction_status == 'settlement') {
            Purchase::where('order_id', $notif->order_id)
                ->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return response()->json(['status' => 'ok']);
    }
}