<?php

// routes/web.php

use App\Http\Controllers\TenantFileController;
use Illuminate\Support\Facades\Route;
use Numista\Collection\UI\Public\Controllers\ContactSellerController;
use Numista\Collection\UI\Public\Controllers\PublicImageController;
use Numista\Collection\UI\Public\Controllers\PublicItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public Marketplace Routes ---

Route::get('/', [PublicItemController::class, 'index'])->name('public.items.index');
Route::get('/items/{item:slug}', [PublicItemController::class, 'show'])->name('public.items.show');

// This route handles the contact form submission for a specific item.
Route::post('/items/{item:slug}/contact', ContactSellerController::class)->name('public.items.contact');
// ----------------------------

// --- File Serving Routes ---

Route::get('/item-images/{image}', [PublicImageController::class, 'show'])->name('public.images.show');
Route::get('/tenant-files/{path}', [TenantFileController::class, 'show'])
    ->where('path', '.*')
    ->name('tenant.files');
