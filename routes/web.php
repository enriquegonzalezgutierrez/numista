<?php

// routes/web.php

use App\Http\Controllers\TenantFileController;
use Illuminate\Support\Facades\Route;
use Numista\Collection\UI\Public\Controllers\Auth\AuthenticatedSessionController;
use Numista\Collection\UI\Public\Controllers\Auth\RegisteredUserController;
use Numista\Collection\UI\Public\Controllers\ContactSellerController;
use Numista\Collection\UI\Public\Controllers\HomeController;
use Numista\Collection\UI\Public\Controllers\PublicImageController;
use Numista\Collection\UI\Public\Controllers\PublicItemController;

// --- Public Marketplace Routes ---
Route::get('/', [PublicItemController::class, 'index'])->name('public.items.index');
Route::get('/items/{item:slug}', [PublicItemController::class, 'show'])->name('public.items.show');
Route::post('/items/{item:slug}/contact', ContactSellerController::class)->name('public.items.contact');

// --- File Serving Routes ---
Route::get('/item-images/{image}', [PublicImageController::class, 'show'])->name('public.images.show');
Route::get('/tenant-files/{path}', [TenantFileController::class, 'show'])->where('path', '.*')->name('tenant.files');

// --- Public Authentication Routes for Customers ---
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});
