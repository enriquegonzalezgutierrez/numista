<?php

use App\Http\Controllers\TenantFileController;
use Illuminate\Support\Facades\Route;
use Numista\Collection\UI\Public\Controllers\AddressController;
use Numista\Collection\UI\Public\Controllers\Auth\AuthenticatedSessionController;
use Numista\Collection\UI\Public\Controllers\Auth\NewPasswordController;
use Numista\Collection\UI\Public\Controllers\Auth\PasswordResetLinkController;
use Numista\Collection\UI\Public\Controllers\Auth\RegisteredUserController;
use Numista\Collection\UI\Public\Controllers\CartController;
use Numista\Collection\UI\Public\Controllers\CheckoutController;
use Numista\Collection\UI\Public\Controllers\ContactSellerController;
use Numista\Collection\UI\Public\Controllers\LandingPageController;
use Numista\Collection\UI\Public\Controllers\MyAccountController;
use Numista\Collection\UI\Public\Controllers\OrderController;
use Numista\Collection\UI\Public\Controllers\ProfileController;
use Numista\Collection\UI\Public\Controllers\PublicItemController;

Route::get('/', LandingPageController::class)->name('landing');

Route::get('/marketplace', [PublicItemController::class, 'index'])->name('public.items.index');
Route::get('/items/{item:slug}', [PublicItemController::class, 'show'])->name('public.items.show');
Route::post('/items/{item:slug}/contact', ContactSellerController::class)->name('public.items.contact');

// THE FIX: Unified file routes
Route::get('/images/{image}', [TenantFileController::class, 'showImage'])->name('images.show');
Route::get('/tenant-files/{path}', [TenantFileController::class, 'showFile'])->where('path', '.*')->name('tenant.files');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::prefix('my-account')->name('my-account.')->group(function () {
        Route::get('/', [MyAccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [MyAccountController::class, 'orders'])->name('orders');
        Route::get('addresses/{address}/delete', [AddressController::class, 'confirmDestroy'])->name('addresses.confirmDestroy');
        Route::resource('addresses', AddressController::class)->except(['show']);
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{item}', [CartController::class, 'add'])->name('add');
    Route::post('/add/{item}/async', [CartController::class, 'addAsync'])->name('add.async');
    Route::patch('/update/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('remove');
});
