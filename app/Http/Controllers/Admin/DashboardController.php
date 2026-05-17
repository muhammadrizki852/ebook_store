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
        Purchase::whereIn('payment_status', ['pending', 'rejected'])->update(['payment_status' => 'approved']);

        $totalUsers    = User::where('role', 'user')->count();
        $totalEbooks   = Ebook::published()->count();
        $publishedEbooks = $totalEbooks;
        $draftEbooks   = Ebook::where('status', 'draft')->count();
        $totalSales    = Purchase::where('payment_status', 'approved')->count();
        $totalRevenue  = Purchase::where('payment_status', 'approved')->sum('amount');
        $recentPurchases = Purchase::with(['user', 'ebook'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalEbooks',
            'totalSales',
            'totalRevenue',
            'publishedEbooks',
            'draftEbooks',
            'recentPurchases'
        ));
    }
}
