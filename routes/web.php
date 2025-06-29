<?php

use App\Http\Controllers\TenantFileController;
use Illuminate\Support\Facades\Route;
use Numista\Collection\UI\Public\Controllers\PublicImageController;
use Numista\Collection\UI\Public\Controllers\PublicItemController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/items', [PublicItemController::class, 'index'])->name('public.items.index');
Route::get('/items/{item:slug}', [PublicItemController::class, 'show'])->name('public.items.show');

Route::get('/item-images/{image}', [PublicImageController::class, 'show'])->name('public.images.show');

Route::get('/tenant-files/{path}', [TenantFileController::class, 'show'])
    ->where('path', '.*')
    ->name('tenant.files');
