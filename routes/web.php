<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserAvatarController;
use App\Models\Ebook;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EbookController as AdminEbookController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\TransactionActivityController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/two-factor-challenge', [AuthController::class, 'showTwoFactorChallenge'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [AuthController::class, 'verifyTwoFactorChallenge'])->name('two-factor.verify');
    Route::post('/two-factor-challenge/resend', [AuthController::class, 'resendTwoFactorCode'])->name('two-factor.resend');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::get('/users/{user}/avatar', [UserAvatarController::class, 'show'])->name('users.avatar');


// Public routes
Route::get('/', function () {
    $publishedEbooks = collect();
    $topRecommendedSlugs = [
        'laut-bercerita',
        'atomic-habits',
        'psychology-of-money',
        'mindset',
        'clean-code',
        'sapiens',
    ];

    if (Schema::hasTable('ebooks')) {
        $publishedEbooks = Ebook::published()
            ->latest()
            ->get()
            ->sortBy(function (Ebook $ebook) use ($topRecommendedSlugs) {
                $position = array_search($ebook->slug, $topRecommendedSlugs, true);

                return $position === false ? count($topRecommendedSlugs) + $ebook->id : $position;
            })
            ->map(fn (Ebook $ebook) => [
                'id' => $ebook->slug,
                'title' => $ebook->title,
                'author' => $ebook->author,
                'description' => $ebook->description,
                'category' => $ebook->category,
                'cover' => $ebook->cover_url,
                'price' => (float) $ebook->price,
                'isFree' => (float) $ebook->price <= 0,
                'pdfUrl' => $ebook->file_path ? route('ebooks.read-pdf', $ebook) : null,
            ])
            ->values();
    }

    return view('spa', compact('publishedEbooks'));
})->name('home');
Route::get('/ebooks/{ebook:slug}', [EbookController::class, 'show'])->name('ebooks.show');
Route::get('/ebooks/{ebook:slug}/read-pdf', [EbookController::class, 'readPdf'])->name('ebooks.read-pdf');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/library', [LibraryController::class, 'index'])->name('library');
    Route::get('/library/download/{ebook}', [LibraryController::class, 'download'])->name('library.download');
    Route::get('/purchase/{ebook}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/{ebook}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{ebook:slug}/status', [PurchaseController::class, 'status'])->name('purchase.status');
    Route::post('/purchase/{ebook:slug}/quick', [PurchaseController::class, 'quickStore'])->name('purchase.quick-store');
    Route::post('/favorites/{ebook:slug}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{ebook:slug}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ebooks CRUD
    Route::resource('ebooks', AdminEbookController::class)->except(['show']);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Transaction Activities are generated automatically from purchases.
    Route::resource('transaction-activities', TransactionActivityController::class)->only(['index', 'show']);
});
