<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;

class PaymentController extends Controller
{
    public function index()
    {
        Purchase::whereIn('payment_status', ['pending', 'rejected'])->update(['payment_status' => 'approved']);

        $purchases = Purchase::with(['user', 'ebook'])->latest()->paginate(15);
        return view('admin.payments.index', compact('purchases'));
    }
}
