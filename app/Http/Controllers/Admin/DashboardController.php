<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use App\Models\Purchase;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers    = User::where('role', 'user')->count();
        $totalEbooks   = Ebook::published()->count();
        $publishedEbooks = $totalEbooks;
        $draftEbooks   = Ebook::where('status', 'draft')->count();
        $totalSales    = Purchase::where('payment_status', 'approved')->count();
        $totalRevenue  = Purchase::where('payment_status', 'approved')->sum('amount');
        $pendingPayments = Purchase::where('payment_status', 'pending')->count();
        $recentPurchases = Purchase::with(['user', 'ebook'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalEbooks',
            'totalSales',
            'totalRevenue',
            'pendingPayments',
            'publishedEbooks',
            'draftEbooks',
            'recentPurchases'
        ));
    }
}
