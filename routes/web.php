<?php

use App\Http\Controllers\TenantFileController;
use Illuminate\Support\Facades\Route;
use Numista\Collection\UI\Public\Controllers\PublicImageController;
use Numista\Collection\UI\Public\Controllers\PublicItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- Public Marketplace Routes ---

// The root URL ('/') now directly shows the item marketplace.
Route::get('/', [PublicItemController::class, 'index'])->name('public.items.index');

// The item detail page remains the same.
Route::get('/items/{item:slug}', [PublicItemController::class, 'show'])->name('public.items.show');

// --- File Serving Routes ---

// Secure public route for item images.
Route::get('/item-images/{image}', [PublicImageController::class, 'show'])->name('public.images.show');

// Private route for tenant files (used by the admin panel).
Route::get('/tenant-files/{path}', [TenantFileController::class, 'show'])
    ->where('path', '.*')
    ->name('tenant.files');
